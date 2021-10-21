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
        $teams = array();
        foreach $ids as $id $teams[] = $this->getTeamService()->getTeam($id);

        return JsonResponse::withJson($response, (string) json_encode($teams), 200);
    }
}
