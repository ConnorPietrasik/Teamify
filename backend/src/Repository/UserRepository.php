<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exception\UserException;

final class UserRepository
{
    private \PDO $database;

    public function __construct(\PDO $database)
    {
        $this->database = $database;
    }

    public function getDb(): \PDO
    {
        return $this->database;
    }

    public function checkAndGet(int $userId): object
    {
        $query = 'SELECT * FROM `user` WHERE `user_id` = :id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $userId);
        $statement->execute();
        $user = $statement->fetchObject();
        if (! $user) {
            throw new UserException('User not found.', 404);
        }

        return $user;
    }

    public function getAll(): array
    {
        $query = 'SELECT * FROM `user` ORDER BY `id`';
        $statement = $this->getDb()->prepare($query);
        $statement->execute();

        return (array) $statement->fetchAll();
    }

    public function update(object $user, object $data): object
    {
        if (isset($data->user_id)) {
            $user->user_id = $data->user_id;
        }
        if (isset($data->username)) {
            $user->username = $data->username;
        }
        if (isset($data->name)) {
            $user->name = $data->name;
        }
        if (isset($data->bio)) {
            $user->bio = $data->bio;
        }

        $query = 'UPDATE `user` SET `user_id` = :user_id, `username` = :username, `name` = :name, `bio` = :bio WHERE `id` = :id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('user_id', $user->user_id);
        $statement->bindParam('username', $user->username);
        $statement->bindParam('name', $user->name);
        $statement->bindParam('bio', $user->bio);

        $statement->execute();

        return $this->checkAndGet((int) $user->id);
    }

    public function delete(int $userId): void
    {
        $query = 'DELETE FROM `user` WHERE `id` = :id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $userId);
        $statement->execute();
    }
}
