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

        $team_id = $this->teamRepository->createTeam($input);
        $this->teamRepository->addMember($team_id, $input['user_id'], 1);

        if (isset($input['tags'])) $this->teamRepository->addTags($team_id, $input['tags']);
        if (isset($input['looking_for'])) $this->teamRepository->addLookingFor($team_id, $input['looking_for']);

        return $team_id;
    }
}
