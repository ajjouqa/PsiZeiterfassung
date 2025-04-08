<?php
class TestXMPP {
    protected $server;
    protected $username;
    protected $password;
    protected $port;
    protected $timeout;

    public function __construct($server, $username, $password, $port = 5222, $timeout = 5) {
        $this->server = $server;
        $this->username = $username;
        $this->password = $password;
        $this->port = $port;
        $this->timeout = $timeout;
    }

    public function test() {
        echo "Testing XMPP connection to {$this->server}:{$this->port}\n";
        echo "Username: {$this->username}\n\n";

  
        echo "Opening connection... ";
        $connection = @fsockopen($this->server, $this->port, $errno, $errstr, $this->timeout);
        if (!$connection) {
            echo "FAILED!\nError: $errstr ($errno)\n";
            return false;
        }
        echo "SUCCESS!\n\n";

        stream_set_timeout($connection, $this->timeout);

        // Start XMPP stream
        echo "Sending initial stream header... ";
        $xml = '<stream:stream xmlns:stream="http://etherx.jabber.org/streams" version="1.0" xmlns="jabber:client" to="' . $this->server . '" xml:lang="en" xmlns:xml="http://www.w3.org/XML/1998/namespace">';
        fwrite($connection, $xml);
        echo "SENT\n";

        echo "Waiting for server features... ";
        $response = $this->readResponse($connection);
        if ($response === false) {
            echo "TIMEOUT!\n";
            fclose($connection);
            return false;
        }
        echo "RECEIVED\n\n";
        echo "Server response:\n" . htmlspecialchars($response) . "\n\n";

        // Try SASL PLAIN authentication
        echo "Attempting SASL PLAIN authentication... ";
        $auth = base64_encode("\0" . $this->username . "\0" . $this->password);
        $xml = '<auth xmlns="urn:ietf:params:xml:ns:xmpp-sasl" mechanism="PLAIN">' . $auth . '</auth>';
        fwrite($connection, $xml);
        echo "SENT\n";

        echo "Waiting for authentication response... ";
        $response = $this->readResponse($connection);
        if ($response === false) {
            echo "TIMEOUT!\n";
            fclose($connection);
            return false;
        }
        echo "RECEIVED\n\n";
        echo "Authentication response:\n" . htmlspecialchars($response) . "\n\n";

        // Check if authentication was successful
        if (strpos($response, '<success') !== false) {
            echo "Authentication SUCCESSFUL!\n";
        } else {
            echo "Authentication FAILED!\n";
        }

        // Close the connection
        fclose($connection);
        echo "Connection closed.\n";
    }

    protected function readResponse($connection) {
        $response = '';
        $startTime = time();
        
        while (true) {
            // Check for timeout
            if (time() - $startTime > $this->timeout) {
                return false;
            }
            
            // Read available data
            $buffer = fread($connection, 4096);
            if ($buffer === false || feof($connection)) {
                return false;
            }
            
            $response .= $buffer;
            
            // Check if we have received the end of the stream or a success/failure response
            if (!empty($response) && (
                strpos($response, '</stream:features>') !== false || 
                strpos($response, '<success') !== false || 
                strpos($response, '<failure') !== false
            )) {
                return $response;
            }
            
            // Small pause to prevent CPU hogging
            usleep(100000); // 100ms
        }
    }
}

// Connection parameters
$server = '127.0.0.1';
$username = 'admin'; // Just the username part, not the full JID
$password = 'admin'; // Replace with your actual password

// Run the test
$tester = new TestXMPP($server, $username, $password);
$tester->test();