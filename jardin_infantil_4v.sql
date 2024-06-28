-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-06-2024 a las 05:28:38
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `jardin_infantil`
--
CREATE DATABASE IF NOT EXISTS `jardin_infantil` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `jardin_infantil`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumno`
--

CREATE TABLE `alumno` (
  `ID_alumno` int(11) NOT NULL,
  `ID_tutor` int(11) DEFAULT NULL,
  `ID_grupo_fk` int(11) DEFAULT NULL,
  `nombre_a` varchar(200) DEFAULT NULL,
  `apellido_a` varchar(255) DEFAULT NULL,
  `doc_identidad` varchar(100) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `info_eps` varbinary(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumno`
--

INSERT INTO `alumno` (`ID_alumno`, `ID_tutor`, `ID_grupo_fk`, `nombre_a`, `apellido_a`, `doc_identidad`, `fecha_nacimiento`, `edad`, `info_eps`) VALUES
(6, 22, 4, 'Pancho', 'Saavedra', '1120123', '2023-01-10', NULL, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos_clases`
--

CREATE TABLE `grupos_clases` (
  `ID_g_c` int(11) NOT NULL,
  `ficha` varchar(100) DEFAULT NULL,
  `num_aula` int(11) DEFAULT NULL,
  `id_profesor_fk` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grupos_clases`
--

INSERT INTO `grupos_clases` (`ID_g_c`, `ficha`, `num_aula`, `id_profesor_fk`) VALUES
(4, '1A', 101, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `observaciones`
--

CREATE TABLE `observaciones` (
  `id_observacion` int(11) NOT NULL,
  `descripcion` mediumtext DEFAULT NULL,
  `fecha_hora_creacion` datetime DEFAULT NULL,
  `id_nino_fk` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `observaciones`
--

INSERT INTO `observaciones` (`id_observacion`, `descripcion`, `fecha_hora_creacion`, `id_nino_fk`) VALUES
(1, 'ola pruebA  update', '2024-06-24 12:23:02', 6),
(3, 'prueba tres', '2024-06-24 16:58:02', 6),
(4, 'prueba cuatro ', '2024-06-24 16:58:16', 6),
(5, 'prueba cinco', '2024-06-24 16:58:35', 6),
(6, 'sis ', '2024-06-24 16:58:53', 6),
(7, 'siete prueba ', '2024-06-24 16:59:05', 6),
(8, 'ocho prueba', '2024-06-24 16:59:15', 6),
(9, 'nueve pueba ', '2024-06-24 16:59:26', 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesor`
--

CREATE TABLE `profesor` (
  `ID_tabla_p` int(11) NOT NULL,
  `ID_profesor` int(11) DEFAULT NULL,
  `materia` varchar(200) DEFAULT NULL,
  `celular` varchar(100) DEFAULT NULL,
  `years_experiencia` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `profesor`
--

INSERT INTO `profesor` (`ID_tabla_p`, `ID_profesor`, `materia`, `celular`, `years_experiencia`) VALUES
(3, 15, 'Psicologia', '321260', 19),
(13, 14, 's', '3333333339', 10),
(14, 23, 'verdugo', '3333333333', 100);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_usuario`
--

CREATE TABLE `rol_usuario` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol_usuario`
--

INSERT INTO `rol_usuario` (`id_rol`, `nombre_rol`) VALUES
(1, 'Administrador'),
(2, 'Profesor'),
(3, 'Acudiente'),
(4, 'Director'),
(5, 'Subdirector'),
(6, 'Rector');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `ID_usuario` int(11) NOT NULL,
  `nombre_u` varchar(200) DEFAULT NULL,
  `apellido_u` varchar(200) DEFAULT NULL,
  `correo_u` varchar(300) DEFAULT NULL,
  `Contrasena_u` varchar(300) DEFAULT NULL,
  `rol_u` int(11) DEFAULT NULL,
  `activo` int(11) NOT NULL DEFAULT 0,
  `fechanacimiento` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`ID_usuario`, `nombre_u`, `apellido_u`, `correo_u`, `Contrasena_u`, `rol_u`, `activo`, `fechanacimiento`) VALUES
(13, 'Karen', 'caro', 'dayanna@gmail.com', '$2y$10$vEgo.tPtfYWwNmZEzxWvyurpcsPqBp/pbjuRf8HiP.urr6YkJsBnC', 1, 1, '2005-02-05'),
(14, 'Dayanna', 'Saavedra', 'saavedr@gmail.com', '$2y$10$77MojASbju6UtKA2MH7q3.kb7aW4vZPVrChPZ80JyTkzjbMQTLhK.', 2, 1, '2004-01-01'),
(15, 'Karen', 'Saavedra', 'carok20@gmail.com', '$2y$10$XvVyowj/l7CucToDkhB3D.XM1BLkerfGI5zYwDcQB56JlaiDHlmzC', 2, 1, '2004-01-01'),
(16, 'ed', 're', 'ed@gmail.com', '$2y$10$eM0o5f2uo3TbMRZmH8V0v.uX/LOPvqMwdMqQmIamoU6cf/vzgv/VC', 6, 1, '0000-00-00'),
(17, 'Karen', 'Saavedra Caro', 'saavedrcarok20@gmail.com', '$2y$10$gLi/FFfn/z2VHk0lLL/aQOIGQg7RtSHTNw4.ZTP9E63YViAyOz0tG', 4, 1, '0000-00-00'),
(18, 'Jhon', 'Vaquiro', 'jevl@sena.edu.co', '$2y$10$vtW1yvOZavmT3v15ZAD7Me3YAXaqP3iSwFcmRcmeM5ZCmi8ZUaqbC', 6, 1, '0000-00-00'),
(19, 'Pedro', 'Saavedra', 'fs025@gmail.com', '$2y$10$JILaIot1uMx779RiV23wde.Hr1m7hqui6yvvQvXq9X/YfPmHBBzXq', 5, 1, '1974-09-25'),
(22, 'darley', 'caro', 'darley@gmail.com', '$2y$10$7JVd.H08EbEH1DyWZPYK1uCiDbm7d7XzZ0lxbtcUPIrQp0TVAGhTG', 3, 1, '1985-05-08'),
(23, 'prueba', 'edwin', 'gg@gm.com', '$2y$10$VlN2vGYUrb1kLkA/CcE7vORubHnwH1yfDRvZCSbaI7WVCTkPrgnI6', 2, 1, '2004-02-29'),
(24, 'edwin david', 'Reyes varon', 'eddareva@gmail.com', '$2y$10$pSeMCbUjfZSSLDIrlar1.O7mJPhqJOrAEFeBqlQ64bldQBSB38mi.', NULL, 0, '2000-10-30');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumno`
--
ALTER TABLE `alumno`
  ADD PRIMARY KEY (`ID_alumno`),
  ADD UNIQUE KEY `doc_identidad` (`doc_identidad`),
  ADD KEY `ID_tutor` (`ID_tutor`),
  ADD KEY `ID_grupo_fk` (`ID_grupo_fk`);

--
-- Indices de la tabla `grupos_clases`
--
ALTER TABLE `grupos_clases`
  ADD PRIMARY KEY (`ID_g_c`),
  ADD KEY `id_profesor_fk` (`id_profesor_fk`);

--
-- Indices de la tabla `observaciones`
--
ALTER TABLE `observaciones`
  ADD PRIMARY KEY (`id_observacion`),
  ADD KEY `id_nino_fk` (`id_nino_fk`);

--
-- Indices de la tabla `profesor`
--
ALTER TABLE `profesor`
  ADD PRIMARY KEY (`ID_tabla_p`),
  ADD KEY `ID_profesor` (`ID_profesor`);

--
-- Indices de la tabla `rol_usuario`
--
ALTER TABLE `rol_usuario`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`ID_usuario`),
  ADD UNIQUE KEY `correo_u` (`correo_u`),
  ADD KEY `rol_u` (`rol_u`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alumno`
--
ALTER TABLE `alumno`
  MODIFY `ID_alumno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `grupos_clases`
--
ALTER TABLE `grupos_clases`
  MODIFY `ID_g_c` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `observaciones`
--
ALTER TABLE `observaciones`
  MODIFY `id_observacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `profesor`
--
ALTER TABLE `profesor`
  MODIFY `ID_tabla_p` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `rol_usuario`
--
ALTER TABLE `rol_usuario`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `ID_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alumno`
--
ALTER TABLE `alumno`
  ADD CONSTRAINT `alumno_ibfk_1` FOREIGN KEY (`ID_tutor`) REFERENCES `usuarios` (`ID_usuario`),
  ADD CONSTRAINT `alumno_ibfk_2` FOREIGN KEY (`ID_grupo_fk`) REFERENCES `grupos_clases` (`ID_g_c`);

--
-- Filtros para la tabla `grupos_clases`
--
ALTER TABLE `grupos_clases`
  ADD CONSTRAINT `grupos_clases_ibfk_1` FOREIGN KEY (`id_profesor_fk`) REFERENCES `profesor` (`ID_tabla_p`);

--
-- Filtros para la tabla `observaciones`
--
ALTER TABLE `observaciones`
  ADD CONSTRAINT `observaciones_ibfk_1` FOREIGN KEY (`id_nino_fk`) REFERENCES `alumno` (`ID_alumno`) ON DELETE CASCADE;

--
-- Filtros para la tabla `profesor`
--
ALTER TABLE `profesor`
  ADD CONSTRAINT `profesor_ibfk_1` FOREIGN KEY (`ID_profesor`) REFERENCES `usuarios` (`ID_usuario`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_u`) REFERENCES `rol_usuario` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
