<?php

declare(strict_types=1);

namespace App\Controller\Team;

use App\Exception\TeamException;
use App\Exception\AuthException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

class TeamAuthMemberMiddleware {

    public function __invoke(Request $request, RequestHandler $handler): Response {

        //Makes sure the user is logged in
        if (!isset($_SESSION['user_id'])) throw new AuthException('User not logged in', 401);
        
        $team_id = (int) RouteContext::fromRequest($request)->getRoute()->getArgument('team_id');

        if (!isset($_SESSION['teams'][$team_id])) throw new TeamException('User not a member of team', 401);

        $response = $handler->handle($request);
        return $response;
    }
}