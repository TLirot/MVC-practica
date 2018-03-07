<?php

class VistaAjax extends Vista {

    function render($accion) {
         header('Content-Type: application/json');
        return json_encode($this->getModel()->getDatos());
    }
}