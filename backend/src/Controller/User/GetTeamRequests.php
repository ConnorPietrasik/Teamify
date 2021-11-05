<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Exception\UserException;
use App\Helper\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class GetTeamRequests extends Base {
    public function __invoke(Request $request, Response $response): Response {

        $requests = $this->getUserService()->getUserTeamRequests((int) $_SESSION['user_id']);
        foreach ($requests as &$req) {
            $req['team'] = $this->getTeamService()->getTeam($req['team_id']);
            $env_id = $this->getTeamService()->getTeamEnvID($req['team_id']);
            unset($req['team_id']);

            foreach ($req['team']['members'] as &$member){
                $member['user'] = $this->getEnvService()->getEnvUser($env_id, $member['user_id']);
                unset($member['user_id']);
            }
        }

        return JsonResponse::withJson($response, (string) json_encode($requests), 200);
    }
}
