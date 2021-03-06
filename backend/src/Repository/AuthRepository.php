<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exception\AuthException;

final class AuthRepository {
    private \PDO $database;

    public function __construct(\PDO $database) {
        $this->database = $database;
    }

    public function getDb(): \PDO {
        return $this->database;
    }

    //Returns the user's ID from the username, or -1 if it doesn't exist
    public function getUserIDByUsername(string $username): int{
        $query = 'SELECT user_id FROM user WHERE username = :username';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('username', $username);
        $statement->execute();
        $id = $statement->fetchColumn();
        if (!$id){
            //Admin has a user_id of 0
            if ($username == 'admin') return 0;
            return -1;
        }
        return (int) $id;
    }

    //Returns the user's ID from the oauth google id
    public function getUserIDByGoogle(string $google_id): int{
        $query = 'SELECT user_id FROM oauth WHERE google_id = :google_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('google_id', $google_id);
        $statement->execute();
        $id = $statement->fetchColumn();
        
        if (!$id) throw new AuthException('User does not yet have an account, please register', 404);
        return (int) $id;
    }

    //Creates the given user and returns their ID
    public function createUser(array $user): int {
        $query = 'INSERT INTO user (username, name, bio) VALUES (:username, :name, :bio)';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('username', $user['username']);
        $statement->bindValue('name', $user['name'] ?? null);
        $statement->bindValue('bio', $user['bio'] ?? null);
        $statement->execute();

        return (int) $this->getDb()->lastInsertId();
    }

    //Returns the hashed password associated with the ID
    public function getHash(int $id): string {
        $query = 'SELECT password FROM password WHERE user_id = :user_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('user_id', $id);
        $statement->execute();
        $pass = $statement->fetchColumn();
        if (!$pass) {
            throw new AuthException('User password not found.', 404);
        }
        return $pass;
    }

    //Adds the given google ID to the user
    public function addGoogle(int $id, string $google_id): bool{
        $query = 'INSERT INTO oauth (user_id, google_id) VALUES (:user_id, :google_id)';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('user_id', $id);
        $statement->bindParam('google_id', $google_id);

        return $statement->execute();
    }

    //Adds the given id/pass combo to the database after hashing the password
    public function addPassword(int $id, string $password): bool {
        $query = 'INSERT INTO password (user_id, password) VALUES (:user_id, :password)';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('user_id', $id);
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $statement->bindParam('password', $hashed);

        return $statement->execute();
    }

    //Changes the password for the given id
    public function changePassword(int $id, string $password): bool{
        $query = 'UPDATE password SET password = :password WHERE user_id = :user_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('user_id', $id);
        $statement->bindParam('password', password_hash($password, PASSWORD_BCRYPT));

        return $statement->execute();
    }
}
