<?php

namespace Model;

class Clientes extends ActiveRecord{
    protected static $tabla = "clientes";
    protected static $columnasDB = ["id","boleta", "nombres", "apellidos", "dni","telefono", "provincia", "distrito", "direccion",
    "fechaEnvio", "precioTotal", "adelanto", "restantePagar", "mensaje","fecha_creacion","usuario_id","confirmado","confirmar_envio",
    "supervisor_id", "fechaEnvioHora","despacho_id","pago_confirmado","mensaje_vendedor","aprobar_envio"];

    public $id;
    public $boleta;
    public $nombres;
    public $apellidos;
    public $dni;
    public $telefono;
    public $provincia;
    public $distrito;
    public $direccion;
    public $fechaEnvio;
    public $precioTotal;
    public $adelanto;
    public $restantePagar;
    public $mensaje;
    public $imagenes = [];
    public $fecha_creacion;
    public $usuario_id;
    public $confirmado;
    public $nombre_usuario;
    public $apellido_usuario;
    public $nombre_supervisor;

    public $apellido_supervisor;

    public $nombre_despacho;
    public $apellido_despacho;
    public $nombreTarea;
    public $confirmar_envio;
    public $nombres_tareas;
    public $supervisor_id;

    public $fechaEnvioHora;

    public $despacho_id;

    public $pago_confirmado;
    public $mensaje_vendedor;

    public $mensaje_tareas;
    public $mensaje_costura;

    public $aprobar_envio;
    

    public function __construct($args = []){
        $this->id = $args["id"] ?? "";
        $this->boleta = $args["boleta"] ?? "";
        $this->nombres = $args["nombres"] ?? "";
        $this->apellidos = $args["apellidos"] ?? "";
        $this->dni = $args["dni"] ?? "";
        $this->telefono = $args["telefono"] ?? "";
        $this->provincia = $args["provincia"] ?? "";
        $this->distrito = $args["distrito"] ?? "";
        $this->direccion = $args["direccion"] ?? "";
        $this->fechaEnvio = $args["fechaEnvio"] ?? date('Y-m-d H:i:s');
        $this->precioTotal = $args["precioTotal"] ?? 0;
        $this->adelanto = $args["adelanto"] ?? 0;
        $this->restantePagar = $args["restantePagar"] ?? 0;
        $this->mensaje = $args["mensaje"] ?? "";
        $this->imagenes = $args["imagenes"] ?? []; 
        $this->fecha_creacion = $args["fecha_creacion"] ?? date('Y-m-d H:i:s');
        $this->usuario_id = $args["usuario_id"] ?? "";
        $this->confirmado = $args["confirmado"] ?? 0;
        $this->confirmar_envio = $args["confirmar_envio"] ?? 0;
        $this->supervisor_id = $args["supervisor_id"] ?? "";
        $this->fechaEnvioHora = $args["fechaEnvioHora"] ?? ($this->fechaEnvio);
        $this->despacho_id = $args["despacho_id"] ?? "";
        $this->pago_confirmado = $args["pago_confirmado"] ?? 0;
        $this->mensaje_vendedor = $args["mensaje_vendedor"] ?? "";
        $this->aprobar_envio = $args["aprobar_envio"] ?? 0;

        

    }

    public function validarNuevoCliente(){
        if(!$this->boleta){
            self::$alertas["error"][] = "El numero de boleta es obligatorio";
        }
        if(!$this->nombres){
            self::$alertas["error"][] = "El nombre es obligatorio";
        }

        if(!$this->apellidos){
            self::$alertas["error"][] = "El apellido es obligatorio";
        }
        if(!$this->dni){
            self::$alertas["error"][] = "El DNI es obligatorio";
        }
        if(!$this->telefono){
            self::$alertas["error"][] = "El telefono es obligatorio";
        }
        if(!$this->provincia){
            self::$alertas["error"][] = "La provincia es obligatoria";
        }
        if(!$this->distrito){
            self::$alertas["error"][] = "El distrito es obligatorio";
        }
        if(!$this->direccion){
            self::$alertas["error"][] = "La direccion es obligatoria";
        }
        if(!$this->fechaEnvio){
            self::$alertas["error"][] = "La fecha es obligatoria";
        }
        if(!$this->precioTotal){
            self::$alertas["error"][] = "El precio total es obligatorio";
        }
        if(!$this->adelanto){
            self::$alertas["error"][] = "El adelanto es obligatorio";
        }
        if(!$this->restantePagar){
            self::$alertas["error"][] = "El restante a pagar es obligatorio";
        }
        if(!$this->mensaje){
            self::$alertas["error"][] = "El mensaje es obligatorio";
        }

        if (!empty($this->imagenes)) {
            foreach ($this->imagenes as $imagen) {
                $alertasImagen = $imagen->validadImagen();
                self::$alertas = array_merge(self::$alertas, $alertasImagen);
            }
        }


        

        $this->id = null;

        return self::$alertas;

        
    }

    
    public function confirmarPagoCompleto() {
        // Actualizar el valor de pago_confirmado a 1
        $this->pago_confirmado = 1;
        // Guardar los cambios en la base de datos
        $this->guardar();
    }

    public static function todos() {
        $query = "SELECT * FROM " . static::$tabla;
        $resultados = self::consultarSQL($query);
        return $resultados;
    }

    public function eliminar() {
        $query = "DELETE FROM "  . static::$tabla . " WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1";
        $resultado = self::$db->query($query);
        return $resultado;
    }


}