<?php 
require_once __DIR__ . '/../includes/app.php';

use MVC\Router;
use Controllers\AppController;
use Controllers\ActividadController;
use Controllers\AsistenciaController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

$router->get('/', [AppController::class, 'index']);

$router->get('/actividad', [ActividadController::class, 'renderizarPagina']);
$router->post('/actividad/guardarAPI', [ActividadController::class, 'guardarAPI']);
$router->get('/actividad/buscarAPI', [ActividadController::class, 'buscarAPI']);
$router->post('/actividad/modificarAPI', [ActividadController::class, 'modificarAPI']);
$router->post('/actividad/eliminarAPI', [ActividadController::class, 'eliminarAPI']);
$router->get('/actividad/actividadesHoyAPI', [ActividadController::class, 'actividadesHoyAPI']);

$router->get('/asistencia', [AsistenciaController::class, 'renderizarPagina']);
$router->post('/asistencia/registrarAPI', [AsistenciaController::class, 'registrarAPI']);
$router->get('/asistencia/buscarAPI', [AsistenciaController::class, 'buscarAPI']);
$router->post('/asistencia/modificarAPI', [AsistenciaController::class, 'modificarAPI']);
$router->post('/asistencia/eliminarAPI', [AsistenciaController::class, 'eliminarAPI']);
$router->get('/asistencia/reportePuntualidadAPI', [AsistenciaController::class, 'reportePuntualidadAPI']);
$router->get('/asistencia/asistenciasPorActividadAPI', [AsistenciaController::class, 'asistenciasPorActividadAPI']);

$router->comprobarRutas();