<?php

namespace Model;

class Presupuesto extends ActiveRecord{
    protected static $tabla = "presupuesto";


    protected static $columnasDB = ["id", "tipo", "cantidad","concepto","fecha"];

    public $id;
    public $tipo;
    public $cantidad;
    public $concepto;
    public $fecha;

    public function __construct($args =[]){
        $this->id = $args["id"] ?? null;
        $this->tipo = $args["tipo"] ?? "";
        $this->cantidad = $args["cantidad"] ?? "";
        $this->concepto = $args["concepto"] ?? "";
        $this->fecha = $args["fecha"] ?? date('Y-m-d H:i:s');
    }
    // En el método filtrarPorMesAno de la clase Presupuesto
public static function filtrarPorMesAno($mes, $ano) {
    $query = "SELECT * FROM " . static::$tabla . " WHERE DATE_FORMAT(fecha, '%m') = ? AND DATE_FORMAT(fecha, '%Y') = ?";
    $params = [$mes, $ano];

    return self::consultarSQLPre($query, $params);
}

    
    public static function calcularTotalPorSemana($ingresosEgresos) {
        $totalPorSemana = [];
    
        foreach ($ingresosEgresos as $registro) {
            $semana = date("W", strtotime($registro->fecha));
            $tipo = $registro->tipo;
            $cantidad = $registro->cantidad;
    
            if (!isset($totalPorSemana[$semana])) {
                $totalPorSemana[$semana] = [];
            }
            
            // Verifica si la clave 'ingresos' existe en el subarray
            if (!isset($totalPorSemana[$semana]['ingreso'])) {
                $totalPorSemana[$semana]['ingreso'] = 0;
            }
            
            // Verifica si la clave 'egresos' existe en el subarray
            if (!isset($totalPorSemana[$semana]['egreso'])) {
                $totalPorSemana[$semana]['egreso'] = 0;
            }
    
            // Agrega la cantidad al total correspondiente
            $totalPorSemana[$semana][$tipo] += $cantidad;
        }
    
        return $totalPorSemana;
    }
    

}

