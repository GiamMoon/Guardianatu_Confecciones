<?php

namespace Model;

class Tareas extends ActiveRecord {
    protected static $tabla = "tareas";
    protected static $columnasDB = ["id", "cliente_id", "nombre", "fecha_creacion", "estado", "creadorTareas_id", "completadorTareas_id","estadoCostura", "completadorCostura_id"];

    public $id;
    public $cliente_id;
    public $nombre;
    public $fecha_creacion;
    public $estado;

    public $creadorTareas_id;
    public $completadorTareas_id;
    public $estadoCostura;
    public $completadorCostura_id;
    public $cliente; // Nueva propiedad


    public function __construct($args = []) {
        $this->id = $args["id"] ?? null;
        $this->cliente_id = $args["cliente_id"] ?? "";
        $this->nombre = $args["nombre"] ?? "";
        $this->fecha_creacion = $args["fecha_creacion"] ?? date('Y-m-d H:i:s');
        $this->estado = $args["estado"] ?? 0;
        $this->creadorTareas_id = $args["creadorTareas_id"] ?? "";
        $this->completadorTareas_id = $args["completadorTareas_id"] ?? "";
        $this->estadoCostura = $args["estadoCostura"] ?? 0;
        $this->completadorCostura_id = $args["completadorCostura_id"] ?? "";
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

    // En la clase Tareas
public static function obtenerEstadoTareasCliente($clienteID) {
    $query = "SELECT estado, estadoCostura FROM " . static::$tabla . " WHERE cliente_id = '{$clienteID}'";
    $resultados = self::consultarSQL($query);

    // Obtener estados de las tareas
    $estados = [];
    $estadosCostura = [];
    
    foreach ($resultados as $resultado) {
        $estados[] = $resultado->estado;
        $estadosCostura[] = $resultado->estadoCostura;
    }

    // Verificar el estado de las tareas
    if (count($estados) > 0 && !in_array(0, $estados)) {
        // Todas las tareas tienen estado 1
        $mensajeTareas = 'Completado';
    } else {
        // Al menos una tarea tiene estado 0
        $mensajeTareas = 'En proceso';
    }

    // Verificar el estado de las tareas de costura
    if (count($estadosCostura) > 0 && !in_array(0, $estadosCostura)) {
        // Todas las tareas de costura tienen estado 1
        $mensajeCostura = 'Completado';
    } else {
        // Al menos una tarea de costura tiene estado 0
        $mensajeCostura = 'En proceso';
    }

    return [
        'mensaje_tareas' => $mensajeTareas,
        'mensaje_costura' => $mensajeCostura,
    ];
}



public static function obtenerTareasAgrupadas() {
    $tareas = static::all(); // Obtén todas las tareas
    $tareasAgrupadas = [];

    foreach ($tareas as $tarea) {
        $clienteId = $tarea->cliente_id;

        // Obtener el cliente asociado a la tarea
        $cliente = Clientes::find($clienteId);

        if ($cliente) {
            if (!isset($tareasAgrupadas[$clienteId])) {
                $tareasAgrupadas[$clienteId] = [
                    'nombreCliente' => $cliente->nombres . ' ' . $cliente->apellidos,
                    'tareas' => [],
                ];
            }

            $tareasAgrupadas[$clienteId]['tareas'][] = $tarea;
        }
    }

    return $tareasAgrupadas;
}


    

 
}

