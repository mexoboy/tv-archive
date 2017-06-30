<?php
declare(strict_types=1);

define('BASE_PATH', realpath(__DIR__ . '/../..'));
define('APP_PATH', BASE_PATH . '/app');
define('VAR_PATH', BASE_PATH . '/var');

$config = new \Phalcon\Config([
    'application' => [
        'appDir'         => APP_PATH,
        'controllersDir' => APP_PATH . '/controllers',
        'modelsDir'      => APP_PATH . '/models',
        'migrationsDir'  => APP_PATH . '/migrations',
        'viewsDir'       => APP_PATH . '/views',
        'cacheDir'       => VAR_PATH . '/cache',
        'logsDir'        => VAR_PATH . '/logs',
        'locksDir'       => VAR_PATH . '/locks',
        'storageDir'     => VAR_PATH . '/storage'
    ],

    'database' => [
        'adapter'  => 'Mysql',
        'host'     => 'mysql',
        'username' => 'root',
        'password' => '',
        'dbname'   => 'phalcon',
        'charset'  => 'utf8',
    ],

    'channels' => [
        'rt' => [
            'baseUrl'       => 'https://www.rt.com',
            'streamUrl'     => 'http://rt-eng-live.hls.adaptive.level3.net/rt/eng/index400.m3u8',
            'ffmpegOptions' => '-bsf:a aac_adtstoasc',
        ],

        'sc' => [
            'wsdl'          => 'https://localhost/WebServices/Schedule.asmx?WSDL',
            'streamUrl'     => 'rtmp://localhost:1935/tv/mp4:superchannel',
            'ffmpegOptions' => '',
        ]
    ]
]);

$configOverridePath = __DIR__ . '/config.override.php';

if (is_file($configOverridePath)) {
    $configOverride = include $configOverridePath;

    $config->merge($configOverride);
}

return $config;