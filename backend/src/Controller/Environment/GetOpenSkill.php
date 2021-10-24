<?php

declare(strict_types=1);

namespace App\Controller\Environment;

use App\Exception\EnvException;
use App\Helper\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class GetOpenSkill extends Base {
    public function __invoke(Request $request, Response $response, array $args): Response {
        $input = (array) $request->getParsedBody();


        $users = $this->getEnvService()->getOpenUsersBySkill((int) $args['env_id'], $input['skills']);
        return JsonResponse::withJson($response, (string) json_encode($users), 200);
    }
}
