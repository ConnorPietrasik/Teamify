<?php

declare(strict_types=1);

namespace App\Controller\Team;

use App\Exception\TeamException;
use App\Helper\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class DeleteTeam extends Base {
    public function __invoke(Request $request, Response $response, array $args): Response {

        $team_id = (int) $args['team_id'];
        $this->getTeamService()->deleteTeam($team_id);

        //Removes the team from logged in user's rights list
        unset($_SESSION['teams'][$team_id]);

        return JsonResponse::withJson($response, '', 200);
    }
}
