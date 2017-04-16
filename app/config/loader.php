<?php
declare(strict_types=1);

return (new \Phalcon\Loader())
    ->registerDirs([
        APP_PATH . '/controllers',
        APP_PATH . '/models',
        APP_PATH . '/tasks'
    ])
    ->register();
