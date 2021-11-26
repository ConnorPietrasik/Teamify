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

    //Returns all the users in the environment
    public function getAllEnvUserIDs(int $env_id): array {
        $query = 'SELECT user_id FROM user_environment WHERE env_id = :id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $env_id);
        $statement->execute();
        $users = $statement->fetchAll(\PDO::FETCH_COLUMN, 0);

        return $users;
    }

    //Returns all the IDs open that match one of the given skills
    public function getOpenSkillIDs(int $env_id, array $skills): array {
        $query = 'SELECT DISTINCT user_id FROM skill WHERE (env_id = ? OR env_id = 0) AND user_id IN (SELECT user_id FROM env_open WHERE env_id = ?) AND skill IN (?';
        $query .= str_repeat(', ?', count($skills) - 1);
        $query .= ')';
        $statement = $this->getDb()->prepare($query);

        $args[] = $env_id;
        $args[] = $env_id;
        foreach ($skills as $skill) $args[] = $skill;

        $statement->execute($args);
        $users = $statement->fetchAll(\PDO::FETCH_COLUMN, 0);

        return $users;
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

    //Adds the user to the environment's open list
    public function addOpen(int $env_id, int $user_id): void {
        $query = 'INSERT INTO env_open (env_id, user_id) VALUES (:env, :user)';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('env', $env_id);
        $statement->bindParam('user', $user_id);

        $statement->execute();
    }

    //Removes the user from the environment's open list
    public function removeOpen(int $env_id, int $user_id): void {
        $query = 'DELETE FROM env_open WHERE env_id = :env AND user_id = :user';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('env', $env_id);
        $statement->bindParam('user', $user_id);
        
        $statement->execute();
    }

    //Returns all the skills for the user matching the environment
    public function getEnvSkills(int $env_id, int $user_id): array {
        $query = 'SELECT skill FROM skill WHERE user_id = :user AND (env_id = :env OR env_id = 0)';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('env', $env_id);
        $statement->bindParam('user', $user_id);
        $statement->execute();
        $skills = $statement->fetchAll(\PDO::FETCH_COLUMN, 0);

        return $skills;
    }

    //Returns the user's availability
    public function getAvailability(int $user_id): array {
        $query = 'SELECT day, time FROM availability WHERE user_id = :id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $user_id);
        $statement->execute();
        $availability = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $availability;
    }

    //Returns all the interests for the user matching the environment
    public function getEnvInterests(int $env_id, int $user_id): array {
        $query = 'SELECT interest FROM interest WHERE user_id = :user AND (env_id = :env OR env_id = 0)';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('env', $env_id);
        $statement->bindParam('user', $user_id);
        $statement->execute();
        $skills = $statement->fetchAll(\PDO::FETCH_COLUMN, 0);

        return $skills;
    }

    //Returns the team_id that matches the current user and environment, or -1 if it doesn't exist
    public function getEnvUserTeamID(int $env_id, int $user_id): int {
        $query = 'SELECT team_id FROM team WHERE env_id = :env AND team_id IN (SELECT team_id FROM team_member WHERE user_id = :user)';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('env', $env_id);
        $statement->bindParam('user', $user_id);
        $statement->execute();
        $team = $statement->fetchColumn();

        return (!$team) ? -1 : $team;
    }

    //Returns the user's status on the given team, or -1 if it doesn't exist
    public function getUserTeamStatus(int $user_id, int $team_id): int {
        throw new EnvException("TESTING: Team id: ".$team_id, 500);
        $query = 'SELECT status FROM team_member WHERE user_id = :user_id AND team_id = :team_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('user_id', $user_id);
        $statement->bindParam('team_id', $team_id);
        $statement->execute();
        $status = $statement->fetchColumn();

        return (!$status) ? -1 : $status;
    }

    //Returns the data from the user table with the given ID
    public function getUser(int $user_id): array {
        $query = 'SELECT * FROM user WHERE user_id = :id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $user_id);
        $statement->execute();
        $user = $statement->fetch(\PDO::FETCH_ASSOC);
        if (! $user) {
            throw new UserException('User not found.', 404);
        }

        return $user;
    }

    //Returns the IDs of all teams in the environment
    public function getAllEnvTeamIDs(int $env_id): array {
        $query = 'SELECT team_id FROM team WHERE env_id = :id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $env_id);
        $statement->execute();
        $teams = $statement->fetchAll(\PDO::FETCH_COLUMN, 0);

        return $teams;
    }
}
