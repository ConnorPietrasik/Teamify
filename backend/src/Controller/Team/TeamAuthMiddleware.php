<?php

declare(strict_types=1);

namespace App\Controller\Team;

use App\Exception\TeamException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

class TeamAuthMiddleware {

    public function __invoke(Request $request, RequestHandler $handler): Response {

        //Makes sure the user is logged in
        if (!isset($_SESSION['user_id'])) throw new AuthException('User not logged in', 401);
        
        $team_id = (int) RouteContext::fromRequest($request)->getRoute()->getArgument('team_id');

        throw new TeamException(var_export($_SESSION['teams']), 400);
        $status = $_SESSION['teams'][$team_id] ?? -1;
        if ($status != 1) throw new TeamException("User does not have admin permissions for team", 401);

        $response = $handler->handle($request);
        return $response;
    }
}