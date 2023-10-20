<?php

namespace Controllers;

use Model\Usuario;
use MVC\Router;

class LoginController{
    public static function login(Router $router){

        $alertas = [];
        
        if($_SERVER["REQUEST_METHOD"] === "POST"){
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if(empty($alertas)){
                $usuario = Usuario::where("email", $auth->email);
                if($usuario){
                    if($usuario->comprobarPassword($auth->password)){
                        //Autenticar al usuario
                        session_start();

                        $_SESSION["id"] = $usuario->id;
                        $_SESSION["nombre"] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION["email"] = $usuario->email;
                        $_SESSION["login"] = true;

                        //Redireccionamiento
                        if($usuario->rol === "1"){
                            $_SESSION["rol"] = $usuario->rol ?? null;
                            header("Location: /ventas");
                        }else if($usuario->rol === "2"){
                            $_SESSION["rol"] = $usuario->rol ?? null;
                            header("Location: /vendedor_dashboard");
                        }else if($usuario->rol === "3"){
                        $_SESSION["rol"] = $usuario->rol ?? null;
                            header("Location: /supervisor_dashboard");
                        }else if($usuario->rol === "4"){
                            $_SESSION["rol"] = $usuario->rol ?? null;
                            header("Location: /tareas_dashboard");
                        }else if($usuario->rol === "5"){
                            $_SESSION["rol"] = $usuario->rol ?? null;
                            header("Location: /tareas_pendientes");
                        }else if($usuario->rol === "6"){
                            $_SESSION["rol"] = $usuario->rol ?? null;
                            header("Location: /despacho_pendiente");
                        }else if($usuario->rol === "7"){
                            $_SESSION["rol"] = $usuario->rol ?? null;
                            header("Location: /costuras_pendientes");
                        }

                    }
                }else{
                    Usuario::setAlerta("error", "Usuario no encontrado");
                }
            }

        }

        $alertas = Usuario::getAlertas();

        $router->render("auth/login",[
            "alertas" => $alertas
        ]);

    }

    public static function logout(){
        session_start();
        $_SESSION = [];
        header("Location: /");
    }

    public static function crear(Router $router){

        isAuth();

        if ($_SESSION['rol'] !== '1') {
            // Si no tiene el rol necesario, redirigir o mostrar un mensaje de error
            header("Location: /sin_permisos");
            exit;
        }


        $usuario = new Usuario;
        //alertas vacias
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] ==='POST'){

            $usuario->sincronizar($_POST);
            $alertas =  $usuario->validarNuevaCuenta();

            if(empty($alertas)){
                $resultado = $usuario->existeUsuario();
                if($resultado->num_rows){
                    $alertas = Usuario::getAlertas();
                }else{

                    //Hashear password
                    $usuario->hashPassword();

                    //Crear el usuario
                    $resultado = $usuario->guardar();

                    header("Location: /ventas");

                }
            }

        }

        $router->render("auth/crear-cuenta",[
            "usuario" => $usuario,
            "alertas" => $alertas
        ]);
    }
}