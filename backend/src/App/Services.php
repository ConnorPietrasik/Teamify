<?php

declare(strict_types=1);

$container['user_service'] = static function (Pimple\Container $container): App\Service\UserService {
    return new App\Service\UserService($container['user_repository']);
};

$container['auth_service'] = static function (Pimple\Container $container): App\Service\AuthService {
    return new App\Service\AuthService($container['auth_repository']);
};