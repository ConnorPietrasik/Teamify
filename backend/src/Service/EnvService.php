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

    //Returns all the env_users that are open in that environment
    public function getOpenUsers(int $env_id): array {
        $user_ids = $this->getOpenIDs($env_id);
        $users = [];
        foreach ($user_ids as $user_id){
            $users[] = getEnvUser($env_id, $user_id);
        }
        return $users;
    }

    //Returns a list of all open users for that environment
    public function getOpenIDs(int $env_id): array {
        $user_ids = $this->envRepository->getOpenIDs($env_id);
        return $user_ids;
    }

    //Returns all users in the environment
    public function getAllEnvUsers(int $env_id): array {
        $user_ids = $this->envRepository->getAllEnvUserIDs($env_id);
        $users = [];
        foreach ($user_ids as $user_id){
            $users[] = getEnvUser($env_id, $user_id);
        }
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

    //Returns the user's info for the environment (includes env_id == 0, which is global)
    public function getEnvUser(int $env_id, int $user_id): array {
        $user = $this->envRepository->getUser($user_id);
        $user['skills'] = $this->envRepository->getEnvSkills($env_id, $user_id);
        $user['availability'] = $this->envRepository->getAvailability($user_id);
        $user['interests'] = $this->envRepository->getEnvInterests($env_id, $user_id);
        $user['team'] = $this->envRepository->getEnvUserTeam($env_id, $user_id);

        return $user;
    }
}
