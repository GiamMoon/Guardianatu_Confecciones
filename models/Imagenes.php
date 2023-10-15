<?php

namespace Model;

class Imagenes extends ActiveRecord{

    protected static $tabla = "imagenescliente";

    protected static $columnasDB = ["id", "clienteImagen_id", "imagen_path"];

    public $id;
    public $clienteImagen_id;
    public $imagen_path;

    public function __construct($args = []){
        $this->id = $args["id"] ?? null;
        $this->clienteImagen_id = $args["clienteImagen_id"] ?? "";
        $this->imagen_path = $args["imagen_path"] ?? "";
    }

    public function setImagen($imagen){
        $this->imagen_path = $imagen;
    }

    public function validadImagen(){
        if(!$this->imagen_path){
            self::$alertas["error"][] = "La imagen  es obligatoria";
        }

        return self::$alertas;
    }



    public function guardar() {
        // Ajustar el clienteImagen_id antes de guardar
        $this->clienteImagen_id = $this->clienteImagen_id ?? "";
        return parent::guardar();
    }


}