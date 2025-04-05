<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;

class XMPPService
{
    protected $server;
    protected $username;
    protected $password;
    protected $resource;
    protected $connection;

    public function __construct($server, $username, $password, $resource = 'globe')
    {
        $this->server = $server;
        $this->username = $username;
        $this->password = $password;
        $this->resource = $resource;
    }

    protected function openConnection()
    {
        $connection = fsockopen($this->server, 5222, $errno, $errstr);
        if (!$connection) {
            Log::error("XMPP connection failed: $errstr ($errno)");
            return false;
        }
        return $connection;
    }

    protected function sendXML($connection, $xml)
    {
        fwrite($connection, $xml);
    }

    protected function recvXML($connection, $size = 4096)
    {
        $xml = '';
        while ($size < 0 || strlen($xml) < $size) {
            $xml .= fread($connection, abs($size));
        }
        return $xml;
    }

    // Authenticate the user with SASL PLAIN
    public function authenticate()
    {
        $connection = $this->openConnection();
        if (!$connection) {
            return null;
        }

        // Send initial stream request
        $this->sendXML($connection, '<stream:stream xmlns:stream="http://etherx.jabber.org/streams" version="1.0" xmlns="jabber:client" to="' . $this->server . '" xml:lang="en" xmlns:xml="http://www.w3.org/XML/1998/namespace">');
        $data = $this->recvXML($connection);

        // Use SASL PLAIN authentication
        $auth = base64_encode("\0" . $this->username . "\0" . $this->password);
        $this->sendXML($connection, '<auth xmlns="urn:ietf:params:xml:ns:xmpp-sasl" mechanism="PLAIN">' . $auth . '</auth>');
        $authResponse = $this->recvXML($connection);

        // Check for successful authentication
        if (strpos($authResponse, '<success') !== false) {
            // Restart stream after successful authentication
            $this->sendXML($connection, '<stream:stream xmlns:stream="http://etherx.jabber.org/streams" version="1.0" xmlns="jabber:client" to="' . $this->server . '" xml:lang="en" xmlns:xml="http://www.w3.org/XML/1998/namespace">');
            $data = $this->recvXML($connection);

            // Bind resource
            $this->sendXML($connection, '<iq type="set" id="bind_1"><bind xmlns="urn:ietf:params:xml:ns:xmpp-bind"><resource>' . $this->resource . '</resource></bind></iq>');
            $bindResponse = $this->recvXML($connection);

            // Start session
            $this->sendXML($connection, '<iq type="set" id="session_1"><session xmlns="urn:ietf:params:xml:ns:xmpp-session"/></iq>');
            $sessionResponse = $this->recvXML($connection);

            return $connection;
        }

        // Authentication failed
        return null;
    }

    // Get connected users
    public function getUsers($connection)
    {
        $xml = '<iq from="' . $this->username . '@' . $this->server . '/' . $this->resource . '" id="123" type="get"><query xmlns="jabber:iq:roster"/></iq>';
        $this->sendXML($connection, $xml);
        $data = $this->recvXML($connection);

        $xmlData = simplexml_load_string($data);
        $users = [];
        foreach ($xmlData->query->item as $user) {
            $users[] = (string) $user->attributes()->name;
        }
        return $users;
    }

    // Disconnect
    public function disconnect($connection)
    {
        fclose($connection);
    }
}