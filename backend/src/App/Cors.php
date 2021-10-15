<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return static function (App $app): void {
    $app->options('/{routes:.+}', function (Request $request, Response $response) {
        return $response;
    });

    $app->add(function (Request $request, $handler): Response {
        $response = $handler->handle($request);

        $allowed_origins = ['https://teamify.pietrasik.top', 'http://teamify.pietrasik.top', 'localhost'];

        $origin = $request->getHeader('Origin');
        if (!empty($origin) && in_array($origin[0], $allowed_origins))
            return $response
                ->withHeader('Access-Control-Allow-Origin', $origin[0])
                ->withHeader('Access-Control-Allow-Credentials', 'true')
                ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                ->withHeader('Access-Control-Allow-Methods','GET, POST, PUT, DELETE, PATCH, OPTIONS');
        else return $response;
    });
};
