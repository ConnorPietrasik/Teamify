<?php

declare(strict_types=1);

namespace App\Controller\Team;

use App\Exception\TeamException;
use App\Helper\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class UpdateTeam extends Base {
    public function __invoke(Request $request, Response $response, array $args): Response {
        $input = (array) $request->getParsedBody();

        $team_id = $this->getTeamService()->updateTeam($args['team_id'], $input);

        $ret = array('team_id' => $team_id);
        return JsonResponse::withJson($response, (string) json_encode($ret), 201);
    }
}
