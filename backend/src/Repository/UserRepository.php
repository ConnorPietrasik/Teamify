<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exception\UserException;

final class UserRepository {
    private \PDO $database;

    public function __construct(\PDO $database) {
        $this->database = $database;
    }

    public function getDb(): \PDO {
        return $this->database;
    }

    //Returns the data from the user table with the given ID
    public function getUser(int $user_id): array {
        $query = 'SELECT * FROM user WHERE user_id = :user_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('user_id', $user_id);
        $statement->execute();
        $user = $statement->fetch(\PDO::FETCH_ASSOC);
        if (! $user) {
            throw new UserException('User not found.', 404);
        }

        return $user;
    }

    //Updates the given user table entry with the given data
    public function updateUser(array $user, array $data): void {
        if (isset($data['username'])) {
            $user['username'] = $data['username'];
        }
        if (isset($data['name'])) {
            $user['name'] = $data['name'];
        }
        if (isset($data['bio'])) {
            $user['bio'] = $data['bio'];
        }

        $query = 'UPDATE user SET username = :username, name = :name, bio = :bio WHERE user_id = :user_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('user_id', $user['user_id']);
        $statement->bindParam('username', $user['username']);
        $statement->bindParam('name', $user['name']);
        $statement->bindParam('bio', $user['bio']);

        $statement->execute();
    }

    //Deletes the given user
    public function deleteUser(int $userId): void {
        $query = 'DELETE FROM user WHERE user_id = :user_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('user_id', $userId);
        $statement->execute();
    }

    //Returns all the skills for the given user
    public function getAllSkills(int $user_id): array {
        $query = 'SELECT env_id, skill FROM skill WHERE user_id = :user_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('user_id', $user_id);
        $statement->execute();
        $skills = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $skills;
    }

    //Adds the given skills to the given user
    public function addSkills(int $user_id, array $skills): void {
        $query = 'INSERT IGNORE INTO skill (user_id, env_id, skill) VALUES (?, ?, ?)';
        $query .= str_repeat(', (?, ?, ?)', count($skills) - 1);
        $statement = $this->getDb()->prepare($query);

        //Creates a 1D array containing all the skills
        $insert = [];
        foreach ($skills as $skill){
            $insert[] = $user_id;
            $insert[] = $skill['env_id'];
            $insert[] = $skill['skill'];
        }

        $statement->execute($insert);
    }

    //Deletes all the user's skills
    public function deleteAllSkills(int $user_id): void {
        $query = 'DELETE FROM skill WHERE user_id = :user_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('user_id', $user_id);

        $statement->execute();
    }

    //Returns all the interests for the given user
    public function getAllInterests(int $user_id): array {
        $query = 'SELECT env_id, interest FROM interest WHERE user_id = :user_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('user_id', $user_id);
        $statement->execute();
        $interests = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $interests;
    }

    //Adds the given interests to the given user
    public function addInterests(int $user_id, array $interests): void {
        $query = 'INSERT IGNORE INTO interest (user_id, env_id, interest) VALUES (?, ?, ?)';
        $query .= str_repeat(', (?, ?, ?)', count($interests) - 1);
        $statement = $this->getDb()->prepare($query);

        //Creates a 1D array containing all the interests
        $insert = [];
        foreach ($interests as $interest){
            $insert[] = $user_id;
            $insert[] = $interest['env_id'];
            $insert[] = $interest['interest'];
        }

        $statement->execute($insert);
    }

    //Deletes all the user's interests
    public function deleteAllInterests(int $user_id): void {
        $query = 'DELETE FROM interest WHERE user_id = :user_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('user_id', $user_id);
        
        $statement->execute();
    }

    //Returns the given user's availability
    public function getAvailability(int $user_id): array {
        $query = 'SELECT day, time FROM availability WHERE user_id = :user_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('user_id', $user_id);
        $statement->execute();
        $availability = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $availability;
    }

    //Adds the given availabilities to the given user
    public function addAvailabilities(int $user_id, array $availabilities): void {
        $query = 'INSERT IGNORE INTO availability (user_id, day, time) VALUES (?, ?, ?)';
        $query .= str_repeat(', (?, ?, ?)', count($availabilities) - 1);
        $statement = $this->getDb()->prepare($query);

        //Creates a 1D array containing all the availabilities
        $insert = [];
        foreach ($availabilities as $availability){
            $insert[] = $user_id;
            $insert[] = $availability['day'];
            $insert[] = $availability['time'];
        }

        $statement->execute($insert);
    }

    //Deletes all the user's availabilities
    public function deleteAvailabilities(int $user_id): void {
        $query = 'DELETE FROM availability WHERE user_id = :user_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('user_id', $user_id);
        
        $statement->execute();
    }

    //Returns the team_id's that the user is in
    public function getUserTeamIDs(int $user_id): array {
        $query = 'SELECT team_id FROM team_member WHERE user_id = :user_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('user_id', $user_id);
        $statement->execute();
        $teams = $statement->fetchAll(\PDO::FETCH_COLUMN, 0);

        return $teams;
    }

    //Returns the user's teams and their status on them
    public function getUserTeamStatuses(int $user_id): array {
        $query = 'SELECT team_id, status FROM team_member WHERE user_id = :user_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('user_id', $user_id);
        $statement->execute();
        $teams = $statement->fetchAll(\PDO::FETCH_KEY_PAIR);

        return $teams;
    }

    //Adds the given teams to the given user
    public function addTeamMember(int $team_id, int $user_id): void {
        $query = 'INSERT INTO team_member (team_id, user_id) VALUES (:team, :user)';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('team', $team_id);
        $statement->bindParam('user', $user_id);

        $statement->execute();
    }

    public function getAll(): array
    {
        $query = 'SELECT * FROM `user` ORDER BY `user_id`';
        $statement = $this->getDb()->prepare($query);
        $statement->execute();
        return (array) $statement->fetchAll();
    }
}
