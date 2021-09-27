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

    public function checkAndGet(int $userId): object
    {
        return $this->userRepository->checkAndGet($userId);
    }

    public function getAll(): array
    {
        return $this->userRepository->getAll();
    }

    public function getOne(int $userId): object
    {
        return $this->checkAndGet($userId);
    }

    public function update(array $input, int $userId): object
    {
        $user = $this->checkAndGet($userId);
        $data = json_decode((string) json_encode($input), false);

        return $this->userRepository->update($user, $data);
    }

    //Deletes the given user
    public function delete(int $userId): void {
        $this->userRepository->delete($userId);
    }
}
