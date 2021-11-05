<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\TeamException;
use App\Repository\TeamRepository;

final class TeamService {
    private TeamRepository $teamRepository;

    public function __construct(TeamRepository $teamRepository) {
        $this->teamRepository = $teamRepository;
    }

    //Creates the given team and returns its ID. $input req: env_id, user_id; optional: name, desc, tags, looking_for
    public function createTeam(array $input): int {

        if ($this->teamRepository->getEnvUserTeam($input['env_id'], $input['user_id']) != -1)
            throw new TeamException("User already in a team for this environment", 409);

        $team_id = $this->teamRepository->createTeam($input);
        $this->teamRepository->addMember($team_id, $input['user_id'], 1);

        if (isset($input['tags'])) $this->teamRepository->addTags($team_id, $input['tags']);
        if (isset($input['looking_for'])) $this->teamRepository->addLookingFor($team_id, $input['looking_for']);

        return $team_id;
    }

    //Returns the team with given ID
    public function getTeam(int $team_id): array {
        $team = $this->teamRepository->getTeam($team_id);
        $team['tags'] = $this->teamRepository->getTags($team_id);
        $team['looking_for'] = $this->teamRepository->getLookingFor($team_id);
        $team['members'] = $this->teamRepository->getMemberIDsAndStatuses($team_id);

        return $team;
    }

    //Returns the environment ID for the team
    public function getTeamEnvID(int $team_id): int {
        return $this->teamRepository->getTeamEnvID($team_id);
    }

    //Updates the team with given info
    public function updateTeam(int $team_id, array $input): void {
        $team = $this->teamRepository->getTeam($team_id);

        if (isset($input['name']) || isset($input['description'])){
            if (isset($input['name'])) $team['name'] = $input['name'];
            if (isset($input['description'])) $team['description'] = $input['description'];
            $this->teamRepository->updateTeam($team_id, $team);
        }
        
        if (isset($input['tags'])){
            $this->teamRepository->deleteAllTags($team_id);
            $this->teamRepository->addTags($team_id, $input['tags']);
        }

        if (isset($input['looking_for'])){
            $this->teamRepository->deleteAllLookingFor($team_id);
            $this->teamRepository->addLookingFor($team_id, $input['looking_for']);
        }
    }

    //Deletes the team with given ID
    public function deleteTeam(int $team_id): void {
        $this->teamRepository->deleteTeam($team_id);
    }

    //Returns the status of the team member, 1 = leader, 0 = member, -1 = not in team
    public function getTeamMemberLevel(int $team_id, int $user_id): int {
        return $this->teamRepository->getMemberStatus($team_id, $user_id);
    }

    //Adds the team request
    public function requestJoinTeam(int $team_id, int $user_id, string $message = null): void {
        $errCheck = $this->teamRepository->getEnvUserTeam($this->teamRepository->getTeamEnvID($team_id), $user_id);
        if ($errCheck != -1) throw new TeamException("User already in a team for this environment, team ID: ".$errCheck, 409);

        $this->teamRepository->addTeamRequest($team_id, $user_id, $message);
    }

    //Returns all the requests for the given team
    public function getTeamRequests(int $team_id): array {
        return $this->teamRepository->getTeamRequests($team_id);
    }

    //Accepts the given user into the given team
    public function acceptRequest(int $team_id, int $user_id): void {
        $this->teamRepository->updateTeamRequest($team_id, $user_id, 1);
        $this->teamRepository->addMember($team_id, $user_id, 0);
        $this->teamRepository->deleteTeamRequestsByUserAndEnv($user_id, $this->teamRepository->getTeamEnvID($team_id));
    }

    //Denies the user's request to join
    public function denyRequest(int $team_id, int $user_id): void {
        $this->teamRepository->updateTeamRequest($team_id, $user_id, 2);
    }

    //Invites the given user to the given team
    public function inviteUserTeam(int $team_id, int $user_id, string $message = null): void {
        $errCheck = $this->teamRepository->getEnvUserTeam($this->teamRepository->getTeamEnvID($team_id), $user_id);
        if ($errCheck != -1) throw new TeamException("User already in a team for this environment, team ID: ".$errCheck, 409);

        $this->teamRepository->inviteUserTeam($team_id, $user_id, $message, (int) $_SESSION['user_id']);
    }

    
}
