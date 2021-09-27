<?php

declare(strict_types=1);

use Slim\Routing\RouteCollectorProxy;
use App\Controller\Auth\AuthMiddleware;

$app->get('/', 'App\Controller\Home:getHelp');
$app->get('/status', 'App\Controller\Home:getStatus');

$app->post('/register', App\Controller\Auth\Register::class);
$app->post('/login', App\Controller\Auth\Login::class);
$app->get('/user/{id}', App\Controller\User\GetOne::class);

//Routes that require authentication
$app->group('', function (RouteCollectorProxy $group){
    $group->get('/checkauth', App\Controller\Auth\CheckAuth::class);
    $group->put('/user', App\Controller\User\Update::class);
    $group->delete('/user', App\Controller\User\Delete::class);
})->add(new AuthMiddleware);