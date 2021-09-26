<?php

declare(strict_types=1);

$container['user_repository'] = static function (Pimple\Container $container): App\Repository\UserRepository {
    return new App\Repository\UserRepository($container['db']);
};

$container['auth_repository'] = static function (Pimple\Container $container): App\Repository\AuthRepository {
    return new App\Repository\AuthRepository($container['db']);
};