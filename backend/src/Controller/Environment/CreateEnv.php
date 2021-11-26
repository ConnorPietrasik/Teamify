<?php

declare(strict_types=1);

namespace App\Controller\Environment;

use App\Exception\EnvException;
use App\Helper\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class CreateEnv extends Base {
    public function __invoke(Request $request, Response $response, array $args): Response {
        $input = (array) $request->getParsedBody();

        $env_id = $this->getEnvService()->createEnv((int) $_SESSION['user_id'], $input['name'], $input['code']);

        $env = array('env_id' => $env_id);
        return JsonResponse::withJson($response, (string) json_encode($env), 201);
    }
}
