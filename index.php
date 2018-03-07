<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

date_default_timezone_set('Europe/Madrid');

require 'classes/AutoLoader.php';




$accion = '';
$ruta = '';

//Para ruta bonita y ruta normal //ruba bonita no implementada, finalmente se ha hecho en TPV
$urlParams = Request::read('urlparams');
$parametros = explode('/', $urlParams);
if(isset($parametros[0])) {
    $ruta = $parametros[0];
} else{
    $ruta = Request::read("ruta");
}
if(isset($parametros[1])) {
    $accion = $parametros[1];
} else{
    $accion = Request::read("accion");
}


$controladorFrontal = new ControladorFrontal($ruta);

$controladorFrontal->doAction($accion);
echo $controladorFrontal->doOutput($accion);