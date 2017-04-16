<?php
declare(strict_types=1);

use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine;
use RussiaToday\Api\Client;
use RussiaToday\Schedule;
use SuperChannel\Schedule as SupperChannelSchedule;
use TvArchive\FFmpeg\Recorder;
use TvArchive\Logger\Adapter\File;
use TvArchive\Mutex\Lock\FlockLock;

if (!isset($config)) {
    throw new RuntimeException('Config not defined');
}

$di->setShared('config', function () use ($config) {
    return $config;
});

$di->setShared('view', function () {
    $view   = new View();
    $config = $this->getConfig();

    $view->setDI($this);
    $view
        ->setViewsDir($config->application->viewsDir)
        ->registerEngines([
            '.volt' => function ($view) {
                $config = $this->getConfig();

                $volt = new Engine\Volt($view, $this);

                $volt->setOptions([
                    'compiledPath' => "{$config->application->cacheDir}/",
                    'compiledSeparator' => '_'
                ]);

                return $volt;
            },
            '.phtml' => Engine\Php::class
        ])
    ;

    return $view;
});

$di->setShared('db', function () {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset
    ];

    if ($config->database->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    return new $class($params);
});

$di->setShared('mutex.flock', function () {
    $flockLock   = new FlockLock($this->getConfig()->application->locksDir);
    $mutexFabric = new \NinjaMutex\MutexFabric('flock', $flockLock);

    return $mutexFabric;
});

/**
 * Supper Channel services
 */
$di->setShared('sc.api.client', function () {
    $config = $this->getConfig();

    return new SuperChannel\Api\Client($config->channels->sc->wsdl);
});

$di->setShared('sc.schedule', function () {
    $apiClient = $this->get('sc.api.client');

    return new SupperChannelSchedule($apiClient);
});

$di->setShared('sc.recorder', function () {
    $config   = $this->getConfig();
    $schedule = $this->get('sc.schedule');

    return new Recorder(
        $config->channels->sc->streamUrl,
        $schedule,
        $config->application->storageDir,
        3600,
        300,
        $config->channels->sc->ffmpegOptions
    );
});

$di->setShared('sc.recorder.mutex', function () {
    return $this->get('mutex.flock')->get('sc.recorder');
});

/**
 * Russia Today services
 */
$di->setShared('rt.api.client.log', function () {
    $config = $this->getConfig();

    return new File("{$config->application->logsDir}/russia-today-api.log");
});

$di->setShared('rt.api.client', function () {
    $config = $this->getConfig();
    $logger = $this->get('rt.api.client.log');

    return new Client($config->channels->rt->baseUrl, $logger);
});

$di->setShared('rt.schedule', function () {
    $apiClient = $this->get('rt.api.client');

    return new Schedule($apiClient);
});

$di->setShared('rt.recorder', function () {
    $config      = $this->getConfig();
    $schedule    = $this->get('rt.schedule');

    return new Recorder(
        $config->channels->rt->streamUrl,
        $schedule,
        $config->application->storageDir,
        1800,
        60,
        $config->channels->rt->ffmpegOptions
    );
});

$di->setShared('rt.recorder.mutex', function() {
    return $this->get('mutex.flock')->get('rt.recorder');
});