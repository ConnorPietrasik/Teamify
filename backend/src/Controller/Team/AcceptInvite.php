<?php

declare(strict_types=1);

namespace App\Controller\Team;

use App\Exception\TeamException;
use App\Helper\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class AcceptInvite extends Base {
    public function __invoke(Request $request, Response $response, array $args): Response {

        $this->getTeamService()->acceptInvite((int) $args['team_id'], (int) $_SESSION['user_id']);
        $this->getEnvService()->removeOpen($this->getTeamService()->getTeamEnvID((int) $args['team_id']), (int) $_SESSION['user_id']);
        return JsonResponse::withJson($response, '', 200);
    }
}
