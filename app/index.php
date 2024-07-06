<?php
error_reporting(-1);
ini_set('display_errors', 1);
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
require __DIR__ . '/../vendor/autoload.php';
require_once './db/AccesoDatos.php';
require_once './controllers/DispositivoController.php';
require_once './controllers/VentaController.php';
require_once './controllers/UsuarioController.php';
require_once '../app/middlewares/issetMW.php';
require_once '../app/middlewares/CheckTipoMW.php';
require_once '../app/middlewares/CheckFechaMW.php';
require_once '../app/middlewares/CheckValoresMW.php';
require_once '../app/middlewares/CheckNumMW.php';
require_once '../app/middlewares/CheckPerfilMW.php';
require_once '../app/middlewares/ConfirmarPerfil.php';


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();

$app->group('/tienda', function (RouteCollectorProxy $group) {
  $group->post('/alta', \DispositivoController::class . ':CargarUno')
  ->add(new CheckTipoMW())
  ->add(new CheckNumMW('precio'))
  ->add(new CheckNumMW('stock'))
  ->add(new ConfirmarPerfil())
  ->add(new issetMW('dispositivo'));
  $group->post('/altaCSV', \DispositivoController::class . ':CargarMuchos');
  $group->get('/descargaCSV', \DispositivoController::class . ':DescargarMuchos');
});

$app->group('/ventas', function (RouteCollectorProxy $group) {
  $group->post('/alta', \VentaController::class . ':Vender')
  ->add(new CheckTipoMW())
  ->add(new CheckNumMW('stock'))
  ->add(new issetMW('venta'))
  ->add(new ConfirmarPerfil('empleado'));
  $group->put('/modificar', \VentaController::class . ':modificarVenta')
  ->add(new CheckTipoMW())
  ->add(new CheckNumMW('stock'))
  ->add(new CheckNumMW('id'))
  ->add(new ConfirmarPerfil())
  ->add(new issetMW('modificar'));
  $group->get('/descargar', \VentaController::class . ':Descargar')
  ->add(new ConfirmarPerfil());
});

$app->group('/registro', function (RouteCollectorProxy $group) {
  $group->post('', \UsuarioController::class . ':Registro')
  ->add(new CheckPerfilMW());
});

$app->group('/login', function (RouteCollectorProxy $group) {
  $group->post('', \UsuarioController::class . ':Logearse');
});

$app->group('/consultar', function (RouteCollectorProxy $group) {

  $group->post('', \DispositivoController::class . ':ConsultarDispositivo')
  ->add(new CheckTipoMW())
  ->add(new issetMW('consulta'))
  ->add(new ConfirmarPerfil('empleado'));
  $group->get('/productos/vendidos', \VentaController::class . ':TraerPorFecha')
  ->add(new CheckFechaMW())
  ->add(new ConfirmarPerfil('empleado'));
  $group->get('/ventas/porUsuario', \VentaController::class . ':TraerPorEmail')
  ->add(new issetMW('usuario'))
  ->add(new ConfirmarPerfil('empleado'));
  $group->get('/ventas/porProducto', \VentaController::class . ':TraerPorTipo')
  ->add(new CheckTipoMW())
  ->add(new issetMW('tipo'))
  ->add(new ConfirmarPerfil('empleado'));
  $group->get('/productos/entreValores', \VentaController::class . ':TraerPorValor')
  ->add(new CheckValoresMW())
  ->add(new issetMW('valor'))
  ->add(new ConfirmarPerfil('empleado'));
  $group->get('/ventas/ingresos', \VentaController::class . ':TraerPorPrecioFinal')
  ->add(new CheckFechaMW())
  ->add(new ConfirmarPerfil());
  $group->get('/productos/masVendidos', \VentaController::class . ':TraerDispMasVendido')
  ->add(new CheckFechaMW())
  ->add(new ConfirmarPerfil('empleado'));
});
$app->run();
