<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\AuthException;
use App\Repository\AuthRepository;
use Google_Client;

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
        if ($this->authRepository->getUserIDByUsername($input['username']) != -1){
            throw new AuthException('Username already in use', 409);
        }
        if (!isset($input['id_token'])) {
            if (!isset($input['password'])) throw new AuthException('Must register with password or oauth', 400);
            $id = $this->authRepository->createUser($input);
            $this->addPassword($id, $input['password']);
            return $id;
        }
        else {
            $id = $this->authRepository->createUser($input);
            $google_id = $this->getOauthID($input["id_token"]);
            $this->addOauth($id, $google_id);
            return $id;
        }
    }

    //Returns true if the id/pass combo matches one found in database
    public function validatePassword(int $id, string $password): bool {
        $hash = $this->authRepository->getHash($id);
        return password_verify($password, $hash);
    }

    //Verifies that the token is legit and returns the ID
    public function getOauthID(string $id_token) : string {
        $client = new Google_Client(['client_id' => '82664365493-qm3h7p8dsqkri7f4mbuc0jmjk02ednv7.apps.googleusercontent.com']);
        $payload = $client->verifyIdToken($id_token);
        if (!$payload) throw new AuthException('Invalid ID Token', 400);

        return $payload['sub'];
    }

    //Returns the user ID if a the user/pass combo is valid, -1 otherwise
    public function login(array $input): int {
        if (isset($input['id_token'])){
            $google_id = $this->getOauthID($input['id_token']);
            return $this->authRepository->getUserIDByGoogle($google_id);
        } else{
            if (!isset($input['username'])) throw new AuthException('Missing username', 400);
            if (!isset($input['password'])) throw new AuthException('Missing password', 400);
    
            $id = $this->authRepository->getUserIDByUsername($input['username']);
            return $this->validatePassword($id, $input['password']) ? $id : -1;
        }
    }

    public function addOauth(int $id, string $google_id): bool {
        return $this->authRepository->addOauth($id, $google_id);
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
