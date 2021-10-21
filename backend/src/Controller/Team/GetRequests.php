<?php

declare(strict_types=1);

namespace App\Controller\Team;

use App\Exception\TeamException;
use App\Helper\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class GetRequests extends Base {
    public function __invoke(Request $request, Response $response, array $args): Response {

        $team = $this->getTeamService()->getTeamRequests((int) $args['team_id']);
        return JsonResponse::withJson($response, (string) json_encode($team), 200);
    }
}
