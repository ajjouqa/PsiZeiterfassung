<?php
// app/Services/XmppAuthService.php

namespace App\Services;

use App\Models\DailyStatus;
use App\Models\XmppUserMapping;
use App\Models\XmppPresenceLog;
use App\Models\XmppDailyPresenceSummary;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class XmppAuthService
{
    protected $xmppService;

    public function __construct(XMPPService $xmppService)
    {
        $this->xmppService = $xmppService;
    }

    /**
     * Create or update XMPP user mapping
     */
    public function registerUser($userType, $userId, $userData)
    {
        // Create a username based on user type and user data
        // For example: azubi_john_doe
        $username = strtolower($userType . '_' . str_replace(' ', '_', $userData['name']));

        // Generate a password or use the one from userData
        $password = $userData['xmpp_password'] ?? str_random(12);

        // Check if mapping already exists
        $mapping = XmppUserMapping::where('user_type', $userType)
            ->where('user_id', $userId)
            ->first();

        if (!$mapping) {
            $mapping = new XmppUserMapping([
                'user_type' => $userType,
                'user_id' => $userId,
                'xmpp_username' => $username,
                'xmpp_password' => $password,
            ]);
        }

        // Update password if provided
        if (isset($userData['xmpp_password'])) {
            $mapping->xmpp_password = $userData['xmpp_password'];
        }

        $mapping->save();

        // Now create or update the user in OpenFire
        $this->syncUserWithOpenFire($mapping);

        return $mapping;
    }

    /**
     * Sync user with OpenFire database
     */
    public function syncUserWithOpenFire($mapping)
    {
        try {
            // Check if user exists in ofUser table
            $ofUser = DB::table('ofUser')
                ->where('username', $mapping->xmpp_username)
                ->first();

            // Generate SCRAM-SHA-1 password values
            $password = $mapping->xmpp_password;
            $salt = random_bytes(16);
            $iterations = 4096; // Standard value for OpenFire

            // Derive the keys using PBKDF2
            $saltedPassword = hash_pbkdf2('sha1', $password, $salt, $iterations, 20, true);
            $clientKey = hash_hmac('sha1', 'Client Key', $saltedPassword, true);
            $storedKey = sha1($clientKey, true);
            $serverKey = hash_hmac('sha1', 'Server Key', $saltedPassword, true);

            // Base64 encode values for storage
            $saltEncoded = base64_encode($salt);
            $storedKeyEncoded = base64_encode($storedKey);
            $serverKeyEncoded = base64_encode($serverKey);

            $userData = [
                'plainPassword' => null, // Don't store plain password
                'name' => $mapping->user->name ?? $mapping->xmpp_username,
                'email' => $mapping->user->email ?? '',
                'modificationDate' => now()->getTimestamp() * 1000,
                
                'storedKey' => $storedKeyEncoded,
                'serverKey' => $serverKeyEncoded,
                'salt' => $saltEncoded,
                'iterations' => $iterations
            ];

            if ($ofUser) {
                // Update existing user
                DB::table('ofUser')
                    ->where('username', $mapping->xmpp_username)
                    ->update($userData);

                // Store OpenFire user ID if needed
                $mapping->of_user_id = $ofUser->username;
                $mapping->save();
            } else {
                // Create new user
                $userData['username'] = $mapping->xmpp_username;
                $userData['creationDate'] = now()->getTimestamp() * 1000;

                DB::table('ofUser')->insert($userData);

                // Store OpenFire user ID
                $mapping->of_user_id = $mapping->xmpp_username;
                $mapping->save();
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error syncing user with OpenFire: ' . $e->getMessage());
            return false;
        }
    }
    /**
     * Authenticate a user with XMPP server
     */
    public function authenticateUser($userType, $userId)
    {
        $mapping = XmppUserMapping::where('user_type', $userType)
            ->where('user_id', $userId)
            ->where('is_active', 1)
            ->first();

        if (!$mapping) {
            Log::error("No XMPP mapping found for {$userType} user #{$userId}");
            return null;
        }

        Log::info("Login Event Triggered for Employee ID: {$userId}");

        // Configure the XMPP service with the user's credentials
        $xmppService = new XMPPService(
            config('xmpp.server'),
            $mapping->xmpp_username,
            $mapping->xmpp_password,
            config('xmpp.resource', 'globe')
        );

        // Try to authenticate
        $connection = $xmppService->authenticate();

        if ($connection) {
            Log::info('XMPP authentication successful.');

            // Set presence to online
            $xmppService->setPresence($connection, 'Online');

            // Update last login timestamp
            $mapping->last_login = now();
            $mapping->save();

            // Record login presence event
            $presenceLog = $this->recordPresenceEvent($mapping, 'login', 'available');

            // Now we can safely log the presence log ID
            Log::debug("Authentication successful, recording presence for user {$mapping->xmpp_username}");
            Log::debug("Presence log created: " . ($presenceLog ? $presenceLog->id : 'FAILED'));

            // Update daily presence summary for login
            if ($presenceLog) {
                $this->updateDailyPresenceSummaryLogin($mapping, $presenceLog->timestamp);
            }

            return [
                'connection' => $connection,
                'xmpp_service' => $xmppService,
                'mapping' => $mapping
            ];
        } else {
            Log::error('XMPP authentication failed.');
            Log::debug("updatePresence called with: userType={$userType}, userId={$userId}, connection=null");
        }

        return null;
    }


    /**
     * Get user by OpenFire username
     */
    public function getUserByXmppUsername($xmppUsername)
    {
        $mapping = XmppUserMapping::where('xmpp_username', $xmppUsername)
            ->where('is_active', true)
            ->first();

        if (!$mapping) {
            return null;
        }

        return [
            'user_type' => $mapping->user_type,
            'user_id' => $mapping->user_id,
            'user' => $mapping->user
        ];
    }

    /**
     * Update user presence status and record event
     */
    public function updatePresence($userType, $userId, $connection, $status = null, $show = null)
    {
        $mapping = XmppUserMapping::where('user_type', $userType)
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->first();

        if (!$mapping || !$connection) {
            return false;
        }

        // Configure the XMPP service with the user's credentials
        $xmppService = new XMPPService(
            config('xmpp.server'),
            $mapping->xmpp_username,
            $mapping->xmpp_password,
            config('xmpp.resource', 'globe')
        );

        // Set presence
        $result = $xmppService->setPresence($connection, $status, $show);

        if ($result) {
            // Record presence event
            $presenceLog = $this->recordPresenceEvent($mapping, 'status_change', $show ?? 'available', $status);

            // If changing to unavailable status, update daily summary
            if ($show === 'unavailable' && $presenceLog) {
                $this->updateDailyPresenceSummaryLogout($mapping, $presenceLog->timestamp);
            }

            // Update mapping status
            $mapping->current_presence = $show ?? 'available';
            $mapping->current_status = $status;
            $mapping->presence_updated_at = now();
            $mapping->save();
        }

        return $result;
    }

    /**
     * Record user logout
     */
    public function logoutUser($userType, $userId, $connection)
    {
        $mapping = XmppUserMapping::where('user_type', $userType)
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->first();

        if (!$mapping || !$connection) {
            return false;
        }

        Log::info("Logout Event Triggered for Employee ID: {$userId}");

        // Configure the XMPP service
        $xmppService = new XMPPService(
            config('xmpp.server'),
            $mapping->xmpp_username,
            $mapping->xmpp_password,
            config('xmpp.resource', 'laravel')
        );

        // Record logout event before disconnecting
        $presenceLog = $this->recordPresenceEvent($mapping, 'logout', 'unavailable');

        // Update daily presence summary for logout
        if ($presenceLog) {
            $this->updateDailyPresenceSummaryLogout($mapping, $presenceLog->timestamp);
        }

        // Update mapping status
        $mapping->current_presence = 'unavailable';
        $mapping->current_status = null;
        $mapping->last_logout = now();
        $mapping->presence_updated_at = now();
        $mapping->save();

        // Disconnect from XMPP server
        $xmppService->disconnect($connection);

        return true;
    }


    /**
     * Record presence event in log
     */
    public function recordPresenceEvent($mapping, $eventType, $presence, $status = null)
    {
        try {
            $log = XmppPresenceLog::create([
                'user_type' => $mapping->user_type,
                'user_id' => $mapping->user_id,
                'xmpp_username' => $mapping->xmpp_username,
                'event_type' => $eventType,
                'presence' => $presence,
                'status' => $status,
                'timestamp' => now(),
                'resource' => config('xmpp.resource', 'globe'),
                'ip_address' => request()->ip()
            ]);

            Log::info("XMPP presence recorded: {$mapping->xmpp_username} - {$eventType} - {$presence}");
            return $log;
        } catch (\Exception $e) {
            Log::error("Failed to record XMPP presence: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user presence logs
     */
    public function getUserPresenceLogs($userType, $userId, $startDate = null, $endDate = null)
    {
        $query = XmppPresenceLog::where('user_type', $userType)
            ->where('user_id', $userId)
            ->orderBy('timestamp', 'desc');

        if ($startDate) {
            $query->where('timestamp', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('timestamp', '<=', $endDate);
        }

        return $query->get();
    }

    /**
     * Calculate user's total online time
     */
    public function calculateOnlineTime($userType, $userId, $startDate = null, $endDate = null)
    {
        $logs = $this->getUserPresenceLogs($userType, $userId, $startDate, $endDate);

        $totalSeconds = 0;
        $loginTime = null;

        foreach ($logs as $log) {
            if ($log->event_type === 'login' || ($log->event_type === 'status_change' && $log->presence !== 'unavailable')) {
                if (!$loginTime) {
                    $loginTime = $log->timestamp;
                }
            } elseif ($log->event_type === 'logout' || ($log->event_type === 'status_change' && $log->presence === 'unavailable')) {
                if ($loginTime) {
                    $totalSeconds += $log->timestamp->diffInSeconds($loginTime);
                    $loginTime = null;
                }
            }
        }

        // If there's no logout event, use current time
        if ($loginTime) {
            $totalSeconds += now()->diffInSeconds($loginTime);
        }

        return [
            'seconds' => $totalSeconds,
            'minutes' => round($totalSeconds / 60, 2),
            'hours' => round($totalSeconds / 3600, 2),
            'formatted' => $this->formatTimeInterval($totalSeconds)
        ];
    }

    /**
     * Format time interval in human-readable format
     */
    protected function formatTimeInterval($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    /**
     * Update daily presence summary when user logs in
     */
    public function updateDailyPresenceSummaryLogin($mapping, $loginTime)
    {
        $date = $loginTime->toDateString();

        // Get or create summary for today
        $summary = XmppDailyPresenceSummary::firstOrNew([
            'user_type' => $mapping->user_type,
            'user_id' => $mapping->user_id,
            'date' => $date
        ]);

        

        // Set initial values if new record
        if (!$summary->exists) {
            $summary->xmpp_username = $mapping->xmpp_username;
            $summary->session_count = 0;
            $summary->total_seconds = 0;
            $summary->formatted_time = '00:00:00';
        }


        // Update session count and first login if needed
        $summary->session_count += 1;

        if (!$summary->first_login || $loginTime->lt($summary->first_login)) {
            $summary->first_login = $loginTime;
        }

        $summary->save();

        $daily_status = DailyStatus::firstOrNew([
            'daily_summary_id' => $summary->id,
            'status' => 'working',
            'notes' => '',
        ]);

        
        if(!$daily_status ->exists) {
            $daily_status->save();
        }

        return $summary;
    }

    /**
     * Update daily presence summary when user logs out
     */
    protected function updateDailyPresenceSummaryLogout($mapping, $logoutTime)
    {
        // Find user's last login event
        $lastLogin = XmppPresenceLog::where('user_type', $mapping->user_type)
            ->where('user_id', $mapping->user_id)
            ->where(function ($query) {
                $query->where('event_type', 'login')
                    ->orWhere(function ($q) {
                        $q->where('event_type', 'status_change')
                            ->where('presence', '!=', 'unavailable');
                    });
            })
            ->where('timestamp', '<', $logoutTime)
            ->orderBy('timestamp', 'desc')
            ->first();

        if (!$lastLogin) {
            Log::warning("No login event found before logout for {$mapping->xmpp_username}");
            return false;
        }

        $loginTime = $lastLogin->timestamp;
        $loginDate = $loginTime->toDateString();
        $logoutDate = $logoutTime->toDateString();

        // Calculate time spent online
        $sessionSeconds = $logoutTime->diffInSeconds($loginTime);

        // Handle case where login and logout are on the same day
        if ($loginDate === $logoutDate) {
            // Update single day record
            $this->updateSingleDaySummary($mapping, $loginDate, $sessionSeconds, $loginTime, $logoutTime);
        } else {
            // Split time between days
            $this->splitSessionBetweenDays($mapping, $loginTime, $logoutTime);
        }

        return true;
    }

    /**
     * Update a single day's presence summary
     */
    protected function updateSingleDaySummary($mapping, $date, $seconds, $loginTime = null, $logoutTime = null)
    {
        // Get or create summary for the date
        $summary = XmppDailyPresenceSummary::firstOrNew([
            'user_type' => $mapping->user_type,
            'user_id' => $mapping->user_id,
            'date' => $date
        ]);

        // Set initial values if new record
        if (!$summary->exists) {
            $summary->xmpp_username = $mapping->xmpp_username;
            $summary->session_count = 0;
            $summary->total_seconds = 0;
        }

        // Update totals
        $summary->total_seconds += $seconds;
        $summary->formatted_time = $this->formatTimeInterval($summary->total_seconds);

        // Update first_login if provided and it's earlier
        if ($loginTime && (!$summary->first_login || $loginTime->lt($summary->first_login))) {
            $summary->first_login = $loginTime;
        }

        // Update last_logout if provided and it's later
        if ($logoutTime && (!$summary->last_logout || $logoutTime->gt($summary->last_logout))) {
            $summary->last_logout = $logoutTime;
        }

        $summary->save();

        return $summary;
    }

    /**
     * Split a session that spans multiple days
     */
    protected function splitSessionBetweenDays($mapping, $loginTime, $logoutTime)
    {
        $currentDate = Carbon::parse($loginTime);
        $endDate = Carbon::parse($logoutTime)->startOfDay();

        // Process first day (partial)
        $dayEnd = Carbon::parse($loginTime)->endOfDay();
        $secondsFirstDay = $dayEnd->diffInSeconds($loginTime);
        $this->updateSingleDaySummary($mapping, $currentDate->toDateString(), $secondsFirstDay, $loginTime, $dayEnd);

        // Process middle days (if any)
        $currentDate = $currentDate->addDay()->startOfDay();

        while ($currentDate->lt($endDate)) {
            // Full day (86400 seconds)
            $this->updateSingleDaySummary(
                $mapping,
                $currentDate->toDateString(),
                86400,
                $currentDate->copy()->startOfDay(),
                $currentDate->copy()->endOfDay()
            );

            $currentDate->addDay();
        }

        // Process last day (partial)
        if ($endDate->lt($logoutTime)) {
            $secondsLastDay = $logoutTime->diffInSeconds($endDate);
            $this->updateSingleDaySummary($mapping, $endDate->toDateString(), $secondsLastDay, $endDate, $logoutTime);
        }

        return true;
    }

    /**
     * Get daily presence summaries for a user
     */
    public function getDailyPresenceSummaries($userType, $userId, $startDate = null, $endDate = null)
    {
        $query = XmppDailyPresenceSummary::where('user_type', $userType)
            ->with('status')
            ->where('user_id', $userId)
            ->orderBy('date', 'asc');

        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }

        return $query->get();
    }


    public function getUserMapping($userType, $userId)
    {
        return XmppUserMapping::where('user_type', $userType)
            ->where('user_id', $userId)
            ->where('is_active', 1)
            ->first();
    }

    /**
     * Process any hanging sessions for a user
     * This should be run periodically (e.g., by cron) to handle cases where logout wasn't properly recorded
     */
    /**
     * Process any hanging sessions for a user
     * This should be run periodically (e.g., by cron) to handle cases where logout wasn't properly recorded
     */
    public function processHangingSessions($userType = null, $userId = null)
    {
        // Build query to find users with active sessions
        $query = XmppUserMapping::where('current_presence', 'available')
            ->where('is_active', true);

        if ($userType) {
            $query->where('user_type', $userType);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $activeUsers = $query->get();
        $processed = 0;

        foreach ($activeUsers as $mapping) {
            // Find last presence event
            $lastEvent = XmppPresenceLog::where('user_type', $mapping->user_type)
                ->where('user_id', $mapping->user_id)
                ->orderBy('timestamp', 'desc')
                ->first();

            if (!$lastEvent) {
                continue;
            }

            // If last event is older than threshold (e.g., 15 minutes), consider it a hanging session
            $threshold = config('xmpp.hanging_session_threshold', 15); // minutes

            if ($lastEvent->timestamp->diffInMinutes(now()) > $threshold) {
                // Record a forced logout event
                $logoutTime = now();
                $presenceLog = $this->recordPresenceEvent($mapping, 'force_logout', 'unavailable', 'Hanging session closed');

                // Update daily presence summary
                if ($presenceLog) {
                    $this->updateDailyPresenceSummaryLogout($mapping, $logoutTime);
                }

                // Update mapping status
                $mapping->current_presence = 'unavailable';
                $mapping->current_status = null;
                $mapping->last_logout = $logoutTime;
                $mapping->presence_updated_at = $logoutTime;
                $mapping->save();

                $processed++;

                Log::info("Processed hanging session for {$mapping->xmpp_username} (inactive for {$lastEvent->timestamp->diffInMinutes(now())} minutes)");
            }
        }

        return $processed;
    }
}