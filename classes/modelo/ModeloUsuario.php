<?php

class ModeloUsuario extends Modelo {


     function registrar(Usuario $usuario) {
         $manager = new ManageUsuario($this->getDataBase());
         if($usuario->getVerificado() == null){
             $resultado = $manager->add($usuario);
             return $resultado;
         }
         $resultado = $manager->addFromAdmin($usuario);
         return $resultado;
        
    }


    function activarUsuario($id, $sha1IdCorreo) {
        $manager = new ManageUsuario($this->getDataBase());
        $usuarioBD = $manager->get($id);
        $r = -1;
        if($usuarioBD !== null) {
            $sha1 = sha1($usuarioBD->getId() . $usuarioBD->getCorreo());
            if($sha1IdCorreo === $sha1) {
                $usuarioBD->setVerificado(1);
                $r = $manager->verifica($usuarioBD);
            }
        }
        return $r;
    }


    function loguear(Usuario $usuario) {
        $manager = new ManageUsuario($this->getDataBase());
        if(Filter::isEmail($usuario->getCorreo())){
        $usuarioBD = $manager->getFromCorreo($usuario->getCorreo());
        } else{
            $usuarioBD = $manager->getFromNick($usuario->getNick());
        }
            if($usuarioBD != null){
                $r = Util::verificarClave($usuario->getClave(), $usuarioBD->getClave());
                if($r == 1 && $usuarioBD->getVerificado() == 1) {
                    return $usuarioBD;
                }
            }
            return null;
    }
    
    function bajaUsuario($id){
        $manager = new ManageUsuario($this->getDataBase());
        return $manager->remove($id);
        
    }
    
    function getFromCorreo($correo){
        $manager = new ManageUsuario($this->getDataBase());
        $usuarioDB = $manager->getFromCorreo($correo);
        if($usuarioDB !== null){
            return $usuarioDB;
        }
        return null;
    }
    
    function getFromId($id){
        $manager = new ManageUsuario($this->getDataBase());
        $usuarioDB = $manager->get($id);
        if($usuarioDB !== null){
            return $usuarioDB;
        }
        return null;
    }
    
    function countTipo($tipo){
        $manager = new ManageUsuario($this->getDataBase());
        return $manager->countTipo($tipo);
    }
    
    function getAll(){
        $manager = new ManageUsuario($this->getDataBase());
        $usuarios = [];
        $usuarios = $manager->getAll();
        return $usuarios;
    }
    
     function getUsuariosParaJson() {
        $usuarios = $this->getAll();
        $array = array();
        foreach($usuarios as $usua) {
            $usua->setClave('');
            $array[] = $usua->getAttributesValues();
        }
        return $array;
    }
    
    function updateUserNoPass($usuario){
        $manager = new ManageUsuario($this->getDataBase());
        return $manager->editSinClave($usuario);
    }
    
    function updateUserPass($usuario){
        $manager = new ManageUsuario($this->getDataBase());
        return $manager->edit($usuario);
    }
    
    function updatePass($id, $data, $clave){
        $manager = new ManageUsuario($this->getDataBase());
        $usuario = $manager->get($id);
        $usuario->setClave($clave);
        $dataV = sha1($usuario->getId().$usuario->getCorreo());
        if($usuario != null && $data === $dataV){
            return $manager->editClave($usuario);
        }else{
            return -1;
        }
    }
    
    function countUsers(){
        $manager = new ManageUsuario($this->getDataBase());
        return $manager->countUsers();
    }
    
    
    //--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    
    function getJuego($id){
        $manager = new ManageJuego($this->getDataBase());
        return $manager->get($id);
    }
    
    function removeJuego($id){
        $manager = new ManageJuego($this->getDataBase());
        return $manager->remove($id);
    }
    
    function getAllJuegosFromUserAndCategory($idusuario){
        $manager = new ManageJuego($this->getDataBase());
        $juegos = $manager->getAllFromUserAndCategory($idusuario);
        return $juegos;
    }
    
    function editJuego($juego){
        $manager = new ManageJuego($this->getDataBase());
        return $manager->edit($juego);
    }
    
    function addJuego($juego){
        $manager = new ManageJuego($this->getDataBase());
        return $manager->add($juego);
    }
    
    function getCategoria($id){
        $manager = new ManageCategoria($this->getDataBase());
        return $manager->get($id);
    }
    
    function removeCategoria($id){
        $manager = new ManageCategoria($this->getDataBase());
        return $manager->remove($id);
    }
    
    function getAllCategoriasFromUser($idusuario){
        $manager = new ManageCategoria($this->getDataBase());
        $categorias = $manager->getAllFromUser($idusuario);
        return $categorias;
    }
    
    function editCategoria($categoria){
        $manager = new ManageCategoria($this->getDataBase());
        return $manager->edit($categoria);
    }
    
    function addCategoria($categoria){
        $manager = new ManageCategoria($this->getDataBase());
        return $manager->add($categoria);
    }
    
    function countCategoria(){
        $manager = new ManageCategoria($this->getDataBase());
        return $manager->countCategoria();
    }
    
    function countJuego(){
        $manager = new ManageJuego($this->getDataBase());
        return $manager->countJuego();
    }
    
    
    
    
    
    /*function addUsuario(Usuario $usuario){
        $manager = new ManageUsuario($this->getDataBase());
        $resultado = $manager->addUsuario($usuario);
        if($resultado > 0) {
            return $usuario->getId();
        }
        return -1;
    }

    function verificarUsuario($id, $data) {
        $manager = new ManageUsuario($this->getDataBase());
        $usuarioBD = $manager->get($id);
        $r = -1;
        if($usuarioBD !== null) {
            $sha1 = sha1($usuarioBD->getId() . $usuarioBD->getCorreo());
            if($data === $sha1) {
                $usuarioBD->setVerificado(1);
                $r = $manager->editSinClave($usuarioBD);
            }
        }
        return $r;
    }*/
}