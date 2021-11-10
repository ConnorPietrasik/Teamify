<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Exception\UserException;
use App\Helper\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class GetTeamInvites extends Base {
    public function __invoke(Request $request, Response $response): Response {

        $invites = $this->getUserService()->getUserTeamInvites((int) $_SESSION['user_id']);
        foreach ($invites as &$inv) {
            $inv['team'] = $this->getTeamService()->getTeam($inv['team_id']);
            $env_id = $this->getTeamService()->getTeamEnvID($inv['team_id']);
            unset($inv['team_id']);

            foreach ($inv['team']['members'] as &$member){
                $member['user'] = $this->getEnvService()->getEnvUser($env_id, $member['user_id']);
                unset($member['user_id']);
            }
        }

        return JsonResponse::withJson($response, (string) json_encode($invites), 200);
    }
}
