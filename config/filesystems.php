<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'tmp' => [
            'driver' => 'local',
            'root' => storage_path('tmp'),
            'url' => '/storage/tmp',
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key'    => env('S3_KEY', 'your-key'),
            'secret' => env('S3_SECRET', 'your-secret'),
            'region' => env('S3_REGION', 'your-region'),
            'bucket' => env('S3_BUCKET', 'your-bucket'),
        ],

        'ftp' => [
            'driver'   => env('FTP_DRIVER', 'ftp'),
            'host'     => env('FTP_HOST'),
            'username' => env('FTP_USERNAME'),
            'password' => env('FTP_PASSWORD'),

            // Optional FTP Settings...
            // 'port'     => 21,
            // 'root'     => '',
            // 'passive'  => true,
            // 'ssl'      => true,
            // 'timeout'  => 30,
        ],

        'sftp_sinotrans' => [
            'driver'     => 'sftp',
            'host'       => env('SFTP_HOST_SINOTRANS', ''),
            'username'   => env('SFTP_USERNAME_SINOTRANS', ''),
            'password'   => env('SFTP_PASSWORD_SINOTRANS', ''),
            'port'       => env('SFTP_PORT', '22'),
            'privateKey' => env('SFTP_PRIVATE_KEY_PATH', ''),
            'root'       => env('SFTP_ROOT', ''),
            'timeout'    => env('SFTP_TIMEOUT', '10'),
        ],

        'ftp_correoschile' => [
            'driver'     => 'ftp',
            'host'       => env('FTP_CORREOSCHILE_HOST', ''),
            'username'   => env('FTP_CORREOSCHILE_USERNAME', ''),
            'password'   => env('FTP_CORREOSCHILE_PASSWORD', ''),
            'root'       => env('FTP_CORREOSCHILE_ROOT', ''),
        ],

    ],

];
