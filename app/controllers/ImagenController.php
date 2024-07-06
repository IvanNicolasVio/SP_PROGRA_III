<?php

  class ImagenController
  {
    public static function FotoDispo($objeto, $file)
    {
        $nombreTemporal = $_FILES['foto']['tmp_name'];
        $nombreOriginal = $_FILES['foto']['name'];
        $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
        $directorio = "./ImagenesDeProductos/2024";
        $nombreNuevo = $objeto->nombre . "_" . $objeto->tipo . "." . $extension;
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }
        $rutaGuardado = $directorio . "/" . $nombreNuevo;
        if (move_uploaded_file($nombreTemporal, $rutaGuardado)) {
            return $rutaGuardado;
        } else {
            return false;
        }
    }

    public static function FotoVenta($objeto, $file)
    {
        $nombreTemporal = $_FILES['foto']['tmp_name'];
        $nombreOriginal = $_FILES['foto']['name'];
        $email = explode('@',$objeto->email);
        $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
        $directorio = "./ImagenesDeVenta/2024";
        $nombreNuevo = $objeto->nombre . "_" . $objeto->tipo . "_" . $objeto->marca . "_" . $email[0] . "_" . $objeto->fechaPedido . "." . $extension;
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }
        $rutaGuardado = $directorio . "/" . $nombreNuevo;
        if (move_uploaded_file($nombreTemporal, $rutaGuardado)) {
            return $rutaGuardado;
        } else {
            return false;
        }
    }

    public static function FotoUsuario($objeto, $file)
    {
        $nombreTemporal = $_FILES['foto']['tmp_name'];
        $nombreOriginal = $_FILES['foto']['name'];
        $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
        $directorio = "./ImagenesDeUsuarios/2024";
        $nombreNuevo = $objeto->usuario . "_" . $objeto->perfil . "_" . $objeto->fecha_de_alta . "." . $extension;
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }
        $rutaGuardado = $directorio . "/" . $nombreNuevo;
        if (move_uploaded_file($nombreTemporal, $rutaGuardado)) {
            return $rutaGuardado;
        } else {
            return false;
        }
    }

  }
