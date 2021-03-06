<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Service\AuthService;
use App\Service\UserService;
use Pimple\Psr11\Container;

abstract class Base {
    protected Container $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    protected function getAuthService(): AuthService {
        return $this->container->get('auth_service');
    }

    protected function getUserService(): UserService {
        return $this->container->get('user_service');
    }
}
