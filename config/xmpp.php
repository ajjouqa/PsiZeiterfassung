<?php

return [
    'server' => env('XMPP_SERVER', '127.0.0.1'),
    'username' => env('XMPP_USERNAME', 'admin'),
    'password' => env('XMPP_PASSWORD', 'admin'),
    'resource' => env('XMPP_RESOURCE', 'globe'),

    'port' => env('XMPP_PORT', 5222),

    'auto_create_of_users' => env('XMPP_AUTO_CREATE_USERS', true),
    'domain' => env('XMPP_DOMAIN', 'desktop-vfgpg63'),

    'default_group' => env('XMPP_DEFAULT_GROUP', 'Users'),
    
    'admin_user' => env('XMPP_ADMIN_USER', 'admin'),
    'admin_password' => env('XMPP_ADMIN_PASSWORD', ''),
];