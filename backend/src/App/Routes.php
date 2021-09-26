<?php

declare(strict_types=1);

$app->get('/', 'App\Controller\Home:getHelp');
$app->get('/status', 'App\Controller\Home:getStatus');

$app->post('/register', App\Controller\Auth\Register::class);
$app->get('/user/{id}', App\Controller\User\GetOne::class);
$app->put('/user', App\Controller\User\Update::class);
$app->delete('/user', App\Controller\User\Delete::class);
