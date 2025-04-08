<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;

class XMPPService
{
    protected $server;
    protected $username;
    protected $password;
    protected $resource;
    protected $port;
    protected $timeout;

    public function __construct($server , $username , $password, $resource , $port = 5222, $timeout = 5)
    {
        $this->server = $server;
        $this->username = $username;
        $this->password = $password;
        $this->resource = $resource;
        $this->port = 5222;
        $this->timeout = $timeout;
    }

    protected function openConnection()
    {

        Log::info("Opening XMPP connection to {$this->server}:{$this->port}");
        $connection = @fsockopen($this->server, $this->port, $errno, $errstr, $this->timeout);
        if (!$connection) {
            Log::error("XMPP connection failed: $errstr ($errno)");
            return false;
        }
        
        
        stream_set_timeout($connection, $this->timeout);
        return $connection;
    }

    protected function sendXML($connection, $xml)
    {
        Log::debug("XMPP sending: " . htmlspecialchars($xml));
        return fwrite($connection, $xml);
    }

    protected function readResponse($connection)
    {
        $response = '';
        $startTime = time();
        
        while (true) {
            // Check for timeout
            if (time() - $startTime > $this->timeout) {
                Log::error("XMPP response timeout");
                return false;
            }
            
            // Read available data
            $buffer = fread($connection, 4096);
            if ($buffer === false || feof($connection)) {
                Log::error("XMPP connection closed unexpectedly");
                return false;
            }
            
            $response .= $buffer;
            
            // Check if we have received complete data
            if (!empty($response) && (
                strpos($response, '</stream:features>') !== false || 
                strpos($response, '<success') !== false || 
                strpos($response, '<failure') !== false ||
                strpos($response, '</iq>') !== false ||
                strpos($response, '</presence>') !== false ||
                strpos($response, '</message>') !== false ||
                strpos($response, '</stream:stream>') !== false
            )) {
                Log::debug("XMPP received: " . htmlspecialchars($response));
                return $response;
            }
            
            // Small pause to prevent CPU hogging
            usleep(100000); // 100ms
        }
    }

    // Authenticate the user with SASL PLAIN
    public function authenticate()
    {
        $connection = $this->openConnection();
        if (!$connection) {
            return null;
        }

        // Send initial stream request
        Log::info("Sending initial XMPP stream header");
        $this->sendXML($connection, '<stream:stream xmlns:stream="http://etherx.jabber.org/streams" version="1.0" xmlns="jabber:client" to="' . config('xmpp.domain', $this->server) . '" xml:lang="en" xmlns:xml="http://www.w3.org/XML/1998/namespace">');
        
        $response = $this->readResponse($connection);
        if ($response === false) {
            Log::error("Failed to receive server features");
            fclose($connection);
            return null;
        }


        // Use SASL PLAIN authentication
        Log::info("Attempting SASL PLAIN authentication");
        $auth = base64_encode("\0" . $this->username . "\0" . $this->password);
        
        $this->sendXML($connection, '<auth xmlns="urn:ietf:params:xml:ns:xmpp-sasl" mechanism="PLAIN">' . $auth . '</auth>');
        
        $authResponse = $this->readResponse($connection);
        if ($authResponse === false) {
            Log::error("Failed to receive authentication response");
            fclose($connection);
            return null;
        }

        // Check for successful authentication
        if (strpos($authResponse, '<success') !== false) {
            Log::info("Authentication successful, restarting stream");
            
            // Restart stream after successful authentication
            $this->sendXML($connection, '<stream:stream xmlns:stream="http://etherx.jabber.org/streams" version="1.0" xmlns="jabber:client" to="' . config('xmpp.domain', $this->server) . '" xml:lang="en" xmlns:xml="http://www.w3.org/XML/1998/namespace">'); 
            
            $response = $this->readResponse($connection);
            if ($response === false) {
                Log::error("Failed to restart stream after authentication");
                fclose($connection);
                return null;
            }

            // Bind resource
            Log::info("Binding resource: {$this->resource}");
            $this->sendXML($connection, '<iq type="set" id="bind_1"><bind xmlns="urn:ietf:params:xml:ns:xmpp-bind"><resource>' . $this->resource . '</resource></bind></iq>');
            
            $bindResponse = $this->readResponse($connection);
            if ($bindResponse === false || strpos($bindResponse, 'type="result"') === false) {
                Log::error("Resource binding failed");
                fclose($connection);
                return null;
            }

            // Start session
            Log::info("Establishing session");
            $this->sendXML($connection, '<iq type="set" id="session_1"><session xmlns="urn:ietf:params:xml:ns:xmpp-session"/></iq>');
            
            $sessionResponse = $this->readResponse($connection);
            if ($sessionResponse === false || strpos($sessionResponse, 'type="result"') === false) {
                Log::error("Session establishment failed");
                fclose($connection);
                return null;
            }

            Log::info("XMPP connection and authentication successful");
            return $connection;
        }

        // Authentication failed
        Log::error("Authentication failed: " . htmlspecialchars($authResponse));
        fclose($connection);
        return null;
    }

    // Get roster (contact list)
    public function getRoster($connection)
    {
        if (!$connection) {
            Log::error("Cannot get roster: No active connection");
            return [];
        }

        Log::info("Requesting roster");
        $xml = '<iq from="' . $this->username . '@' . $this->server . '/' . $this->resource . '" id="roster_1" type="get"><query xmlns="jabber:iq:roster"/></iq>';
        $this->sendXML($connection, $xml);
        
        $response = $this->readResponse($connection);
        if ($response === false) {
            Log::error("Failed to receive roster response");
            return [];
        }

        // Parse the roster response
        try {
            $users = [];
            $xmlData = simplexml_load_string($response);
            
            if ($xmlData && isset($xmlData->query)) {
                foreach ($xmlData->query->item as $item) {
                    $jid = (string)$item['jid'];
                    $name = (string)$item['name'] ?: $jid;
                    $subscription = (string)$item['subscription'];
                    
                    $users[] = [
                        'jid' => $jid,
                        'name' => $name,
                        'subscription' => $subscription
                    ];
                }
            }
            
            return $users;
        } catch (\Exception $e) {
            Log::error("Error parsing roster: " . $e->getMessage());
            return [];
        }
    }

    // Send a message to a user
    public function sendMessage($connection, $to, $message)
    {
        if (!$connection) {
            Log::error("Cannot send message: No active connection");
            return false;
        }

        $id = 'msg_' . time();
        $from = $this->username . '@' . $this->server . '/' . $this->resource;
        
        $xml = '<message from="' . $from . '" to="' . $to . '" type="chat" id="' . $id . '">
            <body>' . htmlspecialchars($message) . '</body>
        </message>';
        
        Log::info("Sending message to $to");
        $result = $this->sendXML($connection, $xml);
        
        return $result !== false;
    }

    // Listen for incoming messages (non-blocking, returns one message at a time)
    public function checkForMessages($connection)
    {
        if (!$connection) {
            Log::error("Cannot check messages: No active connection");
            return null;
        }

        // Check if there's data waiting
        $read = [$connection];
        $write = null;
        $except = null;
        
        if (stream_select($read, $write, $except, 0, 100000)) { // 0.1 second timeout
            $response = $this->readResponse($connection);
            
            if ($response && strpos($response, '<message') !== false) {
                try {
                    $xml = simplexml_load_string($response);
                    if ($xml && $xml->getName() == 'message' && $xml->body) {
                        return [
                            'from' => (string)$xml['from'],
                            'to' => (string)$xml['to'],
                            'type' => (string)$xml['type'],
                            'id' => (string)$xml['id'],
                            'body' => (string)$xml->body
                        ];
                    }
                } catch (\Exception $e) {
                    Log::error("Error parsing message: " . $e->getMessage());
                }
            }
        }
        
        return null;
    }

    // Set presence/status
    public function setPresence($connection, $status = null, $show = null)
    {
        if (!$connection) {
            Log::error("Cannot set presence: No active connection");
            return false;
        }

        $xml = '<presence>';
        if ($show) {
            $xml .= '<show>' . $show . '</show>'; // away, chat, dnd, xa
        }
        if ($status) {
            $xml .= '<status>' . htmlspecialchars($status) . '</status>';
        }
        $xml .= '</presence>';
        
        Log::info("Setting presence: " . ($show ?: 'online') . ($status ? " - $status" : ""));
        $result = $this->sendXML($connection, $xml);
        
        return $result !== false;
    }

    public function ping($connection)
    {
        if (!$connection) {
            Log::error("Cannot ping: No active connection");
            return false;
        }

        $id = 'ping_' . time();
        $xml = '<iq from="' . $this->username . '@' . $this->server . '/' . $this->resource . '" to="' . $this->server . '" id="' . $id . '" type="get"><ping xmlns="urn:xmpp:ping"/></iq>';
        
        Log::debug("Sending ping");
        $this->sendXML($connection, $xml);
        
        $response = $this->readResponse($connection);
        return $response !== false && strpos($response, 'type="result"') !== false;
    }


    public function disconnect($connection)
    {
        if (!$connection) {
            return;
        }

        Log::info("Disconnecting XMPP connection");
        $this->sendXML($connection, '</stream:stream>');
        
        // Wait for server's closing stream tag
        $this->readResponse($connection);
        
        fclose($connection);
    }
}