<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\UserException;
use App\Repository\UserRepository;

final class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAll(): array
    {
        return $this->userRepository->getAll();
    }

    //Returns the whole user
    public function getWholeUser(int $user_id): array {
        $user = $this->userRepository->getUser($user_id);
        $user['skills'] = $this->userRepository->getAllSkills($user_id);
        $user['availability'] = $this->userRepository->getAvailability($user_id);
        $user['interests'] = $this->userRepository->getAllInterests($user_id);
        $user['teams'] = $this->userRepository->getUserTeamIDs($user_id);
        return $user;
    }

    //Returns the user's teams
    public function getUserTeams(int $user_id): array {
        return $this->userRepository->getUserTeamStatuses($user_id);
    }

    //Updates the given user
    public function update(array $input, int $user_id): void {
        $user = $this->getWholeUser($user_id);

        if (isset($input['username']) || isset($input['name']) || isset($input['bio'])) {
            $this->userRepository->updateUser($user, $input);
        }
        if (isset($input['skills'])) {
            $this->userRepository->deleteAllSkills($user_id);
            $this->userRepository->addSkills($user_id, $input['skills']);
        }
        if (isset($input['availability'])){
            $this->userRepository->deleteAvailabilities($user_id);
            $this->userRepository->addAvailabilities($user_id, $input['availability']);
        }
        if (isset($input['interests'])) {
            $this->userRepository->deleteAllInterests($user_id);
            $this->userRepository->addInterests($user_id, $input['interests']);
        }
    }

    //Deletes the given user
    public function deleteUser(int $user_id): void {
        $this->userRepository->deleteUser($user_id);
    }

    //Returns the user's team requests
    public function getUserTeamRequests(int $user_id): array {
        $requests = $this->userRepository->getUserTeamRequests($user_id);
        return $requests;
    }

    //Returns the user's team invites
    public function getUserTeamInvites(int $user_id): array {
        $invites = $this->userRepository->getUserTeamInvites($user_id);
        return $invites;
    }
}
