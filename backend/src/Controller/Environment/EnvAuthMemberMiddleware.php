<?php

declare(strict_types=1);

namespace App\Controller\Environment;

use App\Exception\EnvException;
use App\Exception\AuthException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

class EnvAuthMemberMiddleware {

    public function __invoke(Request $request, RequestHandler $handler): Response {

        //Makes sure the user is logged in
        if (!isset($_SESSION['user_id'])) throw new AuthException('User not logged in', 401);
        
        $env_id = (int) RouteContext::fromRequest($request)->getRoute()->getArgument('env_id');

        if (!isset($_SESSION['environments'][$env_id])) throw new EnvException('User not a member of environment', 401);

        $response = $handler->handle($request);
        return $response;
    }
}