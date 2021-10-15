<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exception\TeamException;

final class TeamRepository {
    private \PDO $database;

    public function __construct(\PDO $database) {
        $this->database = $database;
    }

    public function getDb(): \PDO {
        return $this->database;
    }

    //Creates the team and returns its ID
    public function createTeam(array $team): int {
        $query = 'INSERT INTO team (env_id, name, description) VALUES (:env_id, :name, :description)';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('env_id', $team['env_id']);
        $statement->bindValue('name', $team['name'] ?? null);
        $statement->bindValue('description', $team['description'] ?? null);

        $statement->execute();
        return (int) $this->getDb()->lastInsertId();
    }

    //Adds the member to the team with the given status (0 means member, 1 means leader)
    public function addMember(int $team_id, int $user_id, int $status): void{
        $query = 'INSERT INTO team_member (team_id, user_id, status) VALUES (:team_id, :user_id, :status)';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('team_id', $team_id);
        $statement->bindValue('user_id', $user_id);
        $statement->bindValue('status', $status);

        $statement->execute();
    }

    //Returns the tags for the given team
    public function getTags(int $team_id): array {
        $query = 'SELECT tag FROM team_tag WHERE team_id = :team_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('team_id', $team_id);

        $statement->execute();
        $tags = $statement->fetchAll(\PDO::FETCH_COLUMN, 0);
        return $tags;
    }

    //Adds the given tags to the given team
    public function addTags(int $team_id, array $tags): void {
        $query = 'INSERT IGNORE INTO team_tag (team_id, tag) VALUES (?, ?)';
        $query .= str_repeat(', (?, ?)', count($tags) - 1);
        $statement = $this->getDb()->prepare($query);

        //Creates a 1D array containing all the tags
        $insert = [];
        foreach ($tags as $tag){
            $insert[] = $team_id;
            $insert[] = $tag;
        }

        $statement->execute($insert);
    }

    //Deletes all the team's tags
    public function deleteAllTags(int $team_id): void {
        $query = 'DELETE FROM team_tag WHERE team_id = :team_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('team_id', $team_id);

        $statement->execute();
    }

    //Returns the looking_for for the given team
    public function getLookingFor(int $team_id): array {
        $query = 'SELECT skill FROM team_lf WHERE team_id = :team_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('team_id', $team_id);

        $statement->execute();
        $skills = $statement->fetchAll(\PDO::FETCH_COLUMN, 0);
        return $skills;
    }

    //Adds the given looking_for to the given team
    public function addLookingFor(int $team_id, array $skills): void {
        $query = 'INSERT IGNORE INTO team_lf (team_id, skill) VALUES (?, ?)';
        $query .= str_repeat(', (?, ?)', count($skills) - 1);
        $statement = $this->getDb()->prepare($query);

        //Creates a 1D array containing all the looking_for
        $insert = [];
        foreach ($skills as $skill){
            $insert[] = $team_id;
            $insert[] = $skill;
        }

        $statement->execute($insert);
    }

    //Deletes all the team's looking_for
    public function deleteAllLookingFor(int $team_id): void {
        $query = 'DELETE FROM team_lf WHERE team_id = :team_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('team_id', $team_id);

        $statement->execute();
    }
}
