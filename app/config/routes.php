<?php
declare(strict_types=1);

use Phalcon\Mvc\Router;

/** @var Router $router */
$router = $di->getRouter();

$router->add('/record/view/:int', [
    'controller' => 'record',
    'action'     => 'viewRecord',
    'recordId'   => 1
]);

$router->add('/', [
    'controller' => 'record',
    'action'     => 'list'
]);

$router->handle();