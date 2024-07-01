<?php
include_once './db/AccesoDatos.php';

class Dispositivo
{
    public $nombre;
    public $precio;
    public $tipo;
    public $marca;
    public $stock;

    public function __construct($nombre,$precio,$tipo,$marca,$stock)
    {
        $this->nombre = $nombre;
        $this->precio = $precio;
        $this->tipo = $tipo;
        $this->marca = $marca;
        $this->stock = $stock;
    }

    public function crearDispositivo()
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("INSERT INTO dispositivos (nombre, precio, tipo, marca, stock) VALUES (:nombre, :precio, :tipo, :marca, :stock)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':marca', $this->marca, PDO::PARAM_STR);
        $consulta->bindValue(':stock', $this->stock, PDO::PARAM_INT);
        $consulta->execute();
        return $objAccesoDatos->RetornarUltimoIdInsertado();
    }

    public static function CheckYaCreado($nombre,$marca,$tipo){
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM dispositivos WHERE nombre = ? AND marca = ? AND tipo = ?");
        $consulta->bindValue(1, $nombre, PDO::PARAM_STR);
        $consulta->bindValue(2, $marca, PDO::PARAM_STR);
        $consulta->bindValue(3, $tipo, PDO::PARAM_STR);
        $consulta->execute();
        $dispositivos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if ($dispositivos) {
            return $dispositivos;
        } else {
            return false;
        }
    }

    public static function ActualizarDispositivo($id,$precio,$stock){
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("UPDATE dispositivos SET precio = ?, stock = stock + ? WHERE id = ?");
        $consulta->bindValue(1, $precio, PDO::PARAM_INT);
        $consulta->bindValue(2, $stock, PDO::PARAM_INT);
        $consulta->bindValue(3, $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function ConsultarDispositivo($nombre){
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM dispositivos WHERE nombre = ?");
        $consulta->bindValue(1, $nombre, PDO::PARAM_STR);
        $consulta->execute();
        $dispositivos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if ($dispositivos) {
            return $dispositivos;
        } else {
            return false;
        }
    }

    public static function DescontarStock($id,$stock){
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("UPDATE dispositivos SET  stock = stock - ? WHERE id = ?");
        $consulta->bindValue(1, $stock, PDO::PARAM_INT);
        $consulta->bindValue(2, $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public function AgregarFoto($string, $id) {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("UPDATE dispositivos SET ruta_foto = :foto WHERE id = :id");
        $consulta->bindValue(':foto', $string, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        return $objAccesoDatos->RetornarUltimoIdInsertado();
    }
}