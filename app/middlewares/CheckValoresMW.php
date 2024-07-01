<?php

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class CheckValoresMW
{
    public function __invoke(Request $request, RequestHandler $handler)
    {
        $params = $request->getMethod() === 'POST' ? $request->getParsedBody() : $request->getQueryParams();
        $valorUno = $params['valorUno'];
        $valorDos = $params['valorDos'];
        if (!is_numeric($valorUno) || !is_numeric($valorDos)) {
            $response = new Response();
            $response->getBody()->write(json_encode(['Error' => 'Ambos valores deben ser numéricos']));
            return $response->withHeader('Content-Type', 'application/json');
        }
        if ($valorUno >= $valorDos) {
            $response = new Response();
            $response->getBody()->write(json_encode(['Error' => 'valorUno debe ser menor que valorDos']));
            return $response->withHeader('Content-Type', 'application/json');
        }
        return $handler->handle($request);
    }
}