<?php

/* vamos a dejarlo con sólo los métodos comunes a todos */

class Controlador {

    private $modelo;
    private $sesion;

    function __construct(Modelo $modelo) {
        $this->modelo = $modelo;
        $this->sesion = new Session('gestion');
        if($this->isLogged()) {
            $usuario = $this->getUser();
            $this->getModel()->setDato('usuario', $usuario->getCorreo());
        }
    }

    function getModel() {
        return $this->modelo;
    }
    
    function getSesion() {
        return $this->sesion;
    }

    function getUser() {
        return $this->getSesion()->getUser();
    }

    function isAdministrator() {
        if($this->isLogged() && $this->getSesion()->getUser()->getTipo() === 'admin'){
            return true;
        }
        return false;
    }

    function isAdvanced() {
        if($this->isLogged() && $this->getSesion()->getUser()->getTipo() === 'advanced'){
            return true;
        }
        return false;
    }
    
    function isNormal(){
        if($this->isLogged() && $this->getSesion()->getUser()->getTipo() === 'normal'){
            return true;
        }
        return false;
    }
    
    function isLogged() {
        return $this->getSesion()->isLogged();
    }

    function index() {
        $this->getModel()->setDato('contenido', file_get_contents('plantilla/bootstrap/_login.html'));
    }
    //normalmente en la base de datos en la tabla usuario
    //suele haber un campo tipo usuario: normal, administrador, etc.
  

}