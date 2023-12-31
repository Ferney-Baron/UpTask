<?php

namespace Controllers;
use Model\Proyecto;
use MVC\Router;

class DashboardController {
    public static function index(Router $router) {
        session_start();
        isAuth();

        $proyectos = Proyecto::belongsTo('PropietarioId', $_SESSION['id']);

        $router->render('dashboard/index', [
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]);
    }

    public static function crear_proyecto(Router $router) {
        session_start();
        isAuth();
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $proyecto = new Proyecto($_POST);

            $alertas = $proyecto->validarProyecto();

            if(empty($alertas)) {
                $hash = md5(uniqid());
                $proyecto->url = $hash;

                $proyecto->propietarioId = $_SESSION['id'];
                $proyecto->guardar();

                header('Location: /proyecto?id=' . $proyecto->url);
            }

            // debuguear($proyecto);
        }

        $router->render('dashboard/crear-proyecto', [
            'titulo' => 'Crear Proyecto',
            'alertas' => $alertas
        ]);
    }

    public static function proyecto(Router $router) {
        session_start();
        isAuth();

        $token = $_GET['id'];
        $alertas = [];

        if(!$token) header('Location: /dashboard');

        $proyecto = Proyecto::where('url', $token);

        if($proyecto->propietarioId !== $_SESSION['id']) {
            header('Location: /dashboard');
        }

        
        $router->render('dashboard/proyecto', [
            'titulo' => $proyecto->proyecto,
            'alertas' => $alertas
        ]); 
    }

    public static function perfil(Router $router) {
        session_start();
        isAuth();
        
        $router->render('dashboard/perfil', [
            'titulo' => 'Perfil'
        ]);
    }
}