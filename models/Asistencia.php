<?php
namespace Model;

class Asistencia extends ActiveRecord {
    public static $tabla = 'asistencia';
    
    public static $columnasDB = [
        'asi_actividad',
        'asi_fecha_asistencia',
        'asi_hora_llegada',
        'asi_fue_puntual',
        'asi_minutos_diferencia'
    ];
    
    public static $idTabla = 'asi_id';
    
  
    public $asi_id;
    public $asi_actividad;
    public $asi_fecha_asistencia;
    public $asi_hora_llegada;
    public $asi_fue_puntual;
    public $asi_minutos_diferencia;
    
    public function __construct($args = []) {
        $this->asi_id = $args['asi_id'] ?? null;
        $this->asi_actividad = $args['asi_actividad'] ?? null;
        $this->asi_fecha_asistencia = $args['asi_fecha_asistencia'] ?? '';
        $this->asi_hora_llegada = $args['asi_hora_llegada'] ?? '';
        $this->asi_fue_puntual = $args['asi_fue_puntual'] ?? null;
        $this->asi_minutos_diferencia = $args['asi_minutos_diferencia'] ?? 0;
    }
    
    public function calcularPuntualidad($toleranciaMinutos = 5) {
        if ($this->asi_minutos_diferencia <= $toleranciaMinutos) {
            $this->asi_fue_puntual = true;
        } else {
            $this->asi_fue_puntual = false;
        }
    }
    
    public static function estadisticasPuntualidad() {
        $sql = "SELECT 
                    COUNT(*) as total_asistencias,
                    SUM(CASE WHEN asi_fue_puntual = 't' THEN 1 ELSE 0 END) as puntuales,
                    SUM(CASE WHEN asi_fue_puntual = 'f' THEN 1 ELSE 0 END) as tardanzas,
                    ROUND(AVG(asi_minutos_diferencia), 2) as promedio_minutos
                FROM asistencia";
        return self::fetchArray($sql);
    }
}