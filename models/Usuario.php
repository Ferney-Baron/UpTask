<?php 

namespace Model;

class Usuario extends ActiveRecord {

    protected static $tabla =  'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];

    public $id;
    public $nombre;
    public $email;
    public $password;
    public $password2;
    public $token;
    public $confirmado;

    public function __construct($args = []) {

        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
    }

    public function validarCuentaNueva() {
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El nombre de Usuario es Obligatorio';
        }

        if(!$this->email) {
            self::$alertas['error'][] = 'El Email del Usuario es Obligatorio';
        }

        if(!$this->password || strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El Password no puede ir vacio y Debe contener al menos 5 Caracteres';
        }

        if($this->password !== $this->password2) {
            self::$alertas['error'][] = 'Los Passwords son Diferentes';
        }

        return self::$alertas;
    }

    public function validarLogin() {
        if(!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no Valido';
        }

        if(!$this->password) {
            self::$alertas['error'][] = 'El Password es Obligatorio';
        }

        return self::$alertas;
    }
    
    public function validarEmail() {
        if(!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no Valido';
        }
        return self::$alertas;
    }

    public function validarPassword() {
        if(!$this->password || strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El Password no puede ir vacio y Debe contener al menos 5 Caracteres';
        }

        return self::$alertas;
    }

    public function compararPassword($hashPassword) {
        $password = password_verify($this->password, $hashPassword);

        return $password;
    }

    public function hashPassword() {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken() {
        $this->token = uniqid();
    }

    

}