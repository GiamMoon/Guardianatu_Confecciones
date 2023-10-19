<?php
namespace Model;
class ActiveRecord {

    // Base DE DATOS
    protected static $db;
    protected static $tabla = '';
    protected static $columnasDB = [];

    // Alertas y Mensajes
    protected static $alertas = [];
    
    // Definir la conexión a la BD - includes/database.php
    public static function setDB($database) {
        self::$db = $database;
    }

    public static function setAlerta($tipo, $mensaje) {
        static::$alertas[$tipo][] = $mensaje;
    }

    // Validación
    public static function getAlertas() {
        return static::$alertas;
    }

    public function validar() {
        static::$alertas = [];
        return static::$alertas;
    }

    // Consulta SQL para crear un objeto en Memoria
    public static function consultarSQL($query) {
        // Consultar la base de datos
        $resultado = self::$db->query($query);

        // Iterar los resultados
        $array = [];
        while($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }

        // liberar la memoria
        $resultado->free();

        // retornar los resultados
        return $array;
    }

    public static function consultarSQLPre($query, $params = []) {
        // Consultar la base de datos con parámetros
        $stmt = self::$db->prepare($query);
    
        if ($stmt) {
            // Bind parameters
            if (!empty($params)) {
                $types = str_repeat('s', count($params)); // Assuming all parameters are strings
                $stmt->bind_param($types, ...$params);
            }
    
            // Execute the statement
            $stmt->execute();
    
            // Check for errors
            if ($stmt->error) {
                echo "Error en la ejecución de la consulta: " . $stmt->error;
                return false;
            }
    
            // Get results
            $resultado = $stmt->get_result();
    
            // Iterar los resultados
            $array = [];
            while ($registro = $resultado->fetch_assoc()) {
                $array[] = static::crearObjeto($registro);
            }
    
            // liberar la memoria
            $stmt->free_result();
    
            // cerrar la sentencia
            $stmt->close();
    
            // retornar los resultados
            return $array;
        } else {
            // Handle error
            echo "Error en la preparación de la consulta: " . self::$db->error;
            return false;
        }
    }

    
    
    

    // Crea el objeto en memoria que es igual al de la BD
    protected static function crearObjeto($registro) {
        $objeto = new static;

        foreach($registro as $key => $value ) {
            if(property_exists( $objeto, $key  )) {
                $objeto->$key = $value;
            }
        }

        return $objeto;
    }

    // Identificar y unir los atributos de la BD
    public function atributos() {
        $atributos = [];
        foreach(static::$columnasDB as $columna) {
            if($columna === 'id') continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    // Sanitizar los datos antes de guardarlos en la BD
    public function sanitizarAtributos() {
        $atributos = $this->atributos();
        $sanitizado = [];
        foreach($atributos as $key => $value ) {
            $sanitizado[$key] = self::$db->escape_string($value);
        }
        

        return $sanitizado;
    }

    // Sincroniza BD con Objetos en memoria
    public function sincronizar($args=[]) { 
        foreach($args as $key => $value) {
          if(property_exists($this, $key) && !is_null($value)) {
            $this->$key = $value;
          }
        }
    }

   

    // Registros - CRUD
    public function guardar() {
        $resultado = '';
        if(!is_null($this->id)) {
            // actualizar
            $resultado = $this->actualizar();
        } else {
            // Creando un nuevo registro
            $resultado = $this->crear();



        }
        return $resultado;

    }

    public function crearPresu() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();
    
        // Insertar en la base de datos
        $query = "INSERT INTO " . static::$tabla . " (";
        $query .= join(', ', array_keys($atributos));
        $query .= ") VALUES ('"; 
        $query .= join("', '", array_values($atributos));
        $query .= "')";
    
        // Resultado de la consulta
        $resultado = self::$db->query($query);
    
        if (!$resultado) {
            // Si hay un error, imprimir detalles del error
            echo "Error al ejecutar la consulta: " . self::$db->error;
        }
    
        return [
            'resultado' => $resultado,
            'id' => self::$db->insert_id
        ];
    }

    // Todos los registros
    public static function all() {
        $query = "SELECT * FROM " . static::$tabla;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Busca un registro por su id
    public static function find($id) {
        $query = "SELECT * FROM " . static::$tabla  ." WHERE id = {$id}";
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado ) ;
    }

    // Obtener Registros con cierta cantidad
    public static function get($limite) {
        $query = "SELECT * FROM " . static::$tabla . " LIMIT {$limite}";
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado ) ;
    }

    public static function where($columna, $valor) {
        $query = "SELECT * FROM " . static::$tabla  ." WHERE {$columna} = '{$valor}'";
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado ) ;
    }

public static function whereImagen($columna, $valor) {
    $query = "SELECT imagenes.*
              FROM imagenescliente AS imagenes
              WHERE imagenes.{$columna} = '{$valor}'";

    $resultados = self::consultarSQL($query);
    return $resultados;
}



    
    

    public static function whereTarea($condiciones) {
        $columna = key($condiciones);
        $valor = $condiciones[$columna];
    
        $query = "SELECT * FROM " . static::$tabla . " WHERE {$columna} = '{$valor}'";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }


    public static function whereno($columna, $valor) {
        $query = "SELECT * FROM " . static::$tabla  ." WHERE {$columna} = '{$valor}'";
        $resultado = self::consultarSQL($query);
        return $resultado ;
    }
    public static function whereAllFechas($columna, $valor, $columna2, $valor2) {
        $query = "SELECT * FROM " . static::$tabla . " WHERE {$columna} = '{$valor}' 
        AND {$columna2} >= '{$valor2} 00:00:00' AND {$columna2} <= '{$valor2} 23:59:59'";
        $resultado = self::consultarSQL($query);
        return $resultado;
    }
    

    public static function whereAllFechas2($columna, $valor) {
        $fechaInicio = "{$valor} 00:00:00";
        $fechaFin = "{$valor} 23:59:59";
    
        $query = "SELECT * FROM " . static::$tabla . "
                  WHERE {$columna} >= '{$fechaInicio}' AND {$columna} < '{$fechaFin}'";
        
        $resultado = self::consultarSQL($query);
        
        return $resultado;
    }
    

    public static function whereAllFechasTareas($columna, $valor) {
        $fechaInicio = "{$valor} 00:00:00";
        $fechaFin = "{$valor} 23:59:59";
    
        $query = "SELECT clientes.*, 
                         GROUP_CONCAT(tareas.nombre) AS nombres_tareas, 
                         GROUP_CONCAT(tareas.estado) AS estados_tareas
                  FROM " . static::$tabla . "
                  LEFT JOIN tareas ON clientes.id = tareas.cliente_id
                  WHERE clientes.{$columna} >= '{$fechaInicio}' 
                        AND clientes.{$columna} < '{$fechaFin}' 
                        AND tareas.fecha_creacion BETWEEN '{$fechaInicio}' AND '{$fechaFin}'
                  GROUP BY clientes.id";
        
        $resultado = self::consultarSQL($query);
        
        return $resultado;
    }

    public static function consultaSupervisor($estado, $fecha) {
        $fechaInicio = "{$fecha} 00:00:00";
        $fechaFin = "{$fecha} 23:59:59";
    
        $query = "SELECT clientes.*, usuarios.nombre AS nombre_usuario, usuarios.apellido AS apellido_usuario
                  FROM clientes
                  LEFT JOIN usuarios ON clientes.usuario_id = usuarios.id
                  WHERE confirmado = '{$estado}' 
                  AND clientes.fecha_creacion BETWEEN '{$fechaInicio}' AND '{$fechaFin}'";

                 
    
        $resultado = self::consultarSQL($query);
    
        // Iterar sobre los resultados y asignar las propiedades
        foreach ($resultado as $cliente) {
            $cliente->nombre_usuario = $cliente->nombre_usuario ?? '';
            $cliente->apellido_usuario = $cliente->apellido_usuario ?? '';
        }
    
        return $resultado;
    }
    

    public static function consultaSupervisorAprobado($fecha) {
        $fechaInicio = "{$fecha} 00:00:00";
        $fechaFin = "{$fecha} 23:59:59";
    
        $query = "SELECT clientes.*, usuarios.nombre AS nombre_usuario, usuarios.apellido AS apellido_usuario
                  FROM clientes
                  LEFT JOIN usuarios ON clientes.usuario_id = usuarios.id
                  WHERE confirmado = 1 
                  AND aprobar_envio = 1 
                  AND clientes.fecha_creacion BETWEEN '{$fechaInicio}' AND '{$fechaFin}'";
        
        $resultado = self::consultarSQL($query);
        
        // Iterar sobre los resultados y asignar las propiedades
        foreach ($resultado as $cliente) {
            $cliente->nombre_usuario = $cliente->nombre_usuario ?? '';
            $cliente->apellido_usuario = $cliente->apellido_usuario ?? '';
        }
    
        return $resultado;
    }
    

    public static function consultaSupervisorRechazado($fecha) {
        $fechaInicio = "{$fecha} 00:00:00";
        $fechaFin = "{$fecha} 23:59:59";
    
        $query = "SELECT clientes.*, usuarios.nombre AS nombre_usuario, usuarios.apellido AS apellido_usuario
                  FROM clientes
                  LEFT JOIN usuarios ON clientes.usuario_id = usuarios.id
                  WHERE (confirmado = 2 OR aprobar_envio = 2) 
                  AND clientes.fecha_creacion BETWEEN '{$fechaInicio}' AND '{$fechaFin}'";
        
        $resultado = self::consultarSQL($query);
        
        // Iterar sobre los resultados y asignar las propiedades
        foreach ($resultado as $cliente) {
            $cliente->nombre_usuario = $cliente->nombre_usuario ?? '';
            $cliente->apellido_usuario = $cliente->apellido_usuario ?? '';
        }
    
        return $resultado;
    }

    public static function findCliente($id) {
        $query = "SELECT * FROM " . static::$tabla . " WHERE id = ?";
        $params = [$id];
        $resultado = self::consultarSQL($query, $params);
    
        return !empty($resultado) ? array_shift($resultado) : null;
    }

    public static function consultaSupervisorFinal($valor) {
        $fechaInicio = "{$valor} 00:00:00";
        $fechaFin = "{$valor} 23:59:59";
    
        $query = "SELECT clientes.*, 
                         usuarios.nombre AS nombre_usuario, 
                         usuarios.apellido AS apellido_usuario,
                         supervisores.nombre AS nombre_supervisor,
                         supervisores.apellido AS apellido_supervisor,
                         despacho_usuario.nombre AS nombre_despacho,
                         despacho_usuario.apellido AS apellido_despacho
                  FROM clientes
                  LEFT JOIN usuarios ON clientes.usuario_id = usuarios.id
                  LEFT JOIN usuarios AS supervisores ON clientes.supervisor_id = supervisores.id
                  LEFT JOIN usuarios AS despacho_usuario ON clientes.despacho_id = despacho_usuario.id
                  WHERE clientes.fecha_creacion BETWEEN '{$fechaInicio}' AND '{$fechaFin}'";
    
        $resultado = self::consultarSQL($query);
    
        // Iterar sobre los resultados y asignar las propiedades
        foreach ($resultado as $cliente) {
            $cliente->nombre_usuario = $cliente->nombre_usuario ?? '';
            $cliente->apellido_usuario = $cliente->apellido_usuario ?? '';
            $cliente->nombre_supervisor = $cliente->nombre_supervisor ?? '';
            $cliente->apellido_supervisor = $cliente->apellido_supervisor ?? '';
            $cliente->nombre_despacho = $cliente->nombre_despacho ?? '';
            $cliente->apellido_despacho = $cliente->apellido_despacho ?? '';
        }
    
        return $resultado;
    }
    
    public static function consultaSupervisorEnvio($estado, $fecha) {
        $fechaInicio = "{$fecha} 00:00:00";
        $fechaFin = "{$fecha} 23:59:59";
    
        $query = "SELECT clientes.*, usuarios.nombre AS nombre_usuario, usuarios.apellido AS apellido_usuario
                  FROM clientes
                  LEFT JOIN usuarios ON clientes.usuario_id = usuarios.id
                  WHERE aprobar_envio = '{$estado}' 
                  AND clientes.fecha_creacion BETWEEN '{$fechaInicio}' AND '{$fechaFin}' 
                  AND clientes.aprobar_envio = 0 
                  AND clientes.confirmado = 1 
                  AND clientes.confirmar_envio = 0";
        
        $resultado = self::consultarSQL($query);
        
        // Iterar sobre los resultados y asignar las propiedades
        foreach ($resultado as $cliente) {
            $cliente->nombre_usuario = $cliente->nombre_usuario ?? '';
            $cliente->apellido_usuario = $cliente->apellido_usuario ?? '';
        }
    
        return $resultado;
    }
    
    



    public static function actualizarEstado($estado, $id, $idsuper) {
        // Formatea la fecha y hora según el formato de tu base de datos
    
        $query = "UPDATE " . static::$tabla . "
                  SET confirmado = '{$estado}',
                      supervisor_id = '{$idsuper}'
                  WHERE id = '{$id}'";
                  

        
        // Ejecuta la consulta de actualización
        self::$db->query($query);
    }

    public static function actualizarEstadoSuperEnvio($estado, $id, $idsuper) {
        // Formatea la fecha y hora según el formato de tu base de datos
    
        $query = "UPDATE " . static::$tabla . "
                  SET aprobar_envio = '{$estado}',
                      supervisor_id = '{$idsuper}'
                  WHERE id = '{$id}'";

        
        // Ejecuta la consulta de actualización
        self::$db->query($query);
    }

    public static function confirmarPago($estado, $id) {
        // Formatea la fecha y hora según el formato de tu base de datos
    
        $query = "UPDATE " . static::$tabla . "
                  SET confirmado = '{$estado}'
                  WHERE id = '{$id}'";
    
        // Ejecuta la consulta de actualización
        self::$db->query($query);
    }
    public static function actualizarEstadoEnvio($estado, $id, $fechaHora, $despachoID,$mensaje) {
        // Formatea la fecha y hora según el formato de tu base de datos
        $fechaHoraFormateada = date('Y-m-d H:i:s', strtotime($fechaHora));
    
        $query = "UPDATE " . static::$tabla . "
                  SET confirmar_envio = '{$estado}',
                      fechaEnvioHora = '{$fechaHoraFormateada}',
                      despacho_id = '{$despachoID}',
                      mensaje_vendedor = '{$mensaje}'
                  WHERE id = '{$id}'";
        
        // Ejecuta la consulta de actualización
        self::$db->query($query);
    }


    // crea un nuevo registro
    public function crear() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();


        // Insertar en la base de datos
        $query = " INSERT INTO " . static::$tabla . " ( ";
        $query .= join(', ', array_keys($atributos));
        $query .= " ) VALUES (' "; 
        $query .= join("', '", array_values($atributos));
        $query .= " ') ";
        
        // Resultado de la consulta
        $resultado = self::$db->query($query);

        return [
           'resultado' =>  $resultado,
           'id' => self::$db->insert_id
        ];

    }

    // Actualizar el registro
    public function actualizar() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Iterar para ir agregando cada campo de la BD
        $valores = [];
        foreach($atributos as $key => $value) {
            $valores[] = "{$key}='{$value}'";
        }

        // Consulta SQL
        $query = "UPDATE " . static::$tabla ." SET ";
        $query .=  join(', ', $valores );
        $query .= " WHERE id = '" . self::$db->escape_string($this->id) . "' ";
        $query .= " LIMIT 1 "; 

        // Actualizar BD
        $resultado = self::$db->query($query);
        return $resultado;
    }

    // Eliminar un Registro por su ID
    public function eliminar() {
        $query = "DELETE FROM "  . static::$tabla . " WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1";
        $resultado = self::$db->query($query);
        return $resultado;
    }

}