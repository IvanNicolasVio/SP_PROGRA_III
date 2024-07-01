<?php
include_once './db/AccesoDatos.php';
include_once './models/ControlTiempo.php';
class Venta
{
    public $email;
    public $nombre;
    public $tipo;
    public $marca;
    public $stock;
    public $fechaPedido;
    public $precioTotal;

    public function __construct($email,$nombre,$tipo,$marca,$stock,$precio)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->marca = $marca;
        $this->stock = $stock;
        $this->fechaPedido = Fecha::DarFechaActual();
        $this->precioTotal = $precio * $stock;
    }

    public function crearVenta()
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("INSERT INTO ventas
                                 (email, nombre_disp, tipo, marca, stock,fecha,precio_total) 
                                 VALUES (:email, :nombre, :tipo, :marca, :stock, :fecha, :precio_total)");
        $consulta->bindValue(':email', $this->email, PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_INT);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':marca', $this->marca, PDO::PARAM_STR);
        $consulta->bindValue(':stock', $this->stock, PDO::PARAM_INT);
        $consulta->bindValue(':fecha', $this->fechaPedido);
        $consulta->bindValue(':precio_total', $this->precioTotal, PDO::PARAM_INT);
        $consulta->execute();
        return $objAccesoDatos->RetornarUltimoIdInsertado();
    }

    public function AgregarFoto($string, $id) {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("UPDATE ventas SET ruta_foto = :foto WHERE id = :id");
        $consulta->bindValue(':foto', $string, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        return $objAccesoDatos->RetornarUltimoIdInsertado();
    }

    public static function ConsultarVentasPorFecha($fecha){
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM ventas WHERE fecha = ?");
        $consulta->bindValue(1, $fecha);
        $consulta->execute();
        $dispositivos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if ($dispositivos) {
            return $dispositivos;
        } else {
            return false;
        }
    }

    public static function ConsultarVentasPorUsuario($email){
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM ventas WHERE email = ?");
        $consulta->bindValue(1, $email);
        $consulta->execute();
        $dispositivos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if ($dispositivos) {
            return $dispositivos;
        } else {
            return false;
        }
    }

    public static function ConsultarVentasPorTipo($tipo){
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM ventas WHERE tipo = ?");
        $consulta->bindValue(1, $tipo);
        $consulta->execute();
        $dispositivos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if ($dispositivos) {
            return $dispositivos;
        } else {
            return false;
        }
    }

    public static function ConsultarVentasPorValor($valorUno,$valorDos){
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM ventas WHERE precio_total >= ? AND precio_total <= ?");
        $consulta->bindValue(1, $valorUno);
        $consulta->bindValue(2, $valorDos);
        $consulta->execute();
        $dispositivos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if ($dispositivos) {
            return $dispositivos;
        } else {
            return false;
        }
    }

    public static function ConsultarPrecioTotalPorFecha($fecha){
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT SUM(precio_total) as precio_final FROM ventas WHERE fecha = ?");
        $consulta->bindValue(1, $fecha);
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }

    public static function ConsultarPrecioTotal(){
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT SUM(precio_total) as precio_final FROM ventas");
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }

    public static function ConsultarProductoMasVendido() {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT nombre_disp, SUM(stock) as cantidad_vendida FROM ventas GROUP BY nombre_disp ORDER BY cantidad_vendida DESC 
            LIMIT 1
        ");
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }

    public static function ConsultarVentasPorId($id){
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM ventas WHERE id = ?");
        $consulta->bindValue(1, $id);
        $consulta->execute();
        $dispositivo = $consulta->fetch(PDO::FETCH_ASSOC);
        if ($dispositivo) {
            return $dispositivo;
        } else {
            return false;
        }
    }

    public static function ModificarVenta($id,$email,$nombre,$tipo,$marca,$stock){
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("UPDATE ventas SET email = :email, nombre_disp = :nombre, tipo = :tipo,marca = :marca, stock = :stock  WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':email', $email, PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $consulta->bindValue(':marca', $marca, PDO::PARAM_STR);
        $consulta->bindValue(':stock', $stock, PDO::PARAM_INT);
        $consulta->execute();
        return $objAccesoDatos->RetornarUltimoIdInsertado();
    }
}