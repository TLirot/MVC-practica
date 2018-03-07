<?php

class Enrutador {
    
    private $rutas = array();

    function __construct() {
        $this->rutas['index'] = new Ruta('ModeloUsuario', 'VistaUsuario', 'ControladorUsuario');
        $this->rutas['ajax'] = new Ruta('ModeloUsuario', 'VistaAjax', 'ControladorAjax');
        //añadir rutas
    }
    function getRoute($ruta) {
        if (!isset($this->rutas[$ruta])) {
            return $this->rutas['index'];
        }
        return $this->rutas[$ruta];
    }
}