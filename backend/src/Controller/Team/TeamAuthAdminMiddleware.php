<?php

declare(strict_types=1);

namespace App\Controller\Team;

use App\Exception\TeamException;
use App\Exception\AuthException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

class TeamAuthAdminMiddleware {

    public function __invoke(Request $request, RequestHandler $handler): Response {

        //Makes sure the user is logged in
        if (!isset($_SESSION['user_id'])) throw new AuthException('User not logged in', 401);
        
        $team_id = (int) RouteContext::fromRequest($request)->getRoute()->getArgument('team_id');

        $status = $_SESSION['teams'][$team_id] ?? -1;
        if ($status != 1) throw new TeamException("User does not have admin permissions for team", 401);

        $response = $handler->handle($request);
        return $response;
    }
}