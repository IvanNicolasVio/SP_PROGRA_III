<?php 

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class CheckFechaMW
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $parametros = $request->getQueryParams();
        if (isset($parametros['fecha']) && !empty($parametros['fecha'])) {
            if (!$this->fechaValida($parametros['fecha'])) {
                $response = new Response();
                $response->getBody()->write(json_encode(['Error!' => 'Formato de fecha invalido']));
                return $response->withHeader('Content-Type', 'application/json');
            }
        }
        return $handler->handle($request);
    }

    private function fechaValida($fecha, $formato = 'Y-m-d'): bool
    {
        $d = DateTime::createFromFormat($formato, $fecha);
        return $d && $d->format($formato) === $fecha;
    }
}