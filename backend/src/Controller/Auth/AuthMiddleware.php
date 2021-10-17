<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Exception\AuthException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthMiddleware {

    public function __invoke(Request $request, RequestHandler $handler): Response {

        //Throws an exception when a user tries to access something that requires being logged in while not being logged in
        if (!isset($_SESSION['user_id'])) throw new AuthException('User not logged in', 401);

        $response = $handler->handle($request);
        return $response;
    }
}