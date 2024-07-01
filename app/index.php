<?php
// Error Handling
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
require_once '../app/middlewares/issetMW.php';
require_once '../app/middlewares/CheckTipoMW.php';
require_once '../app/middlewares/CheckFechaMW.php';
require_once '../app/middlewares/CheckValoresMW.php';


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();

$app->group('/tienda', function (RouteCollectorProxy $group) {
  $group->post('/alta', \DispositivoController::class . ':CargarUno')
  ->add(new CheckTipoMW())
  ->add(new issetMW('dispositivo'));
  $group->post('/consultar', \DispositivoController::class . ':ConsultarDispositivo')
  ->add(new CheckTipoMW())
  ->add(new issetMW('consulta'));
});

$app->group('/ventas', function (RouteCollectorProxy $group) {
  $group->post('/alta', \VentaController::class . ':Vender')
  ->add(new CheckTipoMW())
  ->add(new issetMW('venta'));
});

$app->group('/ventas/consultar', function (RouteCollectorProxy $group) {
  $group->get('/productos/vendidos', \VentaController::class . ':TraerPorFecha')
  ->add(new CheckFechaMW());
  $group->get('/ventas/porUsuario', \VentaController::class . ':TraerPorEmail')
  ->add(new issetMW('usuario'));
  $group->get('/ventas/porProducto', \VentaController::class . ':TraerPorTipo')
  ->add(new CheckTipoMW())
  ->add(new issetMW('tipo'));
  $group->get('/productos/entreValores', \VentaController::class . ':TraerPorValor')
  ->add(new CheckValoresMW())
  ->add(new issetMW('valor'));
  $group->get('/ventas/ingresos', \VentaController::class . ':TraerPorPrecioFinal')
  ->add(new CheckFechaMW());
  $group->get('/productos/masVendidos', \VentaController::class . ':TraerDispMasVendido')
  ->add(new CheckFechaMW());
});
$app->run();
