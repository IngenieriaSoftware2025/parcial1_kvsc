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
        $actividades = Actividad::all();
        
        $router->render('asistencia/index', [
            'actividades' => $actividades,
            'titulo' => 'Registro de Asistencias'
        ]);
    }

    public static function registrarAPI()
    {
        getHeadersApi();

        try {
            // Validaciones básicas
            if (empty($_POST['asi_actividad'])) {
                throw new Exception("Carlos, debes seleccionar una actividad");
            }
            
            if (empty($_POST['asi_fecha_asistencia'])) {
                throw new Exception("La fecha de asistencia es requerida");
            }
            
            if (empty($_POST['asi_hora_llegada'])) {
                throw new Exception("Carlos, debes registrar la hora de llegada");
            }

            // Obtener datos de la actividad para calcular puntualidad
            $actividadId = $_POST['asi_actividad'];
            $actividad = Actividad::find($actividadId);
            
            if (!$actividad) {
                throw new Exception("Actividad no encontrada");
            }

            // Formatear fecha para Informix
            $fechaAsistencia = $_POST['asi_fecha_asistencia']; // YYYY-MM-DD
            $horaLlegada = $_POST['asi_hora_llegada']; // HH:MM
            
            // Validar formatos
            if (!strtotime($fechaAsistencia . ' ' . $horaLlegada)) {
                throw new Exception("Formato de fecha u hora inválido");
            }

            // Calcular diferencia en minutos
            // Para Informix DATETIME HOUR TO MINUTE, el valor puede venir como "08:30" directamente
            $horaEsperadaValue = $actividad->act_hora_esperada;
            
            if (!$horaEsperadaValue) {
                throw new Exception("La actividad no tiene hora esperada configurada");
            }
            
            // Si viene en formato datetime, extraer solo la hora
            if (strpos($horaEsperadaValue, ' ') !== false) {
                // Formato: "1900-01-01 08:30" -> extraer "08:30"
                $horaEsperada = date('H:i', strtotime($horaEsperadaValue));
            } else {
                // Ya viene en formato "08:30"
                $horaEsperada = $horaEsperadaValue;
            }
            
            // Asegurar que ambas horas estén en formato correcto para comparar
            $horaEsperadaSeconds = strtotime('1970-01-01 ' . $horaEsperada);
            $horaLlegadaSeconds = strtotime('1970-01-01 ' . $horaLlegada);
            
            if ($horaEsperadaSeconds === false || $horaLlegadaSeconds === false) {
                throw new Exception("Error al procesar las horas para comparación");
            }
            
            $diferenciaSegundos = $horaLlegadaSeconds - $horaEsperadaSeconds;
            $diferenciaMinutos = round($diferenciaSegundos / 60);

            // Determinar si fue puntual (tolerancia de 5 minutos)
            $fuePuntual = $diferenciaMinutos <= 5;
            
            // Formatear para Informix (SIN SEGUNDOS)
            $fechaFormateada = date('Y-m-d', strtotime($fechaAsistencia));
            $horaFormateada = date('H:i', strtotime($horaLlegada));

            $asistencia = new Asistencia([
                'asi_actividad' => $_POST['asi_actividad'],
                'asi_fecha_asistencia' => $fechaFormateada,
                'asi_hora_llegada' => $horaFormateada,
                'asi_fue_puntual' => $fuePuntual,
                'asi_minutos_diferencia' => $diferenciaMinutos
            ]);

            $resultado = $asistencia->crear();
        
            $mensaje = $fuePuntual ? 
                "¡Excelente Carlos! Llegaste puntual a tu actividad" : 
                "Carlos, llegaste {$diferenciaMinutos} minutos tarde. ¡La próxima vez puedes hacerlo mejor!";

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => $mensaje,
                'puntual' => $fuePuntual,
                'minutos_diferencia' => $diferenciaMinutos
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
            $asistencias = self::fetchArray("
                SELECT a.*, ac.act_nombre, ac.act_hora_esperada, ac.act_fecha_esperada
                FROM asistencia a
                JOIN actividad ac ON a.asi_actividad = ac.act_id
                ORDER BY a.asi_fecha_asistencia DESC, a.asi_hora_llegada DESC
            ");

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Asistencias obtenidas correctamente',
                'data' => $asistencias
            ]);
            
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener asistencias',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function modificarAPI()
    {
        getHeadersApi();

        try {
            if (empty($_POST['asi_id'])) {
                throw new Exception("ID de la asistencia no proporcionado");
            }

            if (empty($_POST['asi_actividad'])) {
                throw new Exception("Debes seleccionar una actividad");
            }
            
            if (empty($_POST['asi_fecha_asistencia'])) {
                throw new Exception("La fecha de asistencia es requerida");
            }
            
            if (empty($_POST['asi_hora_llegada'])) {
                throw new Exception("La hora de llegada es requerida");
            }

            $asistencia = Asistencia::find($_POST['asi_id']);
            
            if (!$asistencia) {
                throw new Exception("Asistencia no encontrada");
            }

            // Recalcular puntualidad
            $actividadId = $_POST['asi_actividad'];
            $actividad = Actividad::find($actividadId);
            
            if (!$actividad) {
                throw new Exception("Actividad no encontrada");
            }
            
            // Formatear datos
            $fechaAsistencia = $_POST['asi_fecha_asistencia'];
            $horaLlegada = $_POST['asi_hora_llegada'];
            
            if (!strtotime($fechaAsistencia . ' ' . $horaLlegada)) {
                throw new Exception("Formato de fecha u hora inválido");
            }
            
            // Calcular diferencia
            $horaEsperadaValue = $actividad->act_hora_esperada;
            
            if (!$horaEsperadaValue) {
                throw new Exception("La actividad no tiene hora esperada configurada");
            }
            
            // Manejar diferentes formatos de hora de Informix
            if (strpos($horaEsperadaValue, ' ') !== false) {
                // Formato: "1900-01-01 08:30" -> extraer "08:30"
                $horaEsperada = date('H:i', strtotime($horaEsperadaValue));
            } else {
                // Ya viene en formato "08:30"
                $horaEsperada = $horaEsperadaValue;
            }
            
            $horaEsperadaSeconds = strtotime('1970-01-01 ' . $horaEsperada);
            $horaLlegadaSeconds = strtotime('1970-01-01 ' . $horaLlegada);
            
            if ($horaEsperadaSeconds === false || $horaLlegadaSeconds === false) {
                throw new Exception("Error al procesar las horas para comparación");
            }
            
            $diferenciaSegundos = $horaLlegadaSeconds - $horaEsperadaSeconds;
            $diferenciaMinutos = round($diferenciaSegundos / 60);
            $fuePuntual = $diferenciaMinutos <= 5;
            
            // Formatear para Informix (SIN SEGUNDOS)
            $fechaFormateada = date('Y-m-d', strtotime($fechaAsistencia));
            $horaFormateada = date('H:i', strtotime($horaLlegada));

            $asistencia->sincronizar([
                'asi_actividad' => $_POST['asi_actividad'],
                'asi_fecha_asistencia' => $fechaFormateada,
                'asi_hora_llegada' => $horaFormateada,
                'asi_fue_puntual' => $fuePuntual,
                'asi_minutos_diferencia' => $diferenciaMinutos
            ]);

            $resultado = $asistencia->actualizar();
            
            if (!$resultado) {
                throw new Exception("Error al actualizar la asistencia");
            }
            
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => '¡Asistencia actualizada correctamente!'
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
        getHeadersApi();

        if(empty($_POST['asi_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de la asistencia es obligatorio'
            ]);
            return;
        }

        $id = filter_var($_POST['asi_id'], FILTER_VALIDATE_INT);
        if(!$id) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de la asistencia debe ser un número válido'
            ]);
            return;
        }

        try {
            $asistencia = Asistencia::find($id);
            
            if(!$asistencia) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Asistencia no encontrada'
                ]);
                return;
            }
            
            $resultado = $asistencia->eliminar();
            
            if($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'La asistencia ha sido eliminada correctamente'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se pudo eliminar la asistencia'
                ]);
            }
            
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar la asistencia',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function reportePuntualidadAPI()
    {
        try {
            $estadisticas = self::fetchArray("
                SELECT 
                    COUNT(*) as total_asistencias,
                    SUM(CASE WHEN asi_fue_puntual = 't' THEN 1 ELSE 0 END) as puntuales,
                    SUM(CASE WHEN asi_fue_puntual = 'f' THEN 1 ELSE 0 END) as tardanzas,
                    ROUND(AVG(asi_minutos_diferencia), 2) as promedio_minutos,
                    ROUND((SUM(CASE WHEN asi_fue_puntual = 't' THEN 1 ELSE 0 END) * 100.0 / COUNT(*)), 2) as porcentaje_puntualidad
                FROM asistencia
            ");

            $porActividad = self::fetchArray("
                SELECT 
                    ac.act_nombre,
                    COUNT(*) as total_asistencias,
                    SUM(CASE WHEN a.asi_fue_puntual = 't' THEN 1 ELSE 0 END) as puntuales,
                    ROUND(AVG(a.asi_minutos_diferencia), 2) as promedio_minutos
                FROM asistencia a
                JOIN actividad ac ON a.asi_actividad = ac.act_id
                GROUP BY ac.act_id, ac.act_nombre
                ORDER BY puntuales DESC
            ");

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Reporte de puntualidad generado',
                'estadisticas_generales' => $estadisticas[0] ?? [],
                'por_actividad' => $porActividad
            ]);
            
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al generar reporte',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function asistenciasPorActividadAPI()
    {
        try {
            $actividadId = $_GET['actividad_id'] ?? null;
            
            if (!$actividadId) {
                throw new Exception("ID de actividad requerido");
            }

            $asistencias = self::fetchArray("
                SELECT a.*, ac.act_nombre, ac.act_hora_esperada
                FROM asistencia a
                JOIN actividad ac ON a.asi_actividad = ac.act_id
                WHERE a.asi_actividad = $actividadId
                ORDER BY a.asi_fecha_asistencia DESC, a.asi_hora_llegada DESC
            ");

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Asistencias por actividad obtenidas',
                'data' => $asistencias
            ]);
            
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener asistencias por actividad',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}