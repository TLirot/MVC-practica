/*+++++++++++++++CREAR LAS BASES DE DATOS++++++++++++++++++++++++++++*/
create database gestion
default character set utf8
collate utf8_unicode_ci;

/*++++++++++++++++CREAR AL USUARIO ADMINISTRADOR DE LA BD++++++++++++++++++++++++*/
create user ugestion@'localhost'
identified by 'cgestion';

grant all           --Se le da acceso
on gestion.* to        --A esta base de datos
ugestion@localhost;    --A este usuaio

flush privileges;

use gestion;

/*++++++++++++++++CREAR TABLAS++++++++++++++++++++++++*/

create table if not exists usuario(
    id bigint not null auto_increment primary key,
    nombre varchar (30) not null,
    apellidos varchar (30) not null,
    nick varchar (30) not null unique,
    correo varchar (80) not null unique,
    clave varchar (250) not null,
    tipo ENUM('admin', 'advanced', 'normal'),
    fechaalta datetime not null,
    verificado tinyint (1) not null default 0
)engine=innodb default character set =utf8 collate utf8_unicode_ci;

create table if not exists categoria(
id bigint auto_increment primary key,
categoria varchar (40) not null unique
)engine=innodb default character set =utf8 collate utf8_unicode_ci;


ALTER TABLE categoria
add idusuario bigint (20) not null,
ADD FOREIGN KEY (idusuario) REFERENCES usuario(id) on delete restrict,
add unique (categoria, idusuario);


create table if not exists juego(
id bigint auto_increment primary key,
idusuario bigint (20) not null,
idcategoria bigint (20) not null,
nombre varchar(100) not null,
descripcion text,
unique (nombre, idusuario),
foreign key (idusuario) references usuario (id) on delete restrict,
foreign key (idcateroria) references categoria (id) on delete restrict
)engine=innodb default character set =utf8 collate utf8_unicode_ci;

ALTER TABLE juego
ADD unique (nombre, idusuario);