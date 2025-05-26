<?php
namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Actividad;
use MVC\Router;

class ActividadController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        $router->render('actividad/index', [
            'titulo' => 'Gestión de Actividades'
        ]);
    }

    public static function guardarAPI()
    {
        getHeadersApi();

        try {
            if (empty($_POST['act_nombre'])) {
                throw new Exception("¡No olvides!, El nombre de la actividad es requerido");
            }
            
            if (empty($_POST['act_fecha_esperada'])) {
                throw new Exception("¡Oh no!, La fecha esperada es requerida");
            }
            
            if (empty($_POST['act_hora_esperada'])) {
                throw new Exception("Carlos, debes ingresar la hora esperada");
            }

            $fecha = $_POST['act_fecha_esperada'];
            $hora = $_POST['act_hora_esperada'];  
            
            if (!$fecha || !$hora) {
                throw new Exception("Fecha y hora son requeridas");
            }
            
            $fechaHoraCombinada = $fecha . ' ' . $hora;
            if (!strtotime($fechaHoraCombinada)) {
                throw new Exception("Formato de fecha u hora inválido");
            }
            
            $fechaFormateada = date('Y-m-d H:i', strtotime($fechaHoraCombinada));
            
            $horaFormateada = date('H:i', strtotime($hora));
            
            $actividad = new Actividad([
                'act_nombre' => $_POST['act_nombre'],
                'act_descripcion' => $_POST['act_descripcion'] ?? '',
                'act_fecha_esperada' => $fechaFormateada,
                'act_hora_esperada' => $horaFormateada
            ]);

            $resultado = $actividad->crear();
        
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => '¡Muy bien Carlos, has creado una actividad correctamente!'
            ]);
        
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
        
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    public static function buscarAPI()
    {
        try {
            $actividades = self::fetchArray("
                SELECT * FROM actividad 
                ORDER BY act_fecha_esperada ASC, act_hora_esperada ASC
            ");

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Actividades obtenidas',
                'data' => $actividades
            ]);
            
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener actividades',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function modificarAPI()
    {
        getHeadersApi();

        try {
            if (empty($_POST['act_id'])) {
                throw new Exception("ID de la actividad no proporcionado");
            }

            if (empty($_POST['act_nombre'])) {
                throw new Exception("El nombre de la actividad es requerido");
            }
            
            if (empty($_POST['act_fecha_esperada'])) {
                throw new Exception("La fecha esperada es requerida");
            }
            
            if (empty($_POST['act_hora_esperada'])) {
                throw new Exception("La hora esperada es requerida");
            }

            $actividad = Actividad::find($_POST['act_id']);
            
            if (!$actividad) {
                throw new Exception("Actividad no encontrada");
            }

            $fecha = $_POST['act_fecha_esperada'];
            $hora = $_POST['act_hora_esperada'];
            
            if (!$fecha || !$hora) {
                throw new Exception("Fecha y hora son requeridas");
            }
            
            $fechaHoraCombinada = $fecha . ' ' . $hora;
            if (!strtotime($fechaHoraCombinada)) {
                throw new Exception("Formato de fecha u hora inválido");
            }
            
            $fechaFormateada = date('Y-m-d H:i', strtotime($fechaHoraCombinada));
            $horaFormateada = date('H:i', strtotime($hora));

            $actividad->sincronizar([
                'act_nombre' => $_POST['act_nombre'],
                'act_descripcion' => $_POST['act_descripcion'] ?? '',
                'act_fecha_esperada' => $fechaFormateada,
                'act_hora_esperada' => $horaFormateada
            ]);

            $resultado = $actividad->actualizar();
            
            if (!$resultado) {
                throw new Exception("Error al actualizar la actividad");
            }
            
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => '¡Actividad actualizada correctamente!'
            ]);
            
        } catch (Exception $e) {
            error_log("Error en modificarAPI: " . $e->getMessage());
            
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    public static function eliminarAPI()
    {
        header('Content-Type: application/json; charset=utf-8');

        if(empty($_POST['act_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de la actividad es obligatorio'
            ]);
            return;
        }

        $id = filter_var($_POST['act_id'], FILTER_VALIDATE_INT);
        if(!$id) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de la actividad debe ser un número válido'
            ]);
            return;
        }

        try {
            $actividad = Actividad::find($id);
            
            if(!$actividad) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Actividad no encontrada'
                ]);
                return;
            }

            $asistenciasRelacionadas = self::fetchArray("SELECT COUNT(*) as total FROM asistencia WHERE asi_actividad = $id");
            $totalAsistencias = $asistenciasRelacionadas[0]['total'] ?? 0;

            if ($totalAsistencias > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => "No se puede eliminar la actividad porque tiene $totalAsistencias asistencia(s) registrada(s). Elimina las asistencias primero."
                ]);
                return;
            }

            $sql = "DELETE FROM actividad WHERE act_id = $id";
            $resultado = self::SQL($sql);
            
            if($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'La actividad ha sido eliminada correctamente'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se pudo eliminar la actividad'
                ]);
            }
            
        } catch (Exception $e) {
            error_log("Error al eliminar actividad: " . $e->getMessage());
            
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar la actividad: ' . $e->getMessage()
            ]);
        }
    }

    public static function actividadesHoyAPI()
    {
        try {
            $hoy = date('Y-m-d');
            $actividades = self::fetchArray("
                SELECT * FROM actividad 
                WHERE act_fecha_esperada >= '$hoy 00:00' 
                AND act_fecha_esperada <= '$hoy 23:59'
                ORDER BY act_hora_esperada ASC
            ");

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Actividades de hoy obtenidas',
                'data' => $actividades
            ]);
            
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener actividades de hoy',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}