<?php

class ManageJuego {

    private $db;

    function __construct(DataBase $db) {
        $this->db = $db;
    }

    public function add(Juego $objeto) {
         $sql = 'insert into juego(idusuario, idcategoria, nombre, descripcion)
        values (:idusuario, :idcategoria, :nombre, :descripcion)';
        $params = array(
            'idusuario' => $objeto->getIdusuario(),
            'idcategoria'=> $objeto->getIdcategoria(),
            'nombre' => $objeto->getNombre(),
            'descripcion' => $objeto->getDescripcion()
        );
        
        $resultado = $this->db->execute($sql, $params);
        
        if($resultado) {
            $id = $this->db->getId();
        } else {
            $id = 0;
        }
        return $id;
    }
    
    public function edit(Juego $objeto) {
        $sql = 'update juego set idusuario = :idusuario, idcategoria = :idcategoria, nombre = :nombre, descripcion = :descripcion where id = :id';
        $params = array(
            'id' => $objeto->getId(),
            'idusuario'=> $objeto->getIdusuario(),
            'idcategoria' => $objeto->getIdcategoria(),
            'nombre' => $objeto->getNombre(),
            'descripcion' => $objeto->getDescripcion()
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
        $sql = 'delete from juego where id = :id';
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
        $sql = 'select * from juego where id = :id';
        $params = array(
            'id' => $id
        );
        $resultado = $this->db->execute($sql, $params);//true o false
        $sentencia = $this->db->getStatement();
        $objeto = new Juego();
        if($resultado && $fila = $sentencia->fetch()) {
            $objeto->set($fila);
        } else {
            $objeto = null;
        }
        return $objeto;
    }

    public function getFromCategoria($idcategoria) {
        $sql = 'select * from juego where idcategoria = :idcategoria';
        $params = array(
            'idcategoria' => $idcategoria
        );
        $resultado = $this->db->execute($sql, $params);//true o false
        $sentencia = $this->db->getStatement();
         $objetos = array();
        if($resultado){
            $sentencia = $this->db->getStatement();
            while($fila = $sentencia->fetch()) {
                $objeto = new Juego();
                $objeto->set($fila);
                $objetos[] = $objeto;
            }
        }
        return $objetos;
    }
    
    
    public function getAllFromUserAndCategory($idusuario) {
        $sql = 'select j.id, j.idusuario, c.categoria idcategoria, j.nombre, j.descripcion from juego j join categoria c on j.idcategoria = c.id where j.idusuario = :idusuario';
        $params = array(
            'idusuario' => $idusuario
            );
        $resultado = $this->db->execute($sql, $params);
        $objetos = array();
        if($resultado){
            $sentencia = $this->db->getStatement();
            while($fila = $sentencia->fetch()) {
                $objeto = new Juego();
                $objeto->set($fila);
                $objetos[] = $objeto;
            }
        }
        return $objetos;
    }

    function countJuego(){
        $sql = 'select count(*) from juego';
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