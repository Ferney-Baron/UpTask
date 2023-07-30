<?php

namespace Controllers;
use Classes\Email;
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

                    //Enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();

                    // debuguear($email);

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

        $token = s($_GET['token']);

        if(!$token) header('Location: /');

        //Encontrar al usuario con el token
        $usuario = Usuario::where('token', $token);

        // debuguear($usuario);

        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token no Valido');
        } else {
            $usuario->confirmado = 1;
            $usuario->token = '';
            unset($usuario->password2); 
            
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');
        }

        $alerta = Usuario::getAlertas();

        $router-> render('auth/confirmar', [
            'titulo' => 'confirmar',
            'alertas' => $alerta
        ]);
    }
}                       