<?php
namespace Model;

class Actividad extends ActiveRecord {
    public static $tabla = 'actividad';
    
    public static $columnasDB = [
        'act_nombre',
        'act_descripcion',
        'act_fecha_esperada',
        'act_hora_esperada'
    ];
    
    public static $idTabla = 'act_id';
    
    public $act_id;
    public $act_nombre;
    public $act_descripcion;
    public $act_fecha_esperada;
    public $act_hora_esperada;
    
    public function __construct($args = []) {
        $this->act_id = $args['act_id'] ?? null;
        $this->act_nombre = $args['act_nombre'] ?? '';
        $this->act_descripcion = $args['act_descripcion'] ?? '';
        $this->act_fecha_esperada = $args['act_fecha_esperada'] ?? '';
        $this->act_hora_esperada = $args['act_hora_esperada'] ?? '';
    }
    
    public static function obtenerTodasActividades() {
        $sql = "SELECT * FROM actividad ORDER BY act_fecha_esperada ASC";
        return self::SQL($sql);
    }
    
    public static function buscarPorId($id) {
        $sql = "SELECT * FROM actividad WHERE act_id = $id";
        return self::SQL($sql);
    }
    
    public static function eliminarActividad($id) {
        $sql = "DELETE FROM actividad WHERE act_id = $id";
        return self::SQL($sql);
    }
    
    public static function actividadesDeHoy() {
        $hoy = date('Y-m-d');
        $sql = "SELECT * FROM actividad WHERE DATE(act_fecha_esperada) = '$hoy' ORDER BY act_hora_esperada ASC";
        return self::SQL($sql);
    }
}