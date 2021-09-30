<?php

declare(strict_types=1);

namespace App\Controller\Environment;

use App\Exception\EnvException;
use App\Helper\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class RemoveOpen extends Base {
    public function __invoke(Request $request, Response $response, array $args): Response {

        $users = $this->getEnvService()->removeOpen((int) $args['env_id'], $_SESSION['user_id']);
        return JsonResponse::withJson($response, '', 200);
    }
}
