<?php

class Categoria {

    private $id, $categoria, $idusuario;
    
    function __construct($id = null, $categoria = null, $idusuario = null) {
        $this->id = $id;
        $this->categoria = $categoria;
        $this->idusuario = $idusuario;
    }
    
    function setId($id) { $this->id = $id; }
    function getId() { return $this->id; }
    function setcategoria($categoria) { $this->categoria = $categoria; }
    function getcategoria() { return $this->categoria; }
    function setIdusuario($idusuario) { $this->idusuario = $idusuario; }
    function getIdusuario() { return $this->idusuario; }
    
    
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