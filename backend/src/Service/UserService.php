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
    public function getWholeUser(int $id): array {
        $user = $this->userRepository->getUser($id);
        $user['skills'] = $this->userRepository->getAllSkills($id);
        $user['availability'] = $this->userRepository->getAvailability($id);
        $user['interests'] = $this->userRepository->getAllInterests($id);
        $user['teams'] = $this->userRepository->getUserTeams($id);
        return $user;
    }

    //Updates the given user
    public function update(array $input, int $id): void {
        $user = $this->getWholeUser($id);

        if (isset($input['username']) || isset($input['name']) || isset($input['bio'])) {
            $this->userRepository->updateUser($user, $input);
        }
        if (isset($input['skills'])) {
            $this->userRepository->deleteAllSkills($id);
            $this->userRepository->addSkills($id, $input['skills']);
        }
        if (isset($input['availability'])){
            $this->userRepository->deleteAvailabilities($id);
            $this->userRepository->addAvailabilities($id, $input['availability']);
        }
        if (isset($input['interests'])) {
            $this->userRepository->deleteAllInterests($id);
            $this->userRepository->addInterests($id, $input['interests']);
        }
    }

    //Deletes the given user
    public function deleteUser(int $id): void {
        $this->userRepository->deleteUser($id);
    }
}
