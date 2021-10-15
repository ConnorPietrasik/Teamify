<?php

declare(strict_types=1);

namespace App\Controller\Team;

use App\Exception\TeamException;
use App\Helper\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class CreateTeam extends Base {
    public function __invoke(Request $request, Response $response, array $args): Response {
        $input = (array) $request->getParsedBody();

        $input['env_id'] = $args['env_id'];
        $input['user_id'] = $_SESSION['user_id'];

        $team = $this->getTeamService()->createTeam($input);
        return JsonResponse::withJson($response, (string) json_encode($team), 201);
    }
}
