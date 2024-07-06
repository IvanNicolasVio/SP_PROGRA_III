<?php
include_once './db/AccesoDatos.php';
include_once './models/ControlTiempo.php';
class Usuario
{
    public $mail;
    public $usuario;
    public $contrasenia;
    public $perfil;
    public $fecha_de_alta;


    public function __construct($mail,$usuario,$contrasenia,$perfil)
    {
        $this->mail = $mail;
        $this->usuario = $usuario;
        $this->contrasenia = $contrasenia;
        $this->perfil = $perfil;
        $this->fecha_de_alta = Fecha::DarFechaActual();
    }

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("INSERT INTO usuarios
                                 (mail, usuario, contrasenia, perfil, fecha_de_alta) 
                                 VALUES (:mail, :usuario, :contrasenia, :perfil, :fecha_de_alta)");
        $consulta->bindValue(':mail', $this->mail, PDO::PARAM_STR);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':contrasenia', $this->contrasenia, PDO::PARAM_STR);
        $consulta->bindValue(':perfil', $this->perfil, PDO::PARAM_STR);
        $consulta->bindValue(':fecha_de_alta', $this->fecha_de_alta);
        $consulta->execute();
        return $objAccesoDatos->RetornarUltimoIdInsertado();
    }

    public function AgregarFoto($string, $id) {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("UPDATE usuarios SET foto = :foto WHERE id = :id");
        $consulta->bindValue(':foto', $string, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        return $objAccesoDatos->RetornarUltimoIdInsertado();
    }

    public static function TraerUsuarioContraseña($usuario,$contrasenia){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM usuarios WHERE usuario = ? AND contrasenia = ? ");
        $consulta->bindValue(1, $usuario, PDO::PARAM_STR);
        $consulta->bindValue(2, $contrasenia, PDO::PARAM_STR);
        $consulta->execute();
        $empleado = $consulta->fetch(PDO::FETCH_ASSOC);
        if ($empleado) {
            return $empleado;
        } else {
            return false;
        }
    }

}