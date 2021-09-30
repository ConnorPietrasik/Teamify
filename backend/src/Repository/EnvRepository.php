<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exception\EnvException;

final class EnvRepository {
    private \PDO $database;

    public function __construct(\PDO $database) {
        $this->database = $database;
    }

    public function getDb(): \PDO {
        return $this->database;
    }

    //Returns all the open users in a given environment
    public function getOpenIDs(int $env_id): array {
        $query = 'SELECT user_id FROM env_open WHERE env_id = :id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $env_id);
        $statement->execute();
        $users = $statement->fetchAll(\PDO::FETCH_COLUMN, 0);

        return $users;
    }

    public function addOpen(int $env_id, int $user_id): void {
        $query = 'INSERT INTO env_open (env_id, user_id) VALUES (:env, :user)';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('env', $env_id);
        $statement->bindParam('user', $user_id);

        $statement->execute();
    }

    public function removeOpen(int $env_id, int $user_id): void {
        $query = 'DELETE FROM env_open WHERE env_id = :env AND user_id = :user';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('env', $env_id);
        $statement->bindParam('user', $user_id);
        
        $statement->execute();
    }
}
