<?php

class ManageCategoria {

    private $db;

    function __construct(DataBase $db) {
        $this->db = $db;
    }

    public function add(Categoria $objeto) {
         $sql = 'insert into categoria(categoria, idusuario)
        values (:categoria, :idusuario)';
        $params = array(
            'categoria' => $objeto->getCategoria(),
            'idusuario' => $objeto->getIdusuario()
        );
        
        $resultado = $this->db->execute($sql, $params);
        
        if($resultado) {
            $id = $this->db->getId();
        } else {
            $id = 0;
        }
        return $id;
    }
    
    public function edit(Categoria $objeto) {
        $sql = 'update categoria set categoria = :categoria where id = :id';
        $params = array(
            'id' => $objeto->getId(),
            'categoria'=> $objeto->getCategoria()
        );
        $resultado = $this->db->execute($sql, $params);
        if($resultado) {
            $filasAfectadas = $this->db->getRowNumber();
        } else {
            $filasAfectadas = -1;
        }
        return $filasAfectadas;
    }
    
    public function remove($id) {
        $sql = 'delete from categoria where id = :id';
        $params = array(
            'id' => $id
        );
        $resultado = $this->db->execute($sql, $params);
        if($resultado) {
            $filasAfectadas = $this->db->getRowNumber();
        } else {
            $filasAfectadas = -1;
        }
        return $filasAfectadas;
    }

    public function get($id) {
        $sql = 'select * from categoria where id = :id';
        $params = array(
            'id' => $id
        );
        $resultado = $this->db->execute($sql, $params);//true o false
        $sentencia = $this->db->getStatement();
        $objeto = new Categoria();
        if($resultado && $fila = $sentencia->fetch()) {
            $objeto->set($fila);
        } else {
            $objeto = null;
        }
        return $objeto;
    }
    
    public function getAllFromUser($idusuario) {
        $sql = 'select * from categoria where idusuario = :idusuario';
        $params = array(
            'idusuario' => $idusuario
            );
        $resultado = $this->db->execute($sql, $params);
        $objetos = array();
        if($resultado){
            $sentencia = $this->db->getStatement();
            while($fila = $sentencia->fetch()) {
                $objeto = new Categoria();
                $objeto->set($fila);
                $objetos[] = $objeto;
            }
        }
        return $objetos;
    }

    function countCategoria(){
        $sql = 'select count(*) from categoria';
        $params = array(

            );
        $res = $this->db->execute($sql, $params);
        $cuenta = 0;
        if($res){
            $sentencia = $this->db->getStatement();
            while($fila = $sentencia->fetch()){
                $cuenta = $fila[0];
            }
        }
        return $cuenta;
    }
}