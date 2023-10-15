<?php

namespace Model;

class Tareas extends ActiveRecord {
    protected static $tabla = "tareas";
    protected static $columnasDB = ["id", "cliente_id", "nombre", "fecha_creacion", "estado", "creadorTareas_id", "completadorTareas_id"];

    public $id;
    public $cliente_id;
    public $nombre;
    public $fecha_creacion;
    public $estado;

    public $creadorTareas_id;
    public $completadorTareas_id;
    public $cliente; // Nueva propiedad


    public function __construct($args = []) {
        $this->id = $args["id"] ?? null;
        $this->cliente_id = $args["cliente_id"] ?? "";
        $this->nombre = $args["nombre"] ?? "";
        $this->fecha_creacion = $args["fecha_creacion"] ?? date('Y-m-d');
        $this->estado = $args["estado"] ?? 0;
        $this->creadorTareas_id = $args["creadorTareas_id"] ?? "";
        $this->completadorTareas_id = $args["completadorTareas_id"] ?? "";
        $this->cliente = new Clientes(); // Inicializa la propiedad cliente
    }

    public static function obtenerEstadoTareas() {
        $query = "SELECT estado FROM " . static::$tabla;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'cliente_id' => $this->cliente_id,
            'nombre' => $this->nombre,
            'fecha_creacion' => $this->fecha_creacion,
            'estado' => $this->estado,
        ];
    }
}

