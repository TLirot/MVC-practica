<?php

class ControladorAjax extends Controlador {
    
    function listadocompleto() {
        if($this->isAdministrator()){
            $usuarios = $this->getModel()->getUsuariosParaJson();
            $this->getModel()->setDato('listado', $usuarios);
        }
    }
    
    function registro(){
        $usuario = new Usuario();
        $usuario->read();
        $claveRepetida = Request::read('reClave');
        $resultado = -1;
        if(Filter::isEmail($usuario->getCorreo()) && $usuario->getClave() === $claveRepetida && $claveRepetida !== '') {
            $resultado = $this->getModel()->registrar($usuario);
        }
        $this->getModel()->setDato('respuesta', array('r' => $resultado)); //Es un array porque tiene que ser json.
    }
}
/*
Tengo un objeto Usuario, lo convierto en un array asociativo (getAttributesValues)
En la vista hago json_encode de ese array asociativo y se convierte a JSON.
*/