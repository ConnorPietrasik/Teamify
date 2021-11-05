<?php

declare(strict_types=1);

namespace App\Controller\Team;

use App\Exception\TeamException;
use App\Helper\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class GetTeam extends Base {
    public function __invoke(Request $request, Response $response, array $args): Response {

        $team = $this->getTeamService()->getTeam((int) $args['team_id']);
        
        $env_id = $this->getTeamService()->getTeamEnvID((int) $args['team_id']);

        foreach ($team['members'] as &$member){
            $member['user'] = $this->getEnvService()->getEnvUser($env_id, $member['user_id']);
            unset($member['user_id']);
        }

        return JsonResponse::withJson($response, (string) json_encode($team), 200);
    }
}
