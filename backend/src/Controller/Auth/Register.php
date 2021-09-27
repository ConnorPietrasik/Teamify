<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Helper\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Register extends Base {
    public function __invoke(Request $request, Response $response): Response {
        $input = (array) $request->getParsedBody();

        $user_id = $this->getAuthService()->createUser($input);
        if (isset($input['password'])) $this->getAuthService()->addPassword($user_id, $input['password']);

        //Logs the user in as well
        $_SESSION['user_id'] = $user_id;
        $_SESSION['last_login'] = time();

        $user = array('user_id' => $user_id);
        return JsonResponse::withJson($response, (string) json_encode($user), 201);
    }
}
