<?php

declare(strict_types=1);

namespace App\Controller\Team;

use App\Exception\TeamException;
use App\Helper\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class GetInvites extends Base {
    public function __invoke(Request $request, Response $response, array $args): Response {

        $invites = $this->getTeamService()->getTeamInvites((int) $args['team_id']);
        $env_id = $this->getTeamService()->getTeamEnvID((int) $args['team_id']);
        foreach ($invites as &$inv) {
            $inv['user'] = $this->getEnvService()->getEnvUser($env_id, $inv['user_id']);
            unset($inv['user_id']);
        }

        return JsonResponse::withJson($response, (string) json_encode($invites), 200);
    }
}
