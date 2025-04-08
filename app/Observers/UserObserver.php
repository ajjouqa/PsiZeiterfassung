<?php
// app/Observers/UserObserver.php

namespace App\Observers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserObserver
{
    /**
     * Handle the user "created" event.
     *
     * @param  mixed  $user
     * @return void
     */
    public function created($user)
    {
        // Determine user type and create a username for OpenFire
        $userType = $this->getUserType($user);

        if (!$userType) {
            return; // Not a user type we want to add to OpenFire
        }


        $xmppUsername = explode('@', $user->email)[0];

        // Generate a random password for OpenFire or use a predefined one
        $xmppPassword = $user->xmpp_password ?? Str::random(12);

        // Store XMPP credentials in our mapping table
        $this->createXmppMapping($user, $userType, $xmppUsername, $xmppPassword);

        // Add user directly to OpenFire tables
        $this->createOpenFireUser($xmppUsername, $xmppPassword, $user);
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  mixed  $user
     * @return void
     */
    public function updated($user)
    {
        // Get the XMPP mapping for this user
        $userType = $this->getUserType($user);

        if (!$userType) {
            return;
        }

        $mapping = DB::table('xmpp_user_mappings')
            ->where('user_type', $userType)
            ->where('user_id', $user->id)
            ->first();

        if (!$mapping) {
            $this->created($user);
            return;
        }

        // Update OpenFire user details if name or email changed
        DB::table('ofUser')
            ->where('username', $mapping->xmpp_username)
            ->update([
                'name' => $user->name ?? $mapping->xmpp_username,
                'email' => $user->email ?? '',
                'modificationDate' => now()->getTimestamp() * 1000, // OpenFire uses milliseconds
            ]);

        // If password was changed and we track that
        if (isset($user->xmpp_password) && $user->xmpp_password !== $mapping->xmpp_password) {
            // Update password in OpenFire
            DB::table('ofUser')
                ->where('username', $mapping->xmpp_username)
                ->update([
                    'plainPassword' => $user->xmpp_password,
                    'encryptedPassword' => '',  // Let OpenFire encrypt it
                ]);

            // Update our mapping
            DB::table('xmpp_user_mappings')
                ->where('id', $mapping->id)
                ->update([
                    'xmpp_password' => $user->xmpp_password
                ]);
        }
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  mixed  $user
     * @return void
     */
    public function deleted($user)
    {
        $userType = $this->getUserType($user);

        if (!$userType) {
            return;
        }

        // Get the XMPP mapping
        $mapping = DB::table('xmpp_user_mappings')
            ->where('user_type', $userType)
            ->where('user_id', $user->id)
            ->first();

        if (!$mapping) {
            return;
        }

        // Option 1: Delete the user from OpenFire
        DB::table('ofUser')
            ->where('username', $mapping->xmpp_username)
            ->delete();

        // Option 2: Or just deactivate in our mapping
        DB::table('xmpp_user_mappings')
            ->where('id', $mapping->id)
            ->update([
                'is_active' => false
            ]);
    }

    /**
     * Determine the user type of a model
     */
    protected function getUserType($user)
    {
        $class = get_class($user);

        if (strpos($class, 'Admin') !== false) {
            return 'admin';
        } elseif (strpos($class, 'Azubi') !== false) {
            return 'azubi';
        } elseif ($class === \App\Models\User::class) {
            return 'mitarbeiter';
        }

        return null;
    }

    /**
     * Create XMPP user mapping record
     */
    protected function createXmppMapping($user, $userType, $xmppUsername, $xmppPassword)
    {
        DB::table('xmpp_user_mappings')->insert([
            'xmpp_username' => $xmppUsername,
            'xmpp_password' => $xmppPassword,
            'user_type' => $userType,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Create a user in OpenFire database tables
     */
    protected function createOpenFireUser($username, $password, $user)
    {
        $now = now()->getTimestamp() * 1000; // OpenFire uses milliseconds



        $salt = random_bytes(16);
        $iterations = 4096;
        $saltedPassword = hash_pbkdf2('sha1', $password, $salt, $iterations, 20, true);

        $clientKey = hash_hmac('sha1', 'Client Key', $saltedPassword, true);
        $storedKey = sha1($clientKey, true);
        $serverKey = hash_hmac('sha1', 'Server Key', $saltedPassword, true);

        $saltEncoded = base64_encode($salt);
        $storedKeyEncoded = base64_encode($storedKey);
        $serverKeyEncoded = base64_encode($serverKey);


        // Insert into ofUser table
        DB::table('ofUser')->insert([
            'username' => $username,
            'plainPassword' => '',
            'encryptedPassword' => '', // Let OpenFire encrypt it on next login
            'name' => $user->name ?? $username,
            'email' => $user->email ?? '',
            'creationDate' => $now,
            'modificationDate' => $now,
            'storedKey' => $storedKeyEncoded,
            'serverKey' => $serverKeyEncoded,
            'salt' => $saltEncoded,
            'iterations' => $iterations
        ]);


        if (config('xmpp.default_group')) {
            // First check if group exists
            $group = DB::table('ofGroup')
                ->where('groupName', config('xmpp.default_group'))
                ->first();

            if (!$group) {
                // Create the group
                DB::table('ofGroup')->insert([
                    'groupName' => config('xmpp.default_group'),
                    'description' => 'Auto-created default group'
                ]);
            }

            // Add user to group
            DB::table('ofGroupUser')->insert([
                'groupName' => config('xmpp.default_group'),
                'username' => $username,
                'administrator' => 0
            ]);
        }
    }
}