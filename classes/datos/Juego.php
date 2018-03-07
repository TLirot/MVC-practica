<?php

class Juego {

    private $id, $idusuario, $idcategoria, $nombre, $descripcion;
    
    function __construct($id = null, $idusuario = null, $idcategoria = null, $nombre = null, $descripcion = null) {
        $this->id = $id;
        $this->idusuario = $idusuario;
        $this->idcategoria = $idcategoria;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
    }
    
    function setId($id) { $this->id = $id; }
    function getId() { return $this->id; }
    function setIdusuario($idusuario) { $this->idusuario = $idusuario; }
    function getIdusuario() { return $this->idusuario; }
    function setIdcategoria($idcategoria) { $this->idcategoria = $idcategoria; }
    function getIdcategoria() { return $this->idcategoria; }
    function setNombre($nombre) { $this->nombre = $nombre; }
    function getNombre() { return $this->nombre; }
    function setDescripcion($descripcion) { $this->descripcion = $descripcion; }
    function getDescripcion() { return $this->descripcion; }
    
    
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