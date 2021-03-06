<?php

declare(strict_types=1);

$container['user_service'] = static function (Pimple\Container $container): App\Service\UserService {
    return new App\Service\UserService($container['user_repository']);
};

$container['auth_service'] = static function (Pimple\Container $container): App\Service\AuthService {
    return new App\Service\AuthService($container['auth_repository']);
};

$container['env_service'] = static function (Pimple\Container $container): App\Service\EnvService {
    return new App\Service\EnvService($container['env_repository']);
};

$container['team_service'] = static function (Pimple\Container $container): App\Service\TeamService {
    return new App\Service\TeamService($container['team_repository']);
};