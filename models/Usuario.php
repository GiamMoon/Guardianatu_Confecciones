<?php

namespace Model;

class Usuario extends ActiveRecord{
    protected static $tabla = "usuarios";
    protected static $columnasDB = ["id", "nombre","apellido","email","password","rol"];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $rol;

    public function __construct($args = []){
        $this->id = $args["id"] ?? null;
        $this->nombre = $args["nombre"] ?? "";
        $this->apellido = $args["apellido"] ?? "";
        $this->email = $args["email"] ?? "";
        $this->password = $args["password"] ?? "";
        $this->rol = $args["rol"] ?? null;
    }

    //Mensajes de validacion
    public function validarNuevaCuenta(){
        if(!$this->nombre){
            self::$alertas["error"][] = "El nombre es obligatorio";
        }
        if(!$this->apellido){
            self::$alertas["error"][] = "El apellido es obligatorio";
        }

        if(!$this->email){
            self::$alertas["error"][] = "El Email es obligatorio";
        }
        if(!$this->password){
            self::$alertas["error"][] = "El Password es obligatorio";
        }
        if(strlen($this->password < 6)){
            self::$alertas["error"][] = "El password debe tener al menos 6 caracteres";
        }

        if(!$this->rol){
            self::$alertas["error"][] = "El Rol es obligatorio";
        }


        return self::$alertas;
    }

    public function validarLogin(){
        if(!$this->email){
            self::$alertas["error"][] = "El Email es obligatorio";
        }
        if(!$this->password){
            self::$alertas["error"][] = "El Passowrd es obligatorio";
        }

        return self::$alertas;
    }

    //Revisa si el usuario exite
    public function existeUsuario(){
        $query = " SELECT * FROM " . self::$tabla ." Where email = '" . $this->email. "' LIMIT 1";
        
        $resultado = self::$db->query($query);

        if($resultado->num_rows){
            self::$alertas["error"][] ="El Usuario ya esta registrado";
        }else{
            header("Location: /");
        }

        return $resultado;

    }

    public function hashPassword(){
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function comprobarPassword($password){
        $resultado = password_verify($password, $this->password);

        if(!$resultado){
            self::$alertas["error"][] = "Password incorrecto";
        }else{
            return true;
        }
    }
}