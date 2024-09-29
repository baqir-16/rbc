<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Remote Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default connection that will be used for SSH
    | operations. This name should correspond to a connection name below
    | in the server list. Each connection will be manually accessible.
    |
    */

    'default' => 'MDB_SERVER',

    /*
    |--------------------------------------------------------------------------
    | Remote Server Connections
    |--------------------------------------------------------------------------
    |
    | These are the servers that will be accessible via the SSH task runner
    | facilities of Laravel. This feature radically simplifies executing
    | tasks on your servers, such as deploying out these applications.
    |
    */

    'connections' => [
        'MDB_SERVER' => [
            'host'      => env('DB_HOST', $_ENV['MONGO_DB_HOST']),
            'host_http' => 'http://'.env('DB_HOST', $_ENV['MONGO_DB_HOST']),
            'username'  => env('DB_USERNAME', $_ENV['MONGO_DB_SERVER_USERNAME']),
            'password'  => env('DB_PASSWORD', $_ENV['MONGO_DB_SERVER_PASSWORD']),
            'key'       => '',
            'keytext'   => '',
            'keyphrase' => '',
            'agent'     => '',
            'timeout'   => 10,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Remote Server Groups
    |--------------------------------------------------------------------------
    |
    | Here you may list connections under a single group name, which allows
    | you to easily access all of the servers at once using a short name
    | that is extremely easy to remember, such as "web" or "database".
    |
    */

    'groups' => [
        'web' => ['MDB_SERVER'],
    ],

];
