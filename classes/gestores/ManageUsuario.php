<?php

class ManageUsuario {

    private $db;

    function __construct(DataBase $db) {
        $this->db = $db;
    }

    public function add(Usuario $objeto) {
         $sql = 'insert into usuario(nombre, apellidos, nick, correo, clave, tipo, fechaalta, verificado)
        values (:nombre, :apellidos, :nick, :correo, :clave, :tipo, :fechaalta, :verificado)';
        $params = array(
            'nombre' => $objeto->getNombre(),
            'apellidos'=> $objeto->getApellido(),
            'nick' => $objeto->getNick(),
            'correo' => $objeto->getCorreo(),
            'clave' => Util::encriptar($objeto->getClave()),
            'tipo' => 'normal',
            'fechaalta' => $objeto->getFechaalta(),
            'verificado' => '0'
        );
        
        $resultado = $this->db->execute($sql, $params);
        
        if($resultado) {
            $id = $this->db->getId();
        } else {
            $id = 0;
        }
        return $id;
    }
    
    public function addFromAdmin(Usuario $objeto) {
        
         $sql = 'insert into usuario(nombre, apellidos, nick, correo, clave, tipo, fechaalta, verificado)
        values (:nombre, :apellidos, :nick, :correo, :clave, :tipo, :fechaalta, :verificado)';
        $params = array(
            'nombre' => $objeto->getNombre(),
            'apellidos'=> $objeto->getApellido(),
            'nick' => $objeto->getNick(),
            'correo' => $objeto->getCorreo(),
            'clave' => Util::encriptar($objeto->getClave()),
            'tipo' => $objeto->getTipo(),
            'fechaalta' => $objeto->getFechaalta(),
            'verificado' => $objeto->getVerificado()
        );
        
        $resultado = $this->db->execute($sql, $params);
        
        if($resultado) {
            $id = $this->db->getId();
        } else {
            $id = 0;
        }
        return $id;
    }
    

    public function addUsuario(Usuario $objeto) {
        $sql = 'insert into usuario(correo, clave, verificado) values (:correo, :clave, 0)';
        $params = array(
            'correo' => $objeto->getCorreo(),
            'clave' => Util::encriptar($objeto->getClave())
        );
        $resultado = $this->db->execute($sql, $params);
        if($resultado) {
            $id = $this->db->getId();
            $objeto->setId($id);
        } else {
            $id = 0;
        }
        return $id;
    }

    public function edit(Usuario $objeto) {
        $sql = 'update usuario set nombre = :nombre, apellidos = :apellidos, nick = :nick, correo = :correo, clave = :clave, tipo = :tipo, verificado = :verificado where id = :id';
        $params = array(
            'id' => $objeto->getId(),
            'nombre' => $objeto->getNombre(),
            'apellidos'=> $objeto->getApellido(),
            'nick' => $objeto->getNick(),
            'correo' => $objeto->getCorreo(),
            'clave' => Util::encriptar($objeto->getClave()),
            'tipo' => $objeto->getTipo(),
            'verificado' => $objeto->getVerificado()
        );
        $resultado = $this->db->execute($sql, $params);
        if($resultado) {
            $filasAfectadas = $this->db->getRowNumber();
        } else {
            $filasAfectadas = -1;
        }
        return $filasAfectadas;
    }

    public function editSinClave(Usuario $objeto) {
        $sql = 'update usuario set nombre = :nombre, apellidos = :apellidos, nick = :nick, correo = :correo, tipo = :tipo, verificado = :verificado where id = :id';
        $params = array(
            'id' => $objeto->getId(),
            'nombre' => $objeto->getNombre(),
            'apellidos'=> $objeto->getApellido(),
            'nick' => $objeto->getNick(),
            'correo' => $objeto->getCorreo(),
            'tipo' => $objeto->getTipo(),
            'verificado' => $objeto->getVerificado()
        );
        $resultado = $this->db->execute($sql, $params);
        if($resultado) {
            $filasAfectadas = $this->db->getRowNumber();
        } else {
            $filasAfectadas = -1;
        }
        return $filasAfectadas;
    }

    public function editClave(Usuario $objeto) {
        $sql = 'update usuario set clave = :clave where id = :id';
        $params = array(
            'clave' => Util::encriptar($objeto->getClave()),
            'id' => $objeto->getId()
        );
        $resultado = $this->db->execute($sql, $params);
        if($resultado) {
            $filasAfectadas = $this->db->getRowNumber();
        } else {
            $filasAfectadas = -1;
        }
        return $filasAfectadas;
    }

    public function verifica(Usuario $objeto){
        $sql = 'update usuario set verificado = :verificado where id = :id';
        $params = array(
            'verificado' => $objeto->getVerificado(),
            'id' => $objeto->getId()
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
        $sql = 'select * from usuario where id = :id';
        $params = array(
            'id' => $id
        );
        $resultado = $this->db->execute($sql, $params);//true o false
        $sentencia = $this->db->getStatement();
        $objeto = new Usuario();
        if($resultado && $fila = $sentencia->fetch()) {
            $objeto->set($fila);
        } else {
            $objeto = null;
        }
        return $objeto;
    }

     public function getFromCorreo($correo) {
        $sql = 'select * from usuario where correo = :correo';
        $params = array(
            'correo' => $correo
        );
        $resultado = $this->db->execute($sql, $params);//true o false
        $sentencia = $this->db->getStatement();
        $objeto = new Usuario();
        if($resultado && $fila = $sentencia->fetch()) {
            $objeto->set($fila);
        } else {
            $objeto = null;
        }
        return $objeto;
    }
    
    public function getFromNick($nick) {
        $sql = 'select * from usuario where nick = :nick';
        $params = array(
            'nick' => $nick
        );
        $resultado = $this->db->execute($sql, $params);//true o false
        $sentencia = $this->db->getStatement();
        $objeto = new Usuario();
        if($resultado && $fila = $sentencia->fetch()) {
            $objeto->set($fila);
        } else {
            $objeto = null;
        }
        return $objeto;
    }

    public function getAll() {
        $sql = 'select * from usuario where 1';
        $resultado = $this->db->execute($sql);
        $objetos = array();
        if($resultado){
            $sentencia = $this->db->getStatement();
            while($fila = $sentencia->fetch()) {
                $objeto = new Usuario();
                $objeto->set($fila);
                $objetos[] = $objeto;
            }
        }
        return $objetos;
    }

        function countTipo($tipo){
        $sql = 'select count(*) from usuario where tipo=:tipo';
        $params = array(
            ':tipo' => $tipo
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
    
    function countUsers(){
        $sql = 'select count(*) from usuario';
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
    
    public function remove($id) {
        $sql = 'delete from usuario where id = :id';
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
}