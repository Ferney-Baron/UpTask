<?php

namespace Controllers;
use Model\Usuario;
use MVC\Router;

class LoginController {

    public static function login(Router $router) {

        if($_SERVER['REQUEST_METHOD'] === 'POST') {

        }

        // Render a la vista
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión',
        ]);
    }

    public static function logout() {
        echo 'Desde Logout';
    }
    public static function crear(Router $router) {

        $usuario = new Usuario;
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarCuentaNueva();

           if(empty($alertas)) {
                $existeUsuario = Usuario::where('email', $usuario->email);

                if($existeUsuario) {
                    Usuario::setAlerta('error', 'El Usuario ya esta registrado');
                    $alertas = Usuario::getAlertas();
                } else {
                    $usuario->hashPassword();

                    //Eliminar Password2
                    unset($usuario->password2);
                    $usuario->crearToken();

                    $resultado = $usuario->guardar();

                    if($resultado) {
                        header('Location: /mensaje');
                    }
                }
           }
        }

        $router->render('auth/crear', [
            'titulo' => 'Crear tu Cueta en UpTask',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router) {

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
        }

        $router->render('auth/olvide', [
            'titulo' => 'Recupera tu Cuenta',
        ]);
    }

    public static function reestablecer(Router $router) {
        

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
        }

        $router-> render('auth/reestablecer', [
            'titulo' => 'Reestablece tu Password',
        ]);
    }

    public static function mensaje(Router $router) {
        $router-> render('auth/mensaje', [
            'titulo' => 'Mensaje',
        ]);
    }

    public static function confirmar(Router $router) {
        $router-> render('auth/confirmar', [
            'titulo' => 'confirmar',
        ]);
    }
}