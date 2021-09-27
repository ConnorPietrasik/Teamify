<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Helper\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class CheckAuth extends Base {
    public function __invoke(Request $request, Response $response): Response {
        //If it gets here then it passed the auth middleware, so logged in
        $user = array('user_id' => $_SESSION['user_id']);
        return JsonResponse::withJson($response, (string) json_encode($user), 200);
    }
}
