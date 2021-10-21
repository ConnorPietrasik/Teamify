<?php

declare(strict_types=1);

namespace App\Controller\Team;

use App\Exception\TeamException;
use App\Helper\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class RequestJoin extends Base {
    public function __invoke(Request $request, Response $response, array $args): Response {
        $input = (array) $request->getParsedBody();

        $this->getTeamService()->requestJoinTeam((int) $args['team_id'], $_SESSION['user_id'], $input['message'] ?? null);
        return JsonResponse::withJson($response, '', 200);
    }
}
