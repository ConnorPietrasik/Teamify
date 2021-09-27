<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\AuthException;
use App\Repository\AuthRepository;

final class AuthService {
    private AuthRepository $authRepository;

    public function __construct(AuthRepository $authRepository) {
        $this->authRepository = $authRepository;
    }

    //Creates the given user, this is in auth because it is only done with registration
    public function createUser(array $input): int {
        if (!isset($input['username'])){
            throw new AuthException('Missing username', 400);
        }
        if ($this->authRepository->getUserID($input['username']) != -1){
            throw new AuthException('Username already in use', 409);
        }
        return $this->authRepository->createUser($input);
    }

    //Returns true if the id/pass combo matches one found in database
    public function validatePassword(int $id, string $password): bool {
        $hash = $this->authRepository->getHash($id);
        return password_verify($password, $hash);
    }

    //Returns the user ID if a the user/pass combo is valid, -1 otherwise
    public function login(array $input): int {
        if (!isset($input['username'])) throw new AuthException('Missing username', 400);
        if (!isset($input['password'])) throw new AuthException('Missing password', 400);

        $id = $this->authRepository->getUserID($input['username']);
        return $this->validatePassword($id, $input['password']) ? $id : -1;
    }

    //Adds the given password to the given user id
    public function addPassword(int $id, string $password): bool {
        return $this->authRepository->addPassword($id, $password);
    }

    //Changes the user's password
    public function changePassword(int $id, string $password): bool {
        return $this->authRepository->changePassword($id, $password);
    }
}
