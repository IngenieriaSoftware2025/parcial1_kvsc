<?php
namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Asistencia;
use Model\Actividad;
use MVC\Router;

class AsistenciaController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        try {
            $actividades = self::fetchArray("SELECT * FROM actividad ORDER BY act_nombre ASC");
            
            $actividadesObj = [];
            foreach ($actividades as $actividad) {
                $obj = new \stdClass();
                foreach ($actividad as $key => $value) {
                    $obj->$key = $value;
                }
                $actividadesObj[] = $obj;
            }
            
        } catch (Exception $e) {
            $actividadesObj = [];
        }
        
        $router->render('asistencia/index', [
            'actividades' => $actividadesObj,
            'titulo' => 'Control de Puntualidad'
        ]);
    }

    public static function registrarAPI()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            if (empty($_POST['asi_actividad'])) {
                throw new Exception("Debes seleccionar una actividad");
            }

            $actividadId = intval($_POST['asi_actividad']);
            if ($actividadId <= 0) {
                throw new Exception("ID de actividad inválido");
            }

            $sqlActividad = "SELECT act_id, act_nombre, act_fecha_esperada, act_hora_esperada FROM actividad WHERE act_id = " . $actividadId;
            $actividadData = self::fetchArray($sqlActividad);
            
            if (empty($actividadData)) {
                throw new Exception("Actividad no encontrada");
            }

            $actividad = $actividadData[0];

            date_default_timezone_set('America/Guatemala');
            $timestampActual = date('Y-m-d H:i');
            $fechaActual = date('Y-m-d');
            $horaActual = date('H:i');
            
            $fechaEsperada = date('Y-m-d', strtotime($actividad['act_fecha_esperada']));
            $horaEsperada = date('H:i', strtotime($actividad['act_hora_esperada']));
            
            if ($fechaActual !== $fechaEsperada) {
                throw new Exception("Esta actividad no es para hoy. La fecha esperada es: " . date('d/m/Y', strtotime($fechaEsperada)));
            }
            
            $minutosActuales = (intval(date('H')) * 60) + intval(date('i'));
            $horaEsperadaParts = explode(':', $horaEsperada);
            $minutosEsperados = (intval($horaEsperadaParts[0]) * 60) + intval($horaEsperadaParts[1]);
            
            $diferenciaMinutos = $minutosActuales - $minutosEsperados;
            $fuePuntual = $diferenciaMinutos <= 5;

            $asistencia = new Asistencia([
                'asi_actividad' => $actividadId,
                'asi_timestamp_registro' => $timestampActual,
                'asi_fue_puntual' => $fuePuntual ? 't' : 'f',
                'asi_minutos_diferencia' => $diferenciaMinutos
            ]);
            
            $resultado = $asistencia->guardar();

            if ($resultado) {
                $nombreActividad = $actividad['act_nombre'];
                
                if ($fuePuntual) {
                    if ($diferenciaMinutos <= 0) {
                        $mensaje = "¡Perfecto Carlos! Llegaste temprano a '$nombreActividad'";
                    } else {
                        $mensaje = "¡Bien Carlos! Llegaste puntual a '$nombreActividad'";
                    }
                } else {
                    $mensaje = "Carlos, llegaste $diferenciaMinutos minutos tarde a '$nombreActividad'";
                }

                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => $mensaje
                ], JSON_UNESCAPED_UNICODE);
                
            } else {
                throw new Exception("Error al guardar la asistencia");
            }

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public static function buscarAPI()
    {
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            $sql = "
                SELECT 
                    a.asi_id,
                    a.asi_actividad,
                    a.asi_timestamp_registro,
                    a.asi_fue_puntual,
                    a.asi_minutos_diferencia,
                    ac.act_nombre,
                    ac.act_fecha_esperada,
                    ac.act_hora_esperada
                FROM asistencia a
                INNER JOIN actividad ac ON a.asi_actividad = ac.act_id
                ORDER BY a.asi_timestamp_registro DESC
                LIMIT 50
            ";
            
            $asistencias = self::fetchArray($sql);

           
            foreach ($asistencias as &$asistencia) {
                $asistencia['fecha_formateada'] = date('d/m/Y H:i', strtotime($asistencia['asi_timestamp_registro']));
                $asistencia['asi_fue_puntual'] = ($asistencia['asi_fue_puntual'] === 't') ? 1 : 0;
            }

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Asistencias encontradas',
                'data' => $asistencias
            ], JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar asistencias: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public static function eliminarAPI()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            if (empty($_POST['asi_id'])) {
                throw new Exception('ID de asistencia es obligatorio');
            }

            $id = intval($_POST['asi_id']);
            if ($id <= 0) {
                throw new Exception('ID debe ser un número válido');
            }

            $asistencia = Asistencia::find($id);
            
            if ($asistencia) {
                $resultado = $asistencia->eliminar();
                
                if ($resultado) {
                    http_response_code(200);
                    echo json_encode([
                        'codigo' => 1,
                        'mensaje' => 'Asistencia eliminada correctamente'
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    throw new Exception('No se pudo eliminar la asistencia');
                }
            } else {
                throw new Exception('Asistencia no encontrada');
            }
            
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
}