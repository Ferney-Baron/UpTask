<?php

namespace Controllers;
use Model\Proyecto;
use Model\Tarea;

class TareaController {
    public static function index() {
        $tareaId = $_GET['id'];

        if(!$tareaId) header('Location: /');

        $proyecto = Proyecto::where('url', $tareaId);

        session_start();

        if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']) header('Location: /404');

        $tareas = Tarea::belongsTo('proyectoId', $proyecto->id);

        echo json_encode(['tareas' => $tareas]);
    }

    public static function crear() {
        if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
            session_start();

            $url = $_POST['url'];
            $proyecto = Proyecto::where('url', $url);

            if( !$proyecto || $proyecto->propietarioId !== $_SESSION['id']) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensajse' => 'Hubo un Error al Agregar la tarea'
                ];
                echo json_encode($respuesta);
                return;
            } 

            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;
            $resultado = $tarea->guardar();
            $respuesta = [
                'tipo' => 'exito',
                'id' => $resultado['id'],
                'mensaje' => 'Tarea Creada Correctamente',
                'proyectoId' =>  $proyecto->id
            ];
            echo json_encode($respuesta);
        }
    }

    public static function actualizar() {
        if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

        }
    }

    public static function eliminar() {
        if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

        }
    }

}