<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Exception\AuthException;
use App\Helper\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Logout extends Base {
    public function __invoke(Request $request, Response $response): Response {

        //Being logged in is in the session, so destroying the session = logging out
        //Have to do the session_status() thing for phpunit
        if (session_status() == PHP_SESSION_ACTIVE) session_destroy();
        else unset($_SESSION['user_id']);
        return JsonResponse::withJson($response, '', 200);
    }
}
