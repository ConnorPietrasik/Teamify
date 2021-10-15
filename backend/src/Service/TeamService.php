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

        if ($this->teamRepository->getEnvUserTeam($input['env_id'], $input['user_id']) != -1) throw new TeamException("User already in a team for this environment", 409);

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
}
