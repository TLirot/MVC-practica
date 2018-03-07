<?php

class ControladorUsuario extends Controlador {

    function __construct(Modelo $modelo) {
        parent::__construct($modelo);
    }

    function index() {
        $mensaje = Request::read('mensaje');
        $this->getModel()->setDato('mensaje', $mensaje);
        if($this->isLogged()){
            $this->getModel()->setDato('archivo', '_index_logueado.html');
            $this->getModel()->setDato('correo', $this->getSesion()->getUser()->getNick());
            $this->getModel()->setDato('tipo', $this->getSesion()->getUser()->getTipo());
            if($this->isAdministrator()) {
            $resultadoAccion = Request::read('resultadoAccion');
                $enlace = '<h2><a href="?accion=administrar&resultadoAccion'.$resultadoAccion.'">Administrar</a></h2>';
                $this->getModel()->setDato('indexadministrador', $enlace);
            }
            //PINTAMOS LISTADO JUEGOS
            $page = Request::read('page');
            $rpp = 3; //Respuesta por página para la paginación;
            $this->getModel()->setDato('paginacion', $this->pagination($rpp, true));
            $lineaJuego = '<tr>
                             <td>{{nombre}}</td>
                             <td>{{descripcion}}</td>
                             <td>{{idcategoria}}</td>
                             <td><a class="linkAdminUser" href="?ruta=index&accion=plantillaFormJuego&id={{id}}">editar</a></td>
                             <td><a class="linkAdminUser" href="?ruta=index&accion=removeJuego&id={{id}}">borrar</a></td>
                    </tr>
                    ';
            $juegos = $this->getModel()->getAllJuegosFromUserAndCategory($this->getSesion()->getUser()->getId());
            $todoJuego = '';
            //Calculamos comienzo de la paginación, segun el numero de pagina y el rpp.
            if($page == null){
                $start = 0;
            }else{
                $start=($rpp*$page)-$rpp;    //Fórmula para calcular el inicio del bucle de paginación para sacar los usuarios correspondientes
            }
            for($i = 0; $i<$rpp; $i++){
                if(isset($juegos[$start+$i])){
                //vamos a meter la categoria del juego, por cada juego.
                //$categoria = $this->getModel()->getCategoria($juegos[$start+$i]->getIdcategoria())->getcategoria();
                //echo $categoria;exit;
                //$juegos[$start+$i]['categoria'] = $categoria;
                $r = Util::renderText($lineaJuego, $juegos[$start+$i]->getAttributesValues());
                $todoJuego .= $r;
                }
                else{
                    break;
                }
            }
            $this->getModel()->setDato('lineasJuego', $todoJuego);
            //PINTAMOS LISTADO CATEGORIA
            $lineaCategoria = '<tr>
                             <td>{{categoria}}</td>
                             <td><a class="linkAdminUser" href="?ruta=index&accion=plantillaFormCategoria&id={{id}}">editar</a></td>
                             <td><a class="linkAdminUser" href="?ruta=index&accion=removeCategoria&id={{id}}">borrar</a></td>
                    </tr>
                    ';
            $categorias = $this->getModel()->getAllCategoriasFromUser($this->getSesion()->getUser()->getId());
            $todoCategoria = '';
            foreach($categorias as $categoria){
            $r = Util::renderText($lineaCategoria, $categoria->getAttributesValues());
                $todoCategoria .= $r;
            }
            $this->getModel()->setDato('lineasCategoria', $todoCategoria);
        } else {
            $this->getModel()->setDato('archivo', '_index_nologueado.html');
            //tengo sólo 1 dato en el modelo: archivo
        }
    }
    
    function registrar(){
        //hacer el trabajo y luego una redirección
        $usuario = new Usuario();
        $usuario->read();
        $claveRepetida = Request::read('reClave');
        $datetime = new DateTime();
        $fechaalta = $datetime->format('Y-m-d H:i:s');
        $usuario->setFechaalta($fechaalta);
                                                                                                                                        //echo Util::varDump($usuario);
                                                                                                                                        //exit;
        $resultado = -1;
        if(Filter::isEmail($usuario->getCorreo()) && $usuario->getClave() === $claveRepetida && $claveRepetida !== '') {
            $resultado = $this->getModel()->registrar($usuario);
            if($resultado>0){
                $r = $this->eCorreoVerificacion($resultado, $usuario->getCorreo());
                if($r == 1){
                    $mensaje = 'Se ha registrado correctamente, verifique su correo en su bandeja de entrada.';
                }else{
                    $mensaje = 'Error al enviar el correo de verificación.';
                }
            } else{
                $mensaje = 'Ya existe un usuario con ese correo o nick';
            }
        } else{
            $mensaje = 'El correo introducido es inválido o las claves no coinciden';
        }
        header('Location: index.php?mensaje=' . $mensaje);
        exit();
    }
    
    function eCorreoVerificacion($id, $correo){
        $enlace = '<a href="https://prueba-dewes-toril92.c9users.io/MVC2018/index.php?ruta=index&accion=activar&id=' . $id . '&data=' . sha1($id.$correo). '">activate</a>';
        echo $id.$correo;
            $r = Util::enviarCorreo(Constants::CORREO, Constants::APPNAME, 'Mensaje con el enlace de activación: ' . $enlace);
            exit;
            return $r; 
    }
    
    function eCorreoRecuperacion($id, $correo){
        $enlace = '<a href="https://prueba-dewes-toril92.c9users.io/MVC2018/index.php?ruta=index&accion=recuperarPlantilla&id=' . $id . '&data=' . sha1($id.$correo). '">Recupera contraseña</a>';
            $r = Util::enviarCorreo(Constants::CORREO, Constants::APPNAME, 'Mensaje con el enlace de activación: ' . $enlace);
            return $r; 
    }
    
    function editarusuario(){
        if($this->isLogged()){
            if($this->isAdministrator()){
                $this->editaradministrador();
            } else {
                $this->editarusuarionoadmin();
            }
        }else{
            $this->index();
        }
    }
    
    function editaradministrador(){
        if($this->isLogged() && $this->isAdministrator()){
            $mensaje = Request::read('resultadoAccion');
            $this->getModel()->setDato('archivo' , '_editar_admin.html');
            $this->getModel()->setDato('nombre' , $this->getSesion()->getUser()->getNombre());
            $this->getModel()->setDato('apellidos' , $this->getSesion()->getUser()->getApellido());
            $this->getModel()->setDato('nick' , $this->getSesion()->getUser()->getNick());
            $this->getModel()->setDato('correo' , $this->getSesion()->getUser()->getCorreo());
            $this->getModel()->setDato('tipo' , $this->getSesion()->getUser()->getTipo());
            $this->getModel()->setDato('fechaalta', $this->getSesion()->getUser()->getFechaalta());
            $this->getModel()->setDato('verificado' ,$this->getSesion()->getUser()->getVerificado());
            
            $this->getModel()->setDato('mensaje', $mensaje);
        }else{
            $this->index();
        }
    }
    
    function doeditaradmin(){
        if($this->isAdministrator()){
            $oldClave = Request::read('oldClave');
            //Miro si la clave antigua es la de la sesión para seguridad en los cambios.
            if(Util::verificarClave($oldClave, $this->getSesion()->getUser()->getClave())){
                $usuario = new Usuario();
                $usuario->read();
                $reClave = Request::read('reClave');
                //Añadimos al usuario nuevo el id del el de la sesión para poder hacer el update.
                $usuario->setId($this->getSesion()->getUser()->getId());
                 //Habiendo leido el usuario compruebo que es un correo valido y el nick no está vacio.
                if(Filter::isEmail($usuario->getCorreo()) && $usuario->getNick()!=''){
                    if($usuario->getClave() == ''){
                    //edito sin clave.
                            $r = $this->getModel()->updateUserNoPass($usuario);
                    } else if($reClave === $usuario->getClave()){
                            //edito con clave.
                            $r = $this->getModel()->updateUserPass($usuario);
                            $usuario->setFechaalta($this->getSesion()->getUser()->getFechaalta());
                            $this->getSesion()->logout();
                            $this->getSesion()->login($usuario);
                        } else{
                            $mensaje = 'Las clave nueva no coincide con su repetición. No se aplicarán los cambios.';
                        }
                } else{
                    $mensaje = 'No es una cuenta de correo válida. O el nick está vacío';
                }
            }else{
                $mensaje = 'Su clave antigua no coincide. No se aplicarán los cambios.';
            }
            
            
            if($r == 1 && $mensaje == null){
                $mensaje = 'Editado correctamente';
            } else if ($r == 0 && $mensaje == null){
                $mensaje = 'El nick o el correo pertenecen ya a otro usuario';
            } else if ($r == -1 && $mensaje == null){
                $mensaje = 'Error al editar';
            }
            
            
            header('Location: index.php?accion=editaradministrador&resultadoAccion='.$mensaje);
        } else{
        $this->index();
        }   
    }
    
    function editarusuarionoadmin(){
        if($this->isLogged()){
            $mensaje = Request::read('resultadoAccion');
             $this->getModel()->setDato('archivo' , '_editar_usuario.html');
            $this->getModel()->setDato('nombre' , $this->getSesion()->getUser()->getNombre());
            $this->getModel()->setDato('apellidos' , $this->getSesion()->getUser()->getApellido());
            $this->getModel()->setDato('nick' , $this->getSesion()->getUser()->getNick());
            $this->getModel()->setDato('correo' , $this->getSesion()->getUser()->getCorreo());
            $this->getModel()->setDato('fechaalta', $this->getSesion()->getUser()->getFechaalta());
            //$this->getModel()->setDato('tipo' , $this->getSesion()->getUser()->getTipo());
            //$this->getModel()->setDato('verificado' ,$this->getSesion()->getUser()->getVerificado());
            $this->getModel()->setDato('mensaje', $mensaje);
        }else{
            $this->index();
        }
    }
    
    function doeditarnoadmin(){
        if($this->isLogged()){
            $usuario = new Usuario();
            $usuario->read();
            //Incluyo los campos que no me vienen del formulario.
            $usuario->setId($this->getSesion()->getUser()->getId());
            $usuario->setFechaalta($this->getSesion()->getUser()->getFechaalta());
            $usuario->setVerificado($this->getSesion()->getUser()->getVerificado());
            $usuario->setTipo($this->getSesion()->getUser()->getTipo());
            
            $oldClave = Request::read('oldClave');
            $reClave = Request::read('reClave');
            
            if(Util::verificarClave($oldClave, $this->getSesion()->getUser()->getClave())){
                if(Filter::isEmail($usuario->getCorreo()) && $usuario->getNick() != ''){
                    if($usuario->getClave() == ''){
                        //SIN CLAVE --> NORMAL y ADVANCED
                        $r = $this->getModel()->updateUserNoPass($usuario);
                    } else if($reClave === $usuario->getClave()){
                        //CON CLAVE
                        if($this->isNormal()){
                            //NORMAL
                            $usuario->setVerificado('0');
                            $r = $this->getModel()->updateUserPass($usuario);
                            $this->eCorreoVerificacion($usuario->getId(), $usuario->getCorreo());
                            $mensaje = 'Se ha enviado un correo de verificación por el cambio de clave. Verifique su correo o no podrá volver a hacer login';
                            $this->getSesion()->logout();
                            header('Location: index.php?mensaje='.$mensaje);
                            exit;
                        } else{
                            //ADVANCED
                            $r = $this->getModel()->updateUserPass($usuario);
                        }
                    } else{
                        $mensaje = 'Las clave nueva no coincide con su repetición. No se aplicarán los cambios.';
                    }
                } else{
                    $mensaje = 'El correo es invalido o el nick está vacio.';
                }
            } else{
                $mensaje = 'La clave antigua no coincide';
            }
            
            
             if($r == 1 && $mensaje == null){
                $mensaje = 'Editado correctamente';
            } else if ($r == 0 && $mensaje == null){
                $mensaje = 'El nick o el correo pertenecen ya a otro usuario';
            } else if ($r == -1 && $mensaje == null){
                $mensaje = 'Error al editar';
            }
            
            
            header('Location: index.php?accion=editarusuarionoadmin&resultadoAccion='.$mensaje);
        } else{
            $this->index();
        }
    }
   
    function administrar() {
        if($this->isAdministrator()) {
            $resultadoAccion = Request::read('resultadoAccion');
            $page = Request::read('page');
            $this->getModel()->setDato('archivo' , '_administrar_usuarios.html');
            $this->getModel()->setDato('resultadoAccion' , $resultadoAccion);
            $rpp = 3; //Respuesta por página para la paginación;
            $this->getModel()->setDato('paginacion', $this->pagination($rpp));
            
            $linea = '<tr>
                             <td>{{nombre}}</td>
                             <td>{{apellido}}</td>
                             <td>{{nick}}</td>
                             <td>{{correo}}</td>
                             <td>{{tipo}}</td>
                             <td>{{verificado}}</td>
                             <td>{{fechaalta}}</td>
                             <td><a class="linkAdminUser" href="?ruta=index&accion=editUsuarioFromAdminPlantilla&id={{id}}">editar</a></td>
                             <td><a class="linkAdminUser" href="?ruta=index&accion=bajaUsuario&id={{id}}">borrar</a></td>
                    </tr>
                    ';
            $usuarios = $this->getModel()->getAll();
            $todo = '';
            //Calculamos comienzo de la paginación, segun el numero de pagina y el rpp.
            if($page == null){
                $start = 0;
            }else{
                $start=($rpp*$page)-$rpp;    //Fórmula para calcular el inicio del bucle de paginación para sacar los usuarios correspondientes
            }
            for($i = 0; $i<$rpp; $i++){
                if(isset($usuarios[$start+$i])){
                $r = Util::renderText($linea, $usuarios[$start+$i]->getAttributesValues());
                $todo .= $r;
                }
                else{
                    break;
                }
            }
            $this->getModel()->setDato('lineasUsuario', $todo);
        } else {
            $this->index();
        }
    }
    
    function bajaUsuario(){
        if($this->isLogged()){
            $id = Request::read('id');
            if($id != '' && $this->isAdministrator()){
                //Borro usuario de la lista de usuarios.
                $usuarioDB = $this->getModel()->getFromId($id);
                if($usuarioDB->getTipo() === 'admin'){
                    //si es administrador el usuario a borrar no se puede.
                    $mensaje = 'No puede eliminar a un usuario administrador';
                    header('Location: index.php?accion=administrar&resultadoAccion='.$mensaje);
                    exit;
                }
                $r = $this->getModel()->bajaUsuario($usuarioDB->getId());
                if($r>0){
                    $mensaje = 'Baja del usuario '.$usuarioDB->getNick().' realizada correctamente';
                    
                } else{
                    $mensaje = 'Error al realizar la baja de'.$usuarioDB->getNick();
                }
                header('Location: index.php?accion=administrar&resultadoAccion='.$mensaje);
                exit;
            } else{
                //Borro el usuario de la sesión
                if($this->getModel()->countTipo($this->getSesion()->getUser()->getTipo())>1){
                    $r = $this->getModel()->bajaUsuario($this->getSesion()->getUser()->getId());
                    if($r>0){
                        $this->getSesion()->logout();
                        $mensaje = 'Ha dado de baja su cuenta correctamente.';
                    }else{
                        $mensaje = 'Ha habido un error al dar de baja su cuenta';
                    }
                    
                }else{
                    $mensaje = 'Es el último usuario administrador y no puede darse de baja.';
                }
                header('Location:index.php?mensaje='.$mensaje);
                exit;
            }
        }else{
            $this->index();
        }
    }
    
    function addUsuarioFromAdminPlantilla(){
        if($this->isAdministrator()){
            $this->getModel()->setDato('archivo', '_añadir_usuario_from_admin.html');
        }else{
            $this-index();
        }
    }
    
    function addUsuarioFromAdmin(){
        if($this->isAdministrator()){
            $admClave = Request::read('admClave');
            $usuario = new Usuario();
            $usuario->read();
            $reClave = Request::read('reClave');
            $datetime = new DateTime();
            $fechaalta = $datetime->format('Y-m-d H:i:s');
            $usuario->setFechaalta($fechaalta);
            
            //Verifico Campos
            if(Filter::isEmail($usuario->getCorreo()) &&
                Util::verificarClave($admClave, $this->getSesion()->getUser()->getClave()) &&
                $usuario->getNick()!='' &&
                $reClave != '' &&
                $usuario->getClave()===$reClave &&
                $usuario->getTipo() != ''){
                    //INSERT
                   $r = $this->getModel()->registrar($usuario);
                }else{
                    //NO INSERT
                    $mensaje = 'Fallo al añadir. <br>
                                <br>
                                Pueden pasar las siguiente cosas: <br>
                                1º El correo no es correcto. <br>
                                2º El nick está vacio. <br>
                                3º La clave de administrador no es correcta. <br>
                                4º La clave está vacia. <br>
                                5ª Las claves no coinciden.
                                6º El tipo de usuario quedo vacio.<br>
                                <br>
                                Por favor verifique todos los campos.';
                }  
                
                
        if($mensaje == null){
        if($r > 0){
                $mensaje = 'Añadido correctamente';
            } else if ($r == 0){
                $mensaje = 'El nick o el correo pertenecen ya a otro usuario';
            } else if ($r == -1){
                $mensaje = 'Error al añadir';
            }
        }
        header('Location: index.php?accion=administrar&resultadoAccion='.urlencode($mensaje));
        exit;
            } else {
                $this->index();
        }
    }
    
    function editUsuarioFromAdminPlantilla(){
        if($this->isAdministrator()){
            $id = Request::read('id');
            $usuario = $this->getModel()->getFromId($id);
            $this->getModel()->setDato('id', $usuario->getId());
            $this->getModel()->setDato('archivo', '_editar_usuario_from_admin.html');
            $this->getModel()->setDato('nombre', $usuario->getNombre());
            $this->getModel()->setDato('apellidos', $usuario->getApellido());
            $this->getModel()->setDato('nick', $usuario->getNick());
            $this->getModel()->setDato('correo', $usuario->getCorreo());
            $this->getModel()->setDato('tipo', $usuario->getTipo());
            $this->getModel()->setDato('verificado', $usuario->getVerificado());
            $this->getModel()->setDato('fechaalta', $usuario->getFechaalta());
            
        }else{
            $this->index();
        }
    }
    
    function editUsuarioFromAdmin(){
        if($this->isAdministrator()){
            $id = Request::read('id');
            $admClave = Request::read('admClave');
            $reClave = Request::read('reClave');
            $usuarioDB = $this->getModel()->getFromId($id);
            $usuario = new Usuario();
            $usuario->read();
            
            $usuario->setFechaalta($usuarioDB->getFechaalta());
            
            if($usuario->getTipo() != 'admin'){
                if(Filter::isEmail($usuario->getCorreo()) &&
                Util::verificarClave($admClave, $this->getSesion()->getUser()->getClave()) &&
                $usuario->getNick()!='' &&
                $usuario->getTipo() != ''){
                    
                    //edit sin clave
                    if($usuario->getClave() == ''){
                    $r = $this->getModel()->updateUserNoPass($usuario);
                    } else if($usuario->getClave() == $reClave){
                    //edit con clave
                    $r = $this->getModel()->updateUserPass($usuario);
                    } else{
                        $mensaje = 'Las claves no coinciden. No se ha editado';
                    }
                
                
               } else {
                    $mensaje =  'Pueden pasar las siguiente cosas: <br>
                                1º El correo no es correcto. <br>
                                2º El nick está vacio. <br>
                                3º La clave de administrador no es correcta. <br>
                                4º El tipo de usuario quedo vacio. <br>
                                <br>
                                Por favor verifique todos los campos.';
                }
            } else {
                $mensaje = 'No puede editar un usuario administrador.';
            }
            if($mensaje == null){
                if($r == 1){
                    $mensaje = 'Editado correctamente';
                } else if($r == 0){
                    $mensaje = 'EL nick o el correo estan ya en uso';
                } else{
                    $mensaje = 'Error en la edición';
                }
            }
            header('Location:index.php?accion=administrar&resultadoAccion='.urlencode($mensaje));
            exit;
        }else{
            $this->index();
        }
    }
   
    function activar() {
        $id = Request::read('id');
        $data = Request::read('data');
        $r = $this->getModel()->activarUsuario($id, $data);
        if($r == 1){
            $mensaje = 'Usuario activado correctamente. Haga login';
        } else if ($r==0){
            $mensaje = 'El correo ya estaba activado';
        } else{
            $mensaje = 'Error al activar el correo';
        }
        header('Location: index.php?mensaje='. $mensaje);
        exit();
    }
    
    function login() {
        //hacer el trabajo y luego una redirección
        $usuario = new Usuario();
        $usuario->read();
        
        //Como el campo del form se llama correo, si metemos un nick no lo va a guardar en nick del usuario
        //asi que lo que hacemos es meter en el nick el campo correo, independienteme que sea correo o no, no nos importa.
        $usuario->setNick($usuario->getCorreo());
        if($usuario->getClave() !== '') {
            $usuarioDB = $this->getModel()->loguear($usuario);
        }
            if($usuarioDB != null){
                $this->getSesion()->login($usuarioDB);
            }else{
                $this->getSesion()->logout();
                $mensaje = 'El usuario no existe, clave mal introducida o usuario no verificado';
            }
            header('Location: index.php?mensaje='.$mensaje);
            exit;
    }
    
    function cerrarsesion(){
        $this->getSesion()->close();
        header('Location: index.php?op=logout');
        exit();
    }

    function recuperar(){
        $correo = Request::read('correo');
        $usuario = $this->getModel()->getFromCorreo($correo);
        if($usuario != null){
            $r = $this->eCorreoRecuperacion($usuario->getId(), $usuario->getCorreo());
            if($r){
                $mensaje = 'Se le ha enviado un correo para cambiar su contraseña.';
            } else{
                $mensaje = 'Fallo al enviar el correo para recuperacion';
            }
        }else{
            $mensaje = 'No existe usuario con ese correo.';
        }
        header('Location: index.php?mensaje='.urlencode($mensaje));
        exit;
    }
    
    function recuperarPlantilla(){
        $id = Request::read('id');
        $data = Request::read('data');
        $usuario = $this->getModel()->getFromId($id);
        if($usuario != null){
            $dataV = sha1($usuario->getId().$usuario->getCorreo());
            if($data === $dataV){
                $this->getModel()->setDato('archivo', '_recuperar.html');
                $this->getModel()->setDato('id', $usuario->getId());
                $this->getModel()->setDato('data', $data);
            }
        }else{
            $mensaje = 'No existe usuario para verificar.';
            header('Location: index.php?mensaje='.$mensaje);
            exit;
        }
    }
    
    function doRecuperar(){
        if(!$this->isLogged()){
            $id = Request::read('id');
            $data = Request::read('data');
            $clave = Request::read('clave');
            $reClave = Request::read('reClave');
            if($clave === $reClave && $clave != ''){
                $r = $this->getModel()->updatePass($id, $data, $clave);
            if($r == 1){
                $mensaje = 'Clave cambiada con éxito';
            } else{
                $mensaje = 'Error al cambiar la clave';
            }
            } else{
                $mensaje = 'No ha introducido clave, o no coinciden. Vuelva a pedir que se la cambien.';
            }
            
            header('Location: index.php?mensaje='.$mensaje);
        }else{
            $this->index();
        }
    }
    
    function reactivar(){
        $correo = Request::read('correo');
        $usuario = $this->getModel()->getFromCorreo($correo);
        if($usuario != null){
            $r = $this->eCorreoVerificacion($usuario->getId(), $usuario->getCorreo());
            if($r){
                $mensaje = 'Se le ha enviado un correo de verificacion.';
            } else{
                $mensaje = 'Fallo al enviar el correo de verificacion';
            }
        }else{
            $mensaje = 'No existe usuario con ese correo.';
        }
        header('Location: index.php?mensaje='.urlencode($mensaje));
        exit;
    }

    function subirfoto() {
        if($this->isLogged()) {
            //$input, $name = null, $target = '.', $size = 0, 
            //$policy = FileUpload::RENOMBRAR
            $subir = new FileUpload('foto', $this->getUser()->getId(), '../../foto', 2 * 1024 * 1024, FileUpload::SOBREESCRIBIR);
            $r = $subir->upload();
            if($r){
                $mensaje = 'Subida correctamente.';
            } else{
                $mensaje = 'Error en la subida.';
            }
            header('Location: index.php?mensaje='.$mensaje);
        } else {
            $this->index();
        }
    }
    
    function veravatar() {
        if($this->isLogged()) {
            header('Content-type: image/*');
            $archivo = '../../foto/' . $this->getUser()->getId();
            if(!file_exists($archivo)) {
                $archivo = '../../foto/0';
            }
            readfile($archivo);
            exit();
        } else {
            $this->index();
        }
    }
    
    function pagination($rpp, $juego = null){
         $page = Request::read('page');//página actual
        if($page === null) {
            $page = 1;
        }
        
        if($juego){
            $rows = $this->getModel()->countJuego();
            $accion = 'index';
            $pagination = new Pagination($rows, $page, $rpp, $accion);
        } else{
            $rows = $this->getModel()->countUsers();
            $accion = 'administrar';
            $pagination = new Pagination($rows, $page, $rpp, $accion);
        }
        
        $rango = $pagination->getRange(10);
        $paginas = $this->rango($rango, $accion);
        $draw = '<tr id="pagination">
                    <td colspan=5>
                        <a class="class=linkAdminUser" href="?accion='.$accion.'&page=1">&lt;&lt;</a>
                        <a class="class=linkAdminUser" href="?accion='.$accion.'&page='.$pagination->previous().'">&lt;</a>'.
                        $paginas.'
                        <a class="class=linkAdminUser" href="?accion='.$accion.'&page='.$pagination->next().'">&gt;</a>
                        <a class="class=linkAdminUser" href="?accion='.$accion.'&page='.$pagination->last().'">&gt;&gt;</a>
                    </td>
                </tr>';
                
        return $draw;
    }
    
    function rango($rango, $accion){
        $paginas=null;
        foreach($rango as $pagina){
            $paginas.= '<a class="paginacion" href="?accion='.$accion.'&page=' . $pagina . '">' . $pagina . '    </a>';
        }
        return $paginas;
    }
    
    function addJuego(){
	if($this->isLogged()){
	    $juego = new Juego();
	    $juego->read();
	    $juego->setIdusuario($this->getSesion()->getUser()->getId());
	    $juego->setIdcategoria(Request::read('idcategoria'));
	    $r = $this->getModel()->addJuego($juego);
	    if($r==0){
	       $mensaje = 'Ya existia o datos incorrectos.';
	    } else{
	       $mensaje = 'Añadido con éxito';    
	    }
	    
	    header('Location: index.php?accion=plantillaFormJuego&mensaje='.$mensaje);
	    exit;
	}else{
		$this->index();	
		}
    }

    function removeJuego(){
    	if($this->isLogged()){
    	    $id = Request::read('id');
    	    if($id != ''){
    	        $r = $this->getModel()->removeJuego($id);
    	        if($r == 1){
    	            $mensaje = 'Eliminado correctamente.';
    	        } else{
    	            $mensaje = 'No se ha eliminado';
    	        }
    	    }else{
    	        $mensaje = 'Error al leer el id.';
    	    }
    	    
    	    header('Location:?mensaje='.$mensaje);
    	}else{
    		$this->index();	
    		}
    }
    
    function editJuego(){
    	if($this->isLogged()){
    	    $id = Request::read('id');
    	    if($id!=''){
    	        $juego = new Juego();
    	    $juego->read();
    	    $juego->setIdusuario($this->getSesion()->getUser()->getId());
    	    //echo Util::varDump($juego); exit;
    	    $r = $this->getModel()->editJuego($juego);
        	    if($r == 1){
        	        $mensaje = 'Editado correctamente';
        	    }else{
        	        $mensaje = 'Error al editar. El id no existe';
        	    }
    	    }else{
    	        $mensaje = 'Error al leer el id.';
    	    }
    	    header('Location: index.php?mensaje='.$mensaje);
    	    exit;
    	}else{
    		$this->index();	
    		}
    }
    
    function addCategoria(){
    	if($this->isLogged()){
    	    $categoria = new Categoria();
    	    $categoria->read();
    	    $categoria->setIdusuario($this->getSesion()->getUser()->getId());
    	    $r = $this->getModel()->addCategoria($categoria);
    	    if($r === 0){
    	        $mensaje = 'Error. Existente o datos erroneos';
    	    }else{
    	        $mensaje = "Añadida correctamente";
    	    }
    	    header('Location: index.php?accion=plantillaFormCategoria&mensaje='.$mensaje);
    	    exit;
    	}else{
    		$this->index();	
    		}
    }
    
    function removeCategoria(){
    	if($this->isLogged()){
    	    $id = Request::read('id');
    	    if($id != ''){
    	        $r = $this->getModel()->removeCategoria($id);
    	        if($r == 1){
    	            $mensaje = 'Eliminada correctamente.';
    	        } else{
    	            $mensaje = 'No se ha eliminado. Habrá  juegos con esa categoría.';
    	        }
    	    }else{
    	        $mensaje = 'Error al leer el id.';
    	    }
    	    
    	    header('Location:?mensaje='.$mensaje);
    	}else{
    		$this->index();	
    		}
    }
    
    function editCategoria(){
    	if($this->isLogged()){
    	    $id = Request::read('id');
    	    if($id!=''){
    	        $categoria = new Categoria();
    	    $categoria->read();
    	    $categoria->setIdusuario($this->getSesion()->getUser()->getId());
    	    //echo Util::varDump($juego); exit;
    	    $r = $this->getModel()->editCategoria($categoria);
        	    if($r == 1){
        	        $mensaje = 'Editada correctamente';
        	    }else{
        	        $mensaje = 'Error al editar. El id no existe';
        	    }
    	    }else{
    	        $mensaje = 'Error al leer el id.';
    	    }
    	    header('Location: index.php?mensaje='.$mensaje);
    	    exit;
    	}else{
    		$this->index();	
    		}
    }
    
    function plantillaFormJuego(){
    	if($this->isLogged()){
        	$id = Request::read('id');
    	    $mensaje = Request::read('mensaje');
    	    $this->getModel()->setDato('mensaje', $mensaje);
    	    
    	    //RECOGEMOS TODAS LAS CATEGORIAS.
    	    $todo = $this->getAllCategorias();
    	    $this->getModel()->setDato('categorias', $todo);
    	    //PINTA PARA EDITAR
    	    if($id != ''){
    	        $juego = $this->getModel()->getJuego($id);
    	        if($juego != null){
    	            $this->getModel()->setDato('boton', 'Editar');
    	            $this->getModel()->setDato('accion', 'editJuego');
    	            $this->getModel()->setDato('id', $juego->getId());
    	            $this->getModel()->setDato('nombre', $juego->getNombre());
    	            $this->getModel()->setDato('descripcion', $juego->getDescripcion());
    	            $this->getModel()->setDato('archivo', '_añadir_juego.html');
    	        }else{
    	            $mensaje = 'El id del juego no se encuentra.';
    	            header('Location: index.php?mensaje='.$mensaje);
    	        }
    	        
    	        //PINTA PARA AÑADIR
    	    } else{
  //<option value="volvo">Volvo</option>
    	        $this->getModel()->setDato('accion', 'addJuego');
    	        $this->getModel()->setDato('boton', 'Añadir');
    	        $this->getModel()->setDato('archivo', '_añadir_juego.html');
    	        
    	    }
    	}else{
    		$this->index();	
    		}
    }
    
    function plantillaFormCategoria(){
    	if($this->isLogged()){
            $id = Request::read('id');
    	    $mensaje = Request::read('mensaje');
    	    $this->getModel()->setDato('mensaje', $mensaje);
    	    //PINTA PARA EDITAR
    	    if($id != ''){
    	        $categoria = $this->getModel()->getCategoria($id);
    	        if($categoria != null){
    	            $this->getModel()->setDato('boton', 'Editar');
    	            $this->getModel()->setDato('accion', 'editCategoria');
    	            $this->getModel()->setDato('id', $categoria->getId());
    	            $this->getModel()->setDato('categoria', $categoria->getcategoria());
    	            $this->getModel()->setDato('archivo', '_añadir_categoria.html');
    	        }else{
    	            $mensaje = 'El id de la categoria no se encuentra.';
    	            header('Location: index.php?mensaje='.$mensaje);
    	        }
    	        
    	        //PINTA PARA AÑADIR
    	    } else{
    	        $this->getModel()->setDato('accion', 'addCategoria');
    	        $this->getModel()->setDato('boton', 'Añadir');
    	        $this->getModel()->setDato('archivo', '_añadir_categoria.html');
    	        
    	    }
    	}else{
    		$this->index();	
    		}
    }
    
    private function getAllCategorias(){
        $categorias = $this->getModel()->getAllCategoriasFromUser($this->getSesion()->getUser()->getId());
            $todo = '';
            foreach($categorias as $categoria){
                $todo .= '<option value="'.$categoria->getId().'">'.$categoria->getcategoria().'</option>';
            }
            return $todo;
    }
    
}
