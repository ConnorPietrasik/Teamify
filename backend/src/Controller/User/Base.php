<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Service\EnvService;
use App\Service\TeamService;
use App\Service\UserService;
use Pimple\Psr11\Container;

abstract class Base
{
    protected Container $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    protected function getUserService(): UserService {
        return $this->container->get('user_service');
    }

    protected function getTeamService(): TeamService {
        return $this->container->get('team_service');
    }

    protected function getEnvService(): EnvService {
        return $this->container->get('env_service');
    }
}
