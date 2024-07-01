<?php 

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class CheckNumMW
{
    private $param;

    public function __construct($param)
    {
        $this->param = $param;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $parametros = $request->getMethod() === 'POST' ? $request->getParsedBody() : $request->getQueryParams();
        $parametroCheck = $parametros[$this->param];
        if(!is_numeric($parametroCheck)){
            $response = new Response();
            $response->getBody()->write(json_encode(['Error' => 'El valor debe ser numerico']));
            return $response->withHeader('Content-Type', 'application/json');
        }else{
            return $handler->handle($request);  
        }
    }
}