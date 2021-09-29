<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Helper\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Update extends Base{
    
    public function __invoke(Request $request, Response $response): Response {
        $input = (array) $request->getParsedBody();
        $this->getUserService()->update($input, $_SESSION['user_id']);

        return JsonResponse::withJson($response, '', 200);
    }
}
