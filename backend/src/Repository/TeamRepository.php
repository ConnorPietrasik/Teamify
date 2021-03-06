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

    //Returns the info from the team table
    public function getTeam(int $team_id){
        $query = 'SELECT * FROM team WHERE team_id = :id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $team_id);

        $statement->execute();
        $team = $statement->fetch(\PDO::FETCH_ASSOC);
        if (!$team) throw new TeamException('Team not found.', 404);
        return $team;
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
    
    //Updates the team table with given info
    public function updateTeam(int $team_id, array $team): void {
        $query = 'UPDATE team SET name = :name, description = :description WHERE team_id = :team_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('team_id', $team_id);
        $statement->bindValue('name', $team['name'] ?? null);
        $statement->bindValue('description', $team['description'] ?? null);

        $statement->execute();
    }

    //Deletes the team with given ID
    public function deleteTeam(int $team_id): void {
        $query = 'DELETE FROM team WHERE team_id = :team_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('team_id', $team_id);

        $statement->execute();
    }

    //Returns the team's environment ID
    public function getTeamEnvID(int $team_id): int {
        $query = 'SELECT env_id FROM team WHERE team_id = :team_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('team_id', $team_id);
        $statement->execute();
        $env_id = $statement->fetchColumn();

        return $env_id;
    }

    //Returns the user_ids and statuses of team members
    public function getMemberIDsAndStatuses(int $team_id): array {
        $query = 'SELECT user_id, status FROM team_member WHERE team_id = :team_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('team_id', $team_id);

        $statement->execute();
        $tags = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $tags;
    }

    //Returns the member's status, or -1 if they aren't in the given team
    public function getMemberStatus(int $team_id, int $user_id): int {
        $query = 'SELECT status FROM team_member WHERE team_id = :team_id AND user_id = :user_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('team_id', $team_id);
        $statement->bindParam('user_id', $user_id);

        $statement->execute();
        $status = $statement->fetchColumn();
        return ($status === false) ? -1 : $status;
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

    //Returns the team_id that matches the current user and environment, or -1 if it doesn't exist
    public function getEnvUserTeam(int $env_id, int $user_id): int {
        $query = 'SELECT team_id FROM team WHERE env_id = :env AND team_id IN (SELECT team_id FROM team_member WHERE user_id = :user)';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('env', $env_id);
        $statement->bindParam('user', $user_id);

        $statement->execute();
        $team = $statement->fetchColumn();
        return (!$team) ? -1 : $team;
    }

    //Adds the request to join the specified team
    public function addTeamRequest(int $team_id, int $user_id, string $message = null): void {
        $query = 'INSERT INTO team_request (team_id, user_id, status, message) VALUES (:team_id, :user_id, 0, :message)';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('team_id', $team_id);
        $statement->bindValue('user_id', $user_id);
        $statement->bindValue('message', $message ?? NULL);

        $statement->execute();
    } 

    //Returns all the unresolved requests to join the given team
    public function getTeamRequests(int $team_id): array {
        $query = 'SELECT user_id, status, message FROM team_request WHERE team_id = :team_id AND status = 0';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('team_id', $team_id);

        $statement->execute();
        $requests = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $requests;
    }

    //Returns all the requests to join the given team, including denied
    public function getAllTeamRequests(int $team_id): array {
        $query = 'SELECT user_id, status, message FROM team_request WHERE team_id = :team_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('team_id', $team_id);

        $statement->execute();
        $requests = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $requests;
    }

    //Updates the team request with the given status
    public function updateTeamRequest(int $team_id, int $user_id, int $status): void {
        $query = 'UPDATE team_request SET status = :status WHERE team_id = :team_id AND user_id = :user_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('team_id', $team_id);
        $statement->bindValue('user_id', $user_id);
        $statement->bindValue('status', $status);

        $statement->execute();
        if ($statement->rowCount() < 1) throw new TeamException("No matching request found", 409);
    }

    //Checks if a user has already requested to join a team
    public function getTeamUserReqStatus(int $team_id, int $user_id): int {
        $query = 'SELECT status FROM team_request WHERE team_id = :team_id AND user_id = :user_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('team_id', $team_id);
        $statement->bindParam('user_id', $user_id);

        $statement->execute();
        $status = $statement->fetchColumn();
        return ($status === false) ? -1 : $status;
    }

    //Deletes the user's requests for the given teams
    public function deleteTeamRequestsByUserAndEnv(int $user_id, int $env_id): void {
        $query = 'DELETE FROM team_request WHERE user_id = :user_id AND (status = 0 OR status = 1) AND team_id IN (SELECT team_id FROM team WHERE env_id = :env_id)';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('user_id', $user_id);
        $statement->bindParam('env_id', $env_id);

        $statement->execute();
    }

    //Invites the given user to the given team
    public function inviteUserTeam(int $team_id, int $user_id, string $message = null, int $inviter_id): void {
        $query = 'INSERT INTO team_invite (team_id, user_id, inviter_id, status, message) VALUES (:team_id, :user_id, :inviter_id, 0, :message)';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('team_id', $team_id);
        $statement->bindValue('user_id', $user_id);
        $statement->bindValue('inviter_id', $inviter_id);
        $statement->bindValue('message', $message ?? NULL);

        $statement->execute();
    }

    //Returns the unresolved invites for the given team
    public function getTeamInvites(int $team_id): array {
        $query = 'SELECT user_id, inviter_id, status, message FROM team_invite WHERE team_id = :team_id AND status = 0';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('team_id', $team_id);

        $statement->execute();
        $requests = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $requests;
    }

    //Returns all the invites for the given team, including denied
    public function getAllTeamInvites(int $team_id): array {
        $query = 'SELECT user_id, inviter_id, status, message FROM team_invite WHERE team_id = :team_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('team_id', $team_id);

        $statement->execute();
        $requests = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $requests;
    }

    //Updates the team request with the given status
    public function updateTeamInvite(int $team_id, int $user_id, int $status): void {
        $query = 'UPDATE team_invite SET status = :status WHERE team_id = :team_id AND user_id = :user_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('team_id', $team_id);
        $statement->bindValue('user_id', $user_id);
        $statement->bindValue('status', $status);

        $statement->execute();
        if ($statement->rowCount() < 1) throw new TeamException("No matching invite found", 409);
    }

    //Checks if a user has already been invited to a team
    public function getTeamUserInvStatus(int $team_id, int $user_id): int {
        $query = 'SELECT status FROM team_invite WHERE team_id = :team_id AND user_id = :user_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('team_id', $team_id);
        $statement->bindParam('user_id', $user_id);

        $statement->execute();
        $status = $statement->fetchColumn();

        return ($status === false) ? -1 : $status;
    }

    //Deletes the user's requests for the given teams
    public function deleteTeamInvitesByUserAndEnv(int $user_id, int $env_id): void {
        $query = 'DELETE FROM team_invite WHERE user_id = :user_id AND (status = 0 OR status = 1) AND team_id IN (SELECT team_id FROM team WHERE env_id = :env_id)';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('user_id', $user_id);
        $statement->bindParam('env_id', $env_id);

        $statement->execute();
    }

    //Kicks the member from the team
    public function kickMember(int $team_id, int $user_id): void {
        $query = 'DELETE FROM team_member WHERE team_id = :team_id AND user_id = :user_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('team_id', $team_id);
        $statement->bindParam('user_id', $user_id);

        $statement->execute();
    }
}
