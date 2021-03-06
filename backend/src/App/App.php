<?php

declare(strict_types=1);

//Sessions are used to keep the user logged in, the if !isset is for phpunit
if(!isset($_SESSION)) session_start(); 

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/DotEnv.php';
$app = require __DIR__ . '/Container.php';
$customErrorHandler = require __DIR__ . '/ErrorHandler.php';
(require __DIR__ . '/Middlewares.php')($app, $customErrorHandler);
(require __DIR__ . '/Cors.php')($app);
(require __DIR__ . '/Database.php');
(require __DIR__ . '/Services.php');
(require __DIR__ . '/Repositories.php');
(require __DIR__ . '/Routes.php');
(require __DIR__ . '/NotFound.php')($app);

return $app;
