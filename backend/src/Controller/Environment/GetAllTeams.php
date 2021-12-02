<?php

declare(strict_types=1);

namespace App\Controller\Environment;

use App\Exception\EnvException;
use App\Helper\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class GetAllTeams extends Base {
    public function __invoke(Request $request, Response $response, array $args): Response {

        $ids = $this->getEnvService()->getAllTeamIDs((int) $args['env_id']);

        //Removes the ones with an invite / a request
        $reqs_full = $this->getUserService()->getUserTeamRequests((int) $_SESSION['user_id']);
        $reqs = array_column($reqs_full, 'team_id');
        $invs_full = $this->getUserService()->getUserTeamInvites((int) $_SESSION['user_id']);
        $invs = array_column($invs_full, 'team_id');

        foreach($ids as $i=>$val) if (in_array($val, $reqs) || in_array($val, $invs)) unset($ids[$i]);

        //Gets the teams from the ids
        $teams = array();
        foreach ($ids as $id) $teams[] = $this->getTeamService()->getTeam($id);

        foreach ($teams as &$team){
            foreach ($team['members'] as &$member){
                $member['user'] = $this->getEnvService()->getEnvUser((int) $args['env_id'], $member['user_id']);
                 unset($member['user_id']);
            }
        }

        return JsonResponse::withJson($response, (string) json_encode($teams), 200);
    }
}
