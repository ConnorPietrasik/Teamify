<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\EnvException;
use App\Repository\EnvRepository;
use Google_Client;

final class EnvService {
    private EnvRepository $envRepository;

    public function __construct(EnvRepository $envRepository) {
        $this->envRepository = $envRepository;
    }

    //Returns a list of all open users for that environment
    public function getOpen(int $env_id): array {
        $users = $this->envRepository->getOpenIDs($env_id);
        return $users;
    }

    //Adds the user to the environment's open list
    public function addOpen(int $env_id, int $user_id): void {
        $this->envRepository->addOpen($env_id, $user_id);
    }

    //Removes the user from the environment's open list
    public function removeOpen(int $env_id, int $user_id): void {
        $this->envRepository->removeOpen($env_id, $user_id);
    }
}
