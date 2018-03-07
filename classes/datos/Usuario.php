<?php

class Usuario {

    private $id, $nombre, $apellido, $nick, $correo, $clave, $tipo, $fechaalta, $verificado;
    
    function __construct($id = null, $nombre = null, $apellido = null, $nick = null, $correo = null, $clave = null, $tipo = null, $fechaalta = null, $verificado = null) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->nick = $nick;
        $this->correo = $correo;
        $this->clave = $clave;
        $this->tipo = $tipo;
        $this->fechaalta = $fechaalta;
        $this->verificado = $verificado;
    }
    
/*-----------------get and set--------------------------------------------------------*/

    function getId() {
        return $this->id;
    }

    function getNombre(){
        return $this->nombre;
    }
    
    function getApellido(){
        return $this->apellido;
    }
    
    function getNick(){
        return $this->nick;
    }
    
    function getCorreo() {
        return $this->correo;
    }

    function getClave() {
        return $this->clave;
    }

    function getTipo(){
        return $this->tipo;
    }
    function getFechaalta(){
        return $this->fechaalta;
    }
    function getVerificado() {
        return $this->verificado;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    function setApellido($apellido){
        $this->apellido = $apellido;
    }

    function setNick($nick){
        $this->nick = $nick;
    }

    function setCorreo($correo) {
        $this->correo = $correo;
    }

    function setClave($clave) {
        $this->clave = $clave;
    }

    function setTipo($tipo){
        $this->tipo = $tipo;
    }

    function setFechaalta($fechaalta){
        $this->fechaalta = $fechaalta;
    }
    
    function setVerificado($verificado) {
        $this->verificado = $verificado;
    }
    
    /* -----------------------------------común a todas las clases------------------------------------ */

    function getAttributes(){
        $atributos = [];
        foreach($this as $atributo => $valor){
            $atributos[] = $atributo;
        }
        return $atributos;
    }

    function getValues(){
        $valores = [];
        foreach($this as $valor){
            $valores[] = $valor;
        }
        return $valores;
    }
    
    
    function getAttributesValues(){
        $valoresCompletos = [];
        foreach($this as $atributo => $valor){
            $valoresCompletos[$atributo] = $valor;
        }
        return $valoresCompletos;
    }
    
    function read(){
        foreach($this as $atributo => $valor){
            $this->$atributo = Request::read($atributo);
        }
    }
    
    function set(array $array, $pos = 0){
        foreach ($this as $campo => $valor) {
            if (isset($array[$pos]) ) {
                $this->$campo = $array[$pos];
            }
            $pos++;
        }
    }
    
    function setFromAssociative(array $array){
        foreach($this as $indice => $valor){
            if(isset($array[$indice])){
                $this->$indice = $array[$indice];
            }
        }
    }
    
    public function __toString() {
        $cadena = get_class() . ': ';
        foreach($this as $atributo => $valor){
            $cadena .= $atributo . ': ' . $valor . ', ';
        }
        return substr($cadena, 0, -2);
    }
}

/*----------------------------------------consultasBD-------------------------------------------*/
    
    