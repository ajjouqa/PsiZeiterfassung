<?php


// test-xmpp.php
require 'vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();


$server = '127.0.0.1';
$username = 'admin'; // Just the username part, not the full JID
$password = 'admin';
$port = 5222; // Default XMPP port
$resource = 'globe'; // Resource name for the connection



$xmppService = app()->make('App\Services\XMPPService', [
    'server' => '127.0.0.1',
    'username' => 'admin',
    'password' =>'admin',
    'resource' => 'globe',
    'port' => 5222,
    'timeout' => 5
]);

echo "Testing basic connection to the server...\n";
$canConnect = @fsockopen($server, $port, $errno, $errstr, 5);
if (!$canConnect) {
    echo "Cannot establish basic connection to server: $errstr ($errno)\n";
    echo "Please check if server is running and accessible.\n";
    exit;
} else {
    echo "Basic connection to server established successfully.\n";
    fclose($canConnect);
}

echo "Attempting XMPP authentication...\n";
$connection = $xmppService->authenticate();
if ($connection) {
    echo "Connected successfully to XMPP server\n";
    $xmppService->setPresence($connection, 'Online');
    sleep(5);
    $xmppService->disconnect($connection);
} else {
    echo "Failed to authenticate with XMPP server\n";
    echo "Check the Laravel log file for more details.\n";
}