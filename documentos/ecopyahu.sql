-- phpMyAdmin SQL Dump
-- version 3.5.8.1deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 05-10-2013 a las 22:47:48
-- Versión del servidor: 5.5.32-0ubuntu0.13.04.1
-- Versión de PHP: 5.4.9-4ubuntu2.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
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
  `accion_id` int(11) NOT NULL AUTO_INCREMENT,
  `accion_nombre` varchar(45) NOT NULL,
  `accion_descripcion` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`accion_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='acciones que se pueden hacer sobre los modulos' AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ci_sessions`
--

CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `excepciones`
--

CREATE TABLE IF NOT EXISTS `excepciones` (
  `usuario_id` int(11) NOT NULL,
  `modulo_id` int(11) NOT NULL,
  `accion_id` int(11) NOT NULL,
  PRIMARY KEY (`usuario_id`,`modulo_id`,`accion_id`),
  KEY `fk_excepciones_modulos_acciones1_idx` (`accion_id`,`modulo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE IF NOT EXISTS `modulos` (
  `modulo_id` int(11) NOT NULL AUTO_INCREMENT,
  `modulo_descripcion` varchar(45) DEFAULT NULL,
  `modulo_nombre_corto` varchar(50) DEFAULT NULL COMMENT 'nombre que ira en el action de los botones de ser necesario',
  `modulo_controlador` varchar(50) DEFAULT NULL COMMENT 'nombre del controlador que debe invocar',
  `modulo_icono` varchar(50) DEFAULT NULL,
  `modulo_principal` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`modulo_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos_acciones`
--

CREATE TABLE IF NOT EXISTS `modulos_acciones` (
  `modulo_id` int(11) NOT NULL,
  `accion_id` int(11) NOT NULL,
  PRIMARY KEY (`modulo_id`,`accion_id`),
  KEY `fk_modulos_has_acciones_acciones1_idx` (`accion_id`),
  KEY `fk_modulos_has_acciones_modulos1_idx` (`modulo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perfiles`
--

CREATE TABLE IF NOT EXISTS `perfiles` (
  `perfil_id` int(11) NOT NULL AUTO_INCREMENT,
  `perfil_nombre` varchar(45) DEFAULT NULL,
  `perfil_estado` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`perfil_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perfiles_usuarios`
--

CREATE TABLE IF NOT EXISTS `perfiles_usuarios` (
  `perfil_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  PRIMARY KEY (`perfil_id`,`usuario_id`),
  KEY `fk_perfiles_has_usuarios_usuarios1` (`usuario_id`),
  KEY `fk_perfiles_has_usuarios_perfiles1` (`perfil_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE IF NOT EXISTS `permisos` (
  `perfil_id` int(11) NOT NULL,
  `modulo_id` int(11) NOT NULL,
  `accion_id` int(11) NOT NULL,
  PRIMARY KEY (`perfil_id`,`modulo_id`,`accion_id`),
  KEY `fk_perfiles_has_modulos_acciones_modulos_acciones1_idx` (`modulo_id`,`accion_id`),
  KEY `fk_perfiles_has_modulos_acciones_perfiles1_idx` (`perfil_id`)
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
  `ingreso` int(11) NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE IF NOT EXISTS `usuarios` (
  `usuario_id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_nombre` varchar(45) DEFAULT NULL,
  `usuario_apellido` varchar(45) DEFAULT NULL,
  `usuario_user` varchar(45) DEFAULT NULL,
  `usuario_pass` varchar(45) DEFAULT NULL,
  `usuario_email` varchar(45) DEFAULT NULL,
  `usuario_estado` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Restricciones para tablas volcadas
--

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
