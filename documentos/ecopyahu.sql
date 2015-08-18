-- phpMyAdmin SQL Dump
-- version 4.2.12deb2
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 18-08-2015 a las 18:30:29
-- Versión del servidor: 5.5.44-0+deb8u1
-- Versión de PHP: 5.6.9-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `ecopyahu`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acciones`
--

CREATE TABLE IF NOT EXISTS `acciones` (
`accion_id` int(11) NOT NULL,
  `accion_nombre` varchar(45) NOT NULL,
  `accion_descripcion` varchar(45) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='acciones que se pueden hacer sobre los modulos';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE IF NOT EXISTS `categorias` (
`categoria_id` int(11) NOT NULL,
  `categoria_nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ci_sessions`
--

CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `denuncias`
--

CREATE TABLE IF NOT EXISTS `denuncias` (
`denuncia_id` bigint(20) NOT NULL,
  `denuncia_desc` text,
  `denuncia_fecha` datetime DEFAULT NULL,
  `denuncia_lat` float DEFAULT NULL,
  `denuncia_lon` float DEFAULT NULL,
  `denuncia_direccion` text,
  `denuncia_fuente` enum('twitter','facebook','web','movil') DEFAULT NULL,
  `denuncia_ext_id` bigint(20) DEFAULT NULL,
  `denuncia_ext_datos` text,
  `usuario_id` int(11) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `denuncia_estado` enum('activo','bloqueado','borrado') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `excepciones`
--

CREATE TABLE IF NOT EXISTS `excepciones` (
  `usuario_id` int(11) NOT NULL,
  `modulo_id` int(11) NOT NULL,
  `accion_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE IF NOT EXISTS `modulos` (
`modulo_id` int(11) NOT NULL,
  `modulo_descripcion` varchar(45) DEFAULT NULL,
  `modulo_nombre_corto` varchar(50) DEFAULT NULL COMMENT 'nombre que ira en el action de los botones de ser necesario',
  `modulo_controlador` varchar(50) DEFAULT NULL COMMENT 'nombre del controlador que debe invocar',
  `modulo_icono` varchar(50) DEFAULT NULL,
  `modulo_principal` tinyint(4) DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos_acciones`
--

CREATE TABLE IF NOT EXISTS `modulos_acciones` (
  `modulo_id` int(11) NOT NULL,
  `accion_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `multimedias`
--

CREATE TABLE IF NOT EXISTS `multimedias` (
`multimedia_id` bigint(20) NOT NULL,
  `multimedia_desc` text,
  `multimedia_file_name` varchar(250) DEFAULT NULL,
  `multimedia_tipo` enum('img','video') DEFAULT NULL,
  `denuncia_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perfiles`
--

CREATE TABLE IF NOT EXISTS `perfiles` (
`perfil_id` int(11) NOT NULL,
  `perfil_nombre` varchar(45) DEFAULT NULL,
  `perfil_estado` varchar(45) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perfiles_usuarios`
--

CREATE TABLE IF NOT EXISTS `perfiles_usuarios` (
  `perfil_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE IF NOT EXISTS `permisos` (
  `perfil_id` int(11) NOT NULL,
  `modulo_id` int(11) NOT NULL,
  `accion_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesiones`
--

CREATE TABLE IF NOT EXISTS `sesiones` (
  `session_id` varchar(40) NOT NULL,
  `dir_ip` varchar(16) NOT NULL,
  `user_agent` varchar(150) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `estado` varchar(8) NOT NULL DEFAULT 'inactivo',
  `ingreso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE IF NOT EXISTS `usuarios` (
`usuario_id` int(11) NOT NULL,
  `usuario_nombre` varchar(45) DEFAULT NULL,
  `usuario_apellido` varchar(45) DEFAULT NULL,
  `usuario_user` varchar(45) DEFAULT NULL,
  `usuario_pass` varchar(45) DEFAULT NULL,
  `usuario_email` varchar(45) DEFAULT NULL,
  `usuario_estado` varchar(45) DEFAULT NULL,
  `usuario_cod_act` varchar(45) DEFAULT NULL,
  `usuario_fecha_reg` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `acciones`
--
ALTER TABLE `acciones`
 ADD PRIMARY KEY (`accion_id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
 ADD PRIMARY KEY (`categoria_id`);

--
-- Indices de la tabla `ci_sessions`
--
ALTER TABLE `ci_sessions`
 ADD PRIMARY KEY (`session_id`), ADD KEY `last_activity_idx` (`last_activity`);

--
-- Indices de la tabla `denuncias`
--
ALTER TABLE `denuncias`
 ADD PRIMARY KEY (`denuncia_id`), ADD KEY `fk_denuncias_usuarios1_idx` (`usuario_id`), ADD KEY `fk_denuncias_clasificaciones1_idx` (`categoria_id`);

--
-- Indices de la tabla `excepciones`
--
ALTER TABLE `excepciones`
 ADD PRIMARY KEY (`usuario_id`,`modulo_id`,`accion_id`), ADD KEY `fk_excepciones_modulos_acciones1_idx` (`accion_id`,`modulo_id`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
 ADD PRIMARY KEY (`modulo_id`);

--
-- Indices de la tabla `modulos_acciones`
--
ALTER TABLE `modulos_acciones`
 ADD PRIMARY KEY (`modulo_id`,`accion_id`), ADD KEY `fk_modulos_has_acciones_acciones1_idx` (`accion_id`), ADD KEY `fk_modulos_has_acciones_modulos1_idx` (`modulo_id`);

--
-- Indices de la tabla `multimedias`
--
ALTER TABLE `multimedias`
 ADD PRIMARY KEY (`multimedia_id`), ADD KEY `fk_multimedias_denuncias1_idx` (`denuncia_id`);

--
-- Indices de la tabla `perfiles`
--
ALTER TABLE `perfiles`
 ADD PRIMARY KEY (`perfil_id`);

--
-- Indices de la tabla `perfiles_usuarios`
--
ALTER TABLE `perfiles_usuarios`
 ADD PRIMARY KEY (`perfil_id`,`usuario_id`), ADD KEY `fk_perfiles_has_usuarios_usuarios1` (`usuario_id`), ADD KEY `fk_perfiles_has_usuarios_perfiles1` (`perfil_id`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
 ADD PRIMARY KEY (`perfil_id`,`modulo_id`,`accion_id`), ADD KEY `fk_perfiles_has_modulos_acciones_modulos_acciones1_idx` (`modulo_id`,`accion_id`), ADD KEY `fk_perfiles_has_modulos_acciones_perfiles1_idx` (`perfil_id`);

--
-- Indices de la tabla `sesiones`
--
ALTER TABLE `sesiones`
 ADD PRIMARY KEY (`session_id`), ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
 ADD PRIMARY KEY (`usuario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `acciones`
--
ALTER TABLE `acciones`
MODIFY `accion_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
MODIFY `categoria_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `denuncias`
--
ALTER TABLE `denuncias`
MODIFY `denuncia_id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
MODIFY `modulo_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT de la tabla `multimedias`
--
ALTER TABLE `multimedias`
MODIFY `multimedia_id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `perfiles`
--
ALTER TABLE `perfiles`
MODIFY `perfil_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
MODIFY `usuario_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `denuncias`
--
ALTER TABLE `denuncias`
ADD CONSTRAINT `fk_denuncias_usuarios1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_denuncias_clasificaciones1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`categoria_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `excepciones`
--
ALTER TABLE `excepciones`
ADD CONSTRAINT `fk_excepciones_modulos_acciones1` FOREIGN KEY (`accion_id`, `modulo_id`) REFERENCES `modulos_acciones` (`accion_id`, `modulo_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_excepciones_usuarios1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `modulos_acciones`
--
ALTER TABLE `modulos_acciones`
ADD CONSTRAINT `fk_modulos_has_acciones_acciones1` FOREIGN KEY (`accion_id`) REFERENCES `acciones` (`accion_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_modulos_has_acciones_modulos1` FOREIGN KEY (`modulo_id`) REFERENCES `modulos` (`modulo_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `multimedias`
--
ALTER TABLE `multimedias`
ADD CONSTRAINT `fk_multimedias_denuncias1` FOREIGN KEY (`denuncia_id`) REFERENCES `denuncias` (`denuncia_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `perfiles_usuarios`
--
ALTER TABLE `perfiles_usuarios`
ADD CONSTRAINT `fk_perfiles_has_usuarios_perfiles1` FOREIGN KEY (`perfil_id`) REFERENCES `perfiles` (`perfil_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_perfiles_has_usuarios_usuarios1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `permisos`
--
ALTER TABLE `permisos`
ADD CONSTRAINT `fk_perfiles_has_modulos_acciones_modulos_acciones1` FOREIGN KEY (`modulo_id`, `accion_id`) REFERENCES `modulos_acciones` (`modulo_id`, `accion_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `permisos_ibfk_1` FOREIGN KEY (`perfil_id`) REFERENCES `perfiles` (`perfil_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `sesiones`
--
ALTER TABLE `sesiones`
ADD CONSTRAINT `sesiones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
