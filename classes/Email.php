<?php

namespace Classes;
use PHPMailer\PHPMailer\PHPMailer;

class Email {
    protected $email;
    protected $nombre;
    protected $token;

    public function __construct($email, $nombre, $token) {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion() {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '73b00f76649c7a';
        $mail->Password = '95b765dd122ca0';

        $mail->setFrom('admin@uptask.com');
        $mail->addAddress('admin@uptask.com', 'uptask.com');
        $mail->Subject = 'Confirma tu Cuenta';

        $mail->isHTML(true);
        $mail->CharSet = 'utf-8';

        $enlace = "http://localhost:3000/confirmar?token=" . $this->token ;

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->nombre . " </strong> Has Creado tu Cuenta en UpTask, solo debes confirmarla en el siguiente enlace</p>";
        $contenido .= "<p>Presiona Aquí: <a href='$enlace'>Activar Cuenta</a></p>";
        $contenido .= "Si no has solicitado la creación, ignora este mensaje";
        $contenido .= '<html>';

        $mail->Body = $contenido;

        $mail->send();

    }
}