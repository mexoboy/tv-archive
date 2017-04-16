<?php
declare(strict_types=1);

use Phalcon\Di\FactoryDefault;
use Phalcon\Dispatcher;
use Phalcon\Events\Manager;
use Phalcon\Mvc\Application;
use TvArchive\Exception\NotFoundException;

define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/vendor/autoload.php';
require_once ROOT_PATH . '/app/config/config.php';

$di = new FactoryDefault();

require_once APP_PATH . '/config/loader.php';
require_once APP_PATH . '/config/services.php';
require_once APP_PATH . '/config/routes.php';

$eventsManager = new Manager();

$eventsManager->attach("dispatch", function ($event, $dispatcher, $exception) {
    if ($event->getType() == 'beforeException') {
        if ($exception instanceof NotFoundException
            || $exception->getCode() == Dispatcher::EXCEPTION_HANDLER_NOT_FOUND
            || $exception->getCode() == Dispatcher::EXCEPTION_ACTION_NOT_FOUND
        ) {
            $dispatcher->forward(array(
                'controller' => 'error',
                'action'     => 'notFound'
            ));

            return false;
        }
    }
});

$di->getDispatcher()->setEventsManager($eventsManager);

echo (new Application($di))->handle()->getContent();