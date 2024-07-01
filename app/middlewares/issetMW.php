<?php

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
class issetMW{

    private $tipo;

    public function __construct($tipo)
    {
        $this->tipo = $tipo;
    }
    public function __invoke(Request $request, RequestHandler $handler){
        $params = $request->getMethod() === 'POST' ? $request->getParsedBody() : $request->getQueryParams();

        if($this->tipo == 'dispositivo'){
            if(isset($params['nombre']) && isset($params['precio']) && isset($params['tipo']) && isset($params['marca'])&& isset($params['stock']) && isset($_FILES['foto']))
            {
                $response = $handler->handle($request);
            }
            else
            {
                $response = new Response();
                $response->getBody()->write(json_encode(array('Error!'=>'Parametros equivocados')));
            }
            return $response;

        }else if($this->tipo == 'consulta'){
            if(isset($params['nombre']) && isset($params['tipo']) && isset($params['marca']))
            {
                $response = $handler->handle($request);
            }
            else
            {
                $response = new Response();
                $response->getBody()->write(json_encode(array('Error!'=>'Parametros equivocados')));
            }
            return $response;
        }else if($this->tipo == 'venta'){
            if(isset($params['email']) && isset($params['nombre']) && isset($params['tipo']) && isset($params['marca']) && isset($params['stock']) && isset($_FILES['foto']))
            {
                $response = $handler->handle($request);
            }
            else
            {
                $response = new Response();
                $response->getBody()->write(json_encode(array('Error!'=>'Parametros equivocados')));
            }
            return $response;
        }else if($this->tipo == 'usuario'){
            if(isset($params['email']))
            {
                $response = $handler->handle($request);
            }
            else
            {
                $response = new Response();
                $response->getBody()->write(json_encode(array('Error!'=>'Parametros equivocados')));
            }
            return $response;
        }else if($this->tipo == 'tipo'){
            if(isset($params['tipo']))
            {
                $response = $handler->handle($request);
            }
            else
            {
                $response = new Response();
                $response->getBody()->write(json_encode(array('Error!'=>'Parametros equivocados')));
            }
            return $response;
        }else if($this->tipo == 'valor'){
            if(isset($params['valorUno']) && isset($params['valorDos']))
            {
                $response = $handler->handle($request);
            }
            else
            {
                $response = new Response();
                $response->getBody()->write(json_encode(array('Error!'=>'Parametros equivocados')));
            }
            return $response;
        }
    }
}
