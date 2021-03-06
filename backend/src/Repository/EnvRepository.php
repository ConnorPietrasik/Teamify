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

    //Returns true if the given user is open in the given environment, false otherwise
    public function isOpen(int $env_id, int $user_id): bool {
        $query = 'SELECT COUNT(*) FROM env_open WHERE env_id = :env_id AND user_id = :user_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('env_id', $env_id);
        $statement->bindParam('user_id', $user_id);

        $statement->execute();
        return (bool) $statement->fetchColumn();
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
        $query = 'SELECT status FROM team_member WHERE user_id = :user_id AND team_id = :team_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('user_id', $user_id);
        $statement->bindParam('team_id', $team_id);

        $statement->execute();
        $status = $statement->fetchColumn();
        return ($status === false) ? -1 : $status;
    }

    //Returns the data from the user table with the given ID
    public function getUser(int $user_id): array {
        $query = 'SELECT * FROM user WHERE user_id = :id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $user_id);

        $statement->execute();
        $user = $statement->fetch(\PDO::FETCH_ASSOC);
        if (! $user) throw new UserException('User not found.', 404);
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

    //Returns the user's status in the environment, or -1 if it doesn't exist
    public function getUserEnvStatus(int $user_id, int $env_id): int {
        $query = 'SELECT status FROM user_environment WHERE user_id = :user_id AND env_id = :env_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('user_id', $user_id);
        $statement->bindParam('env_id', $env_id);

        $statement->execute();
        $status = $statement->fetchColumn();
        return ($status === false) ? -1 : $status;
    }

    //Creates the environment with given specs
    public function createEnv(string $name, string $code): int {
        $query = 'INSERT INTO environment (name, code) VALUES (:name, :code)';
        $statement = $this->getDb()->prepare($query);
        $statement->bindValue('name', $name);
        $statement->bindValue('code', $code);

        $statement->execute();
        return (int) $this->getDb()->lastInsertId();
    }

    //Adds the user to the environment with given status
    public function addEnvMember(int $env_id, int $user_id, int $status = 0): void {
        $query = 'INSERT INTO user_environment (env_id, user_id, status) VALUES (:env_id, :user_id, :status)';
        $statement = $this->getDb()->prepare($query);
        $statement->bindValue('env_id', $env_id);
        $statement->bindValue('user_id', $user_id);
        $statement->bindValue('status', $status);

        $statement->execute();
    }

    //Returns the environment's ID from the code, or -1 if it doesn't exist
    public function getEnvIDByCode(string $code): int {
        $query = 'SELECT env_id FROM environment WHERE code = :code';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('code', $code);

        $statement->execute();
        $id = $statement->fetchColumn();
        return (!$id) ? -1 : $id;
    }

    //Deletes the given environment
    public function deleteEnv(int $env_id): void {
        $query = 'DELETE FROM environment WHERE env_id = :env_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('env_id', $env_id);
        
        $statement->execute();
    }

    //Returns the status of the environment member or -1
    public function getEnvMemberStatus(int $env_id, int $user_id): int {
        $query = 'SELECT status FROM user_environment WHERE env_id = :env_id AND user_id = :user_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('env_id', $env_id);
        $statement->bindParam('user_id', $user_id);

        $statement->execute();
        $status = $statement->fetchColumn();
        return ($status === false) ? -1 : $status;
    }

    //Returns the team ids from list that match at least one skill or interest
    public function getMatchingTeamIDs(array $team_ids, array $skills, array $interests): array {
        $teamQs = (count($team_ids) > 0) ? '?'.str_repeat(', ?', count($team_ids) - 1) : '\'\'';
        $skillQs = (count($skills) > 0) ? '?'.str_repeat(', ?', count($skills) - 1) : '\'\'';
        $interestQs = (count($interests) > 0) ? '?'.str_repeat(', ?', count($interests) - 1) : '\'\'';

        $query = 'SELECT DISTINCT team_id FROM team_tag WHERE team_id IN ('.$teamQs.') AND tag IN ('.$interestQs.') UNION '
                .'SELECT DISTINCT team_id FROM team_lf WHERE team_id IN ('.$teamQs.') AND skill IN ('.$skillQs.')';
        $statement = $this->getDb()->prepare($query);

        $args = [];
        foreach ($team_ids as $id) $args[] = $id;
        foreach ($interests as $tag) $args[] = $tag;
        foreach ($team_ids as $id) $args[] = $id;
        foreach ($skills as $skill) $args[] = $skill;

        $statement->execute((count($args) > 0) ? $args : null);
        $teams = $statement->fetchAll(\PDO::FETCH_COLUMN, 0);
        return $teams;
    }

    //Returns the name of the environment
    public function getEnvName(int $env_id): string {
        $query = 'SELECT name FROM environment WHERE env_id = :env_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('env_id', $env_id);

        $statement->execute();
        $name = $statement->fetchColumn();
        return ($name === false) ? ''.$env_id : $name;
    }
}
