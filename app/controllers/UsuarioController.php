<?php
require_once './models/Usuario.php';
require_once './models/AutenticadorJWT.php';
require_once './controllers/ImagenController.php';
include_once './models/ControlTiempo.php';
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UsuarioController
{
    public function Registro(Request $request, Response $response, $args)
    {
        $parametros = $request->getParsedBody();
        $mail = $parametros['mail'];
        $usuario = $parametros['usuario'];
        $contrasenia = $parametros['contrasenia'];
        $perfil = $parametros['perfil'];
        $foto = isset($parametros['foto']) ? $parametros['foto'] : false;
        $nuevoUsuario = new Usuario($mail,$usuario,$contrasenia,$perfil);
        $ultimoId = $nuevoUsuario->crearUsuario();
        if(!$foto){
            $ruta = ImagenController::FotoUsuario($nuevoUsuario,$foto);
            $nuevoUsuario->AgregarFoto($ruta,$ultimoId);
        }

        if($ultimoId){
            $response->getBody()->write(json_encode(array('Status'=>'Se creo el usuario  ' . $usuario)));
        }else{
            $response->getBody()->write(json_encode(array('Error'=> 'No se pudo crear el usuario')));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function Logearse(Request $request, Response $response, $args){
        $params = $request->getParsedBody();
        $usuario = $params['usuario'];
        $contrasenia = $params['contrasenia'];
        $usuarioCheq = Usuario::TraerUsuarioContraseña($usuario,$contrasenia);
        if($usuarioCheq){
            $data = AutentificadorJWT::CrearToken($usuarioCheq);
            $response->getBody()->write($data);
        }else{
            $response->getBody()->write(json_encode(array('Status'=>'No existe el empleado')));
        }
        return $response;
    }
}

