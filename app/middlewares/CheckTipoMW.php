<?php

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
class CheckTipoMW{

    public function __invoke(Request $request, RequestHandler $handler){
        $params = $request->getMethod() === 'POST' ? $request->getParsedBody() : $request->getQueryParams();

        if($params['tipo'] === 'Smartphone' || $params['tipo'] === 'Tablet')
        {
            $response = $handler->handle($request);
        }
        else
        {
            $response = new Response();
            $response->getBody()->write(json_encode(array('Error!'=>'Tipo inexistente')));
        }
        return $response;
    }
}