<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\EnvException;
use App\Repository\EnvRepository;

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
            $users[] = $this->getEnvUser($env_id, $user_id);
        }
        return $users;
    }

    //Returns all open users that match at least one skill
    public function getOpenUsersBySkill(int $env_id, array $skills): array {
        $user_ids = $this->envRepository->getOpenSkillIDs($env_id, $skills);
        $users = [];
        foreach ($user_ids as $user_id){
            $users[] = $this->getEnvUser($env_id, $user_id);
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
            $users[] = $this->getEnvUser($env_id, $user_id);
        }
        return $users;
    }

    //Returns all the team IDs
    public function getAllTeamIDs(int $env_id): array {
        return $this->envRepository->getAllEnvTeamIDs($env_id);
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
        $user['team'] = $this->envRepository->getEnvUserTeamID($env_id, $user_id);
        if ($user['team'] != -1) $user['status'] = $this->envRepository->getUserTeamStatus($user_id, $user['team']);
        else $user['status'] = -1;
        $user['env_status'] = $this->envRepository->getUserEnvStatus($user_id, $env_id);
        $user['open'] = $this->envRepository->isOpen($env_id, $user_id);

        return $user;
    }

    //Creates the environment with given specs
    public function createEnv(int $owner_id, string $name, string $code): int {
        $errCheck = $this->envRepository->getEnvIDByCode($code);
        if ($errCheck != -1) throw new EnvException("Environment code already in use for env_id ".$errCheck, 409);
        
        $env_id = $this->envRepository->createEnv($name, $code);
        $this->envRepository->addEnvMember($env_id, $owner_id, 1);
        return $env_id;
    }

    //Joins the environment with the given code
    public function joinEnv(int $user_id, string $code): int {
        $env_id = $this->envRepository->getEnvIDByCode($code);
        if ($env_id == -1) throw new EnvException("Environment code not found", 404);

        $status = $this->envRepository->getEnvMemberStatus($env_id, $user_id);
        if ($status == 2) throw new EnvException("User has been banned from environment", 401);
        if ($status != -1) throw new EnvException("User already in environment", 409);

        $this->envRepository->addEnvMember($env_id, $user_id);
        return $env_id;
    }

    //Deletes the given environment
    public function deleteEnv(int $env_id): void {
        $this->envRepository->deleteEnv($env_id);
    }

    //Returns the team ids from list that match at least one skill or interest
    public function getMatchingTeamIDsForUser(int $env_id, int $user_id): array {
        $team_ids = $this->getAllTeamIDs($env_id);
        $skills = $this->envRepository->getEnvSkills($env_id, $user_id);
        $interests = $this->envRepository->getEnvInterests($env_id, $user_id);
        return $this->getMatchingTeamIDs($team_ids, $skills, $interests);
    }

    //Returns the team ids from list that match at least one skill or interest
    public function getMatchingTeamIDs(array $team_ids, array $skills, array $interests): array {
        return $this->envRepository->getMatchingTeamIDs($team_ids, $skills, $interests);
    }
}
