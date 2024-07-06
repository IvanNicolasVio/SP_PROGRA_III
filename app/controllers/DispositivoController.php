<?php
require_once './models/Dispositivo.php';
require_once './controllers/ImagenController.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class DispositivoController
{
    public function CargarUno(Request $request, Response $response, $args)
    {
        $parametros = $request->getParsedBody();
        $nombre = $parametros['nombre'];
        $precio = $parametros['precio'];
        $tipo = $parametros['tipo'];
        $marca = $parametros['marca'];
        $stock = $parametros['stock'];
        $foto = isset($parametros['foto']) ? $parametros['foto'] : false;
        $dispositivo = Dispositivo::CheckYaCreado($nombre,$marca,$tipo);
        if($dispositivo){
            $id = $dispositivo[0]['id'];
            $nombre = $dispositivo[0]['nombre'];
            Dispositivo::ActualizarDispositivo($id,$precio,$stock);
            $response->getBody()->write(json_encode(array('Status'=>$nombre . ' actualizado con exito!')));
        }else{
            $dispo = new Dispositivo($nombre,$precio,$tipo,$marca,$stock);
            $ultimoId = $dispo->crearDispositivo();
            if(!$foto){
                $ruta = ImagenController::FotoDispo($dispo,$foto);
                $dispo->AgregarFoto($ruta,$ultimoId);
            }
            $response->getBody()->write(json_encode(array('Status'=>$dispo->nombre . ' dado de alta con exito!')));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ConsultarDispositivo(Request $request, Response $response, $args){
        $parametros = $request->getParsedBody();
        $nombre = $parametros['nombre'];
        $tipo = $parametros['tipo'];
        $marca = $parametros['marca'];
        $banderaMarca = false;
        $banderaTipo = false;
        $dispositivos = Dispositivo::ConsultarDispositivo($nombre);
        if ($dispositivos) {
            foreach ($dispositivos as $dispositivo) {
                if($dispositivo['marca'] === $marca){
                    $banderaMarca = true;
                    if($dispositivo['tipo'] === $tipo){
                        $banderaTipo = true;
                        $response->getBody()->write(json_encode(array('Status'=>'Existe')));
                        return $response->withHeader('Content-Type', 'application/json');
                    }
                }
            }
            if($banderaMarca && !$banderaTipo){
                $response->getBody()->write(json_encode(array('Status'=>'No existe el tipo')));
                return $response->withHeader('Content-Type', 'application/json');
            }elseif(!$banderaMarca){
                $response->getBody()->write(json_encode(array('Status'=>'No existe la marca')));
                return $response->withHeader('Content-Type', 'application/json');
            }
        } else {
            $response->getBody()->write(json_encode(array('Status'=>'No existe el nombre')));
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public function CargarMuchos(Request $request, Response $response, $args)
    {
        $parametros = $request->getUploadedFiles();
        $archivoCsv = $parametros['csv'];
        if ($archivoCsv->getError() === UPLOAD_ERR_OK) {
            $filename = $archivoCsv->getClientFilename();
            $uploadDir = __DIR__ . '/../archivos/';
            $archivoCsv->moveTo($uploadDir . $filename);
            $file = fopen($uploadDir . $filename, 'r');
            $result = [];
            while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
                $nombre = $data[0];
                $precio = $data[1];
                $tipo = $data[2];
                $marca = $data[3];
                $stock = $data[4];
                $foto = isset($data[5]) ? $data[5] : false;
                if ($tipo != "Tablet" && $tipo != "Smartphone") {
                    $result[] = array('Status' => 'Tipo de dispositivo no válido para ' . $nombre . ', no sera cargado.');
                    continue; 
                }
                if (!filter_var($precio, FILTER_VALIDATE_INT) || !filter_var($stock, FILTER_VALIDATE_INT)) {
                    $result[] = array('Status' => 'Precio o stock no válidos para ' . $nombre . ', no sera cargado.');
                    continue;
                }
                $dispositivo = Dispositivo::CheckYaCreado($nombre, $marca, $tipo);
                if ($dispositivo) {
                    $id = $dispositivo[0]['id'];
                    $nombre = $dispositivo[0]['nombre'];
                    Dispositivo::ActualizarDispositivo($id, $precio, $stock);
                    $result[] = array('Status' => $nombre . ' actualizado con exito!');
                } else {
                    $dispo = new Dispositivo($nombre, $precio, $tipo, $marca, $stock);
                    $ultimoId = $dispo->crearDispositivo();
                    $result[] = array('Status' => $dispo->nombre . ' dado de alta con exito!');
                }
            }
            fclose($file);
            $response->getBody()->write(json_encode($result));
        } else {
            $response->getBody()->write(json_encode(array('Status' => 'Error al subir el archivo')));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function DescargarMuchos(Request $request, Response $response, $args){
        $dispositivos = Dispositivo::TraerTodos();
        if($dispositivos){
            $filename = "dispositivos.csv";
            $file = fopen('php://memory', 'w');
            fputcsv($file, ['nombre', 'precio', 'tipo', 'marca', 'stock', 'ruta_foto']);
            foreach ($dispositivos as $dispositivo) {
                unset($dispositivo['id']);
                fputcsv($file, $dispositivo);

            }
            fseek($file, 0);
            $response = $response->withHeader('Content-Type', 'text/csv')
                                    ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
            $response->getBody()->write(stream_get_contents($file));
            fclose($file);
        }else {
            $response->getBody()->write(json_encode(array('Status' => 'No existen dispositivos')));
            $response->withStatus(404);
        }   
        return $response;
    }
}
