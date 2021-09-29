<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Helper\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class GetWhole extends Base {

    public function __invoke(Request $request, Response $response, array $args): Response {
        $user = $this->getUserService()->getWholeUser((int) $args['id']);

        return JsonResponse::withJson($response, (string) json_encode($user));
    }
}
