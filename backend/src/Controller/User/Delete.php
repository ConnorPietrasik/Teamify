<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Helper\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Delete extends Base {

    public function __invoke(Request $request, Response $response): Response {

        //Deletes the user from the database and then destroys the session (to log them out)
        $this->getUserService()->deleteUser($_SESSION['user_id']);

        //Have to do the session_status() thing for phpunit
        if (session_status() == PHP_SESSION_ACTIVE) session_destroy();
        else unset($_SESSION['user_id']);

        return JsonResponse::withJson($response, '', 200);
    }
}
