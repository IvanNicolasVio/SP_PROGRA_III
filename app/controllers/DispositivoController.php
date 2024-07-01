<?php
require_once './models/Dispositivo.php';
require_once './controllers/ImagenController.php';

class DispositivoController
{
    public function CargarUno($request, $response, $args)
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

    public function ConsultarDispositivo($request, $response, $args){
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
}
