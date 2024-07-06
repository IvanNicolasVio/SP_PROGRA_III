<?php

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
class CheckPerfilMW{

    public function __invoke(Request $request, RequestHandler $handler){
        $params = $request->getMethod() === 'POST' ? $request->getParsedBody() : $request->getQueryParams();

        if($params['perfil'] === 'cliente' || $params['perfil'] === 'empleado' || $params['perfil'] === 'admin')
        {
            $response = $handler->handle($request);
        }
        else
        {
            $response = new Response();
            $response->getBody()->write(json_encode(array('Error!'=>'perfil inexistente')));
        }
        return $response;
    }
}