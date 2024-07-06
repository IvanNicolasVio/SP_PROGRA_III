<?php
require_once './models/Venta.php';
require_once './models/Dispositivo.php';
require_once './controllers/ImagenController.php';
include_once './models/ControlTiempo.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class VentaController
{
    public function Vender(Request $request, Response $response, $args)
    {
        $parametros = $request->getParsedBody();
        $email = $parametros['email'];
        $nombre = $parametros['nombre'];
        $tipo = $parametros['tipo'];
        $marca = $parametros['marca'];
        $stock = $parametros['stock'];
        $foto = isset($parametros['foto']) ? $parametros['foto'] : false;
        $dispositivo = Dispositivo::CheckYaCreado($nombre,$marca,$tipo);
        if($dispositivo && $dispositivo[0]['stock'] >= $stock){
            $id = $dispositivo[0]['id'];
            $nuevaVenta = new Venta($email,$nombre,$tipo,$marca,$stock,$dispositivo[0]['precio']);
            $ultimoId = $nuevaVenta->crearVenta();
            if(!$foto){
                $ruta = ImagenController::FotoVenta($nuevaVenta,$foto);
                $nuevaVenta->AgregarFoto($ruta,$ultimoId);
            }
            Dispositivo::DescontarStock($id,$stock);
            $response->getBody()->write(json_encode(array('Status'=>'Se realizo la venta de ' . $nombre)));
        }else{
            $response->getBody()->write(json_encode(array('Error'=> 'Stock insuficiente')));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerPorFecha(Request $request, Response $response, $args){
        $parametros = $request->getQueryParams();
        if(!$parametros){
            $fecha = Fecha::DarFechaAyer();
        }else{
            $fecha = $parametros['fecha'];
        }
        $ventas = Venta::ConsultarVentasPorFecha($fecha);
        if($ventas){
            $response->getBody()->write(json_encode($ventas));
        }else{
            $response->getBody()->write(json_encode(array('Status'=>'No hay ventas registradas en esa fecha')));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerPorEmail(Request $request, Response $response, $args){
        $parametros = $request->getQueryParams();
        $email = $parametros['email'];
        $ventas = Venta::ConsultarVentasPorUsuario($email);
        if($ventas){
            $response->getBody()->write(json_encode($ventas));
        }else{
            $response->getBody()->write(json_encode(array('Status'=>'No hay ventas registradas de ese email')));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerPorTipo(Request $request, Response $response, $args){
        $parametros = $request->getQueryParams();
        $tipo = $parametros['tipo'];
        $ventas = Venta::ConsultarVentasPorTipo($tipo);
        if($ventas){
            $response->getBody()->write(json_encode($ventas));
        }else{
            $response->getBody()->write(json_encode(array('Status'=>'No hay ventas registradas de ese tipo')));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerPorValor(Request $request, Response $response, $args){
        $parametros = $request->getQueryParams();
        $valorUno = $parametros['valorUno'];
        $valorDos = $parametros['valorDos'];
        $ventas = Venta::ConsultarVentasPorValor($valorUno,$valorDos);
        if($ventas){
            $response->getBody()->write(json_encode($ventas));
        }else{
            $response->getBody()->write(json_encode(array('Status'=>'No hay ventas registradas entre ese rango')));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerPorPrecioFinal(Request $request, Response $response, $args){
        $parametros = $request->getQueryParams();
        if(!$parametros){
            $resultado = Venta::ConsultarPrecioTotal();
            if($resultado){
                $response->getBody()->write(json_encode(array('Ganancia total'=>$resultado['precio_final'])));
            }else{
                $response->getBody()->write(json_encode(array('Status'=>'No hay ventas registradas')));
            }
        }else{
            $fecha = $parametros['fecha'];
            $resultado = Venta::ConsultarPrecioTotalPorFecha($fecha);
            if($resultado){
                $response->getBody()->write(json_encode(array('Ganancia del dia'=>$resultado['precio_final'])));
            }else{
                $response->getBody()->write(json_encode(array('Status'=>'No hay ventas registradas en esa fecha')));
            }
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerDispMasVendido(Request $request, Response $response, $args){
        $producto = Venta::ConsultarProductoMasVendido();
        if($producto){
            $response->getBody()->write(json_encode(array('Producto mas vendido'=>$producto['nombre_disp'] . ' cantidad ' . $producto['cantidad_vendida'])));
        }else{
            $response->getBody()->write(json_encode(array('Status'=>'No hay ventas registradas')));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function modificarVenta(Request $request, Response $response, $args){
        $parametros = $request->getQueryParams();
        $id = $parametros['id'];
        $email = $parametros['email'];
        $nombre_disp = $parametros['nombre'];
        $tipo = $parametros['tipo'];
        $marca = $parametros['marca'];
        $stock = $parametros['stock'];
        $producto = Venta::ConsultarVentasPorId($id);
        if($producto){
            Venta::ModificarVenta($id,$email,$nombre_disp,$tipo,$marca,$stock);
            $response->getBody()->write(json_encode(array('Status'=>'Se actualizo la venta N ' . $id)));
        }else{
            $response->getBody()->write(json_encode(array('Status'=>'No existe esa venta')));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function Descargar(Request $request, Response $response, $args){
        $ventas = Venta::TraerTodas();
        if($ventas){
            $filename = "ventas.csv";
            $file = fopen('php://memory', 'w');
            fputcsv($file, ['email', 'nombre_disp', 'tipo', 'marca', 'stock', 'fecha', 'precio_total', 'ruta_foto']);
            foreach ($ventas as $venta) {
                unset($venta['id']);
                fputcsv($file, $venta);
            }
            fseek($file, 0);
            $response = $response->withHeader('Content-Type', 'text/csv')
                                    ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
            $response->getBody()->write(stream_get_contents($file));
            fclose($file);
        }else {
            $response->getBody()->write(json_encode(array('Status' => 'No existen ventas')));
            $response->withStatus(404);
        }   
        return $response;
    }
}