<?php

namespace Controllers;
use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {

    public static function login(Router $router) {

        $usuario = new Usuario();

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
        
            if(empty($usuario->validarLogin())) {
                $usuarioExiste = Usuario::where('email', $usuario->email);

                if(!$usuarioExiste || !$usuarioExiste->confirmado) {
                    // debuguear($usuario);
                    Usuario::setAlerta('error', 'El usuario no existe o no esta Confirmado');
                } else {
                    $password = $usuario->compararPassword($usuarioExiste->password);
    
                    if($password) {
                        session_start();
                        $_SESSION['id'] = $usuarioExiste->id;
                        $_SESSION['nombre'] = $usuarioExiste->nombre;
                        $_SESSION['email'] = $usuarioExiste->email;
                        $_SESSION['login'] = true;

                        header('Location: /dashboard');
                    } else {
                        Usuario::setAlerta('error', 'Password Incorrecta');
                    }
                }
            }
        }

        $alertas = Usuario::getAlertas();
        // Render a la vista
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas,
            'usuario' => $usuario
        ]);
    }

    public static function logout() {
        session_start();
        $_SESSION = [];
        header('Location: /');
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

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();
            
            if(empty($alertas)) {
                $usuario = Usuario::where('email', $usuario->email);

                // debuguear($usuario
                
                if($usuario && $usuario->confirmado) {
                    unset($usuario->password2);
                    $usuario->crearToken();
                    $usuario->guardar();
                    $mail = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $mail->enviarRecuperacion();
                    Usuario::setAlerta('exito', 'Hemos envido las instrucciones a tu Email');
                } else {
                    Usuario::setAlerta('error', 'El usuario no existe o no esta Confirmado');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/olvide', [
            'titulo' => 'Recupera tu Cuenta',
            'alertas' => $alertas,
        ]);
    }

    public static function reestablecer(Router $router) {
        
        $token = s($_GET['token']);
        $mostrar = true;

        if(!$token) header('Location: /');

        $usuario = Usuario::where('token', $token);
        
        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token not found');
            $mostrar = false;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarPassword();

            if(empty($alertas)) {
                $usuario->hashPassword();
                $usuario->token = '';
                $resultado = $usuario->guardar();
               if($resultado) {
                    header('Location: /');
                }
            } 
        }

        $alertas = Usuario::getAlertas();

        $router-> render('auth/reestablecer', [
            'titulo' => 'Reestablece tu Password',
            'alertas' => $alertas,
            'mostrar' => $mostrar
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