<?php

declare(strict_types=1);

namespace App\Controller\Team;

use App\Exception\TeamException;
use App\Helper\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class GetRequests extends Base {
    public function __invoke(Request $request, Response $response, array $args): Response {

        $requests = $this->getTeamService()->getTeamRequests((int) $args['team_id']);
        $env_id = $this->getTeamService()->getTeamEnvironmentID((int) $args['team_id']);
        foreach ($requests as &$req) {
            $req['user'] = $this->getEnvService()->getEnvUser($env_id, $req['user_id']);
            unset($req['user_id']);
        }

        return JsonResponse::withJson($response, (string) json_encode($requests), 200);
    }
}
