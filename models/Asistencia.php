<?php
namespace Model;

class Asistencia extends ActiveRecord {
    public static $tabla = 'asistencia';
    
    public static $columnasDB = [
        'asi_actividad',
        'asi_timestamp_registro',
        'asi_fue_puntual',
        'asi_minutos_diferencia'
    ];
    
    public static $idTabla = 'asi_id';
    
    public $asi_id;
    public $asi_actividad;
    public $asi_timestamp_registro;
    public $asi_fue_puntual;
    public $asi_minutos_diferencia;
    
    public function __construct($args = []) {
        $this->asi_id = $args['asi_id'] ?? null;
        $this->asi_actividad = $args['asi_actividad'] ?? null;
        $this->asi_timestamp_registro = $args['asi_timestamp_registro'] ?? '';
        $this->asi_fue_puntual = $args['asi_fue_puntual'] ?? 'f';
        $this->asi_minutos_diferencia = $args['asi_minutos_diferencia'] ?? 0;
    }

    public function validar() {
        self::$alertas = [];
        
        if (!$this->asi_actividad) {
            self::$alertas['error'][] = 'La actividad es obligatoria';
        }
        
        if (!$this->asi_timestamp_registro) {
            self::$alertas['error'][] = 'El timestamp es obligatorio';
        }

        return self::$alertas;
    }
}