-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-08-2024 a las 16:31:44
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acudientes`
--

CREATE TABLE `acudientes` (
  `ID_acudiente` int(11) NOT NULL,
  `ID_usuario_fk` int(11) DEFAULT NULL,
  `celular` varchar(200) DEFAULT NULL,
  `direccion` varchar(250) DEFAULT NULL,
  `emergencia_cel` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `acudientes`
--

INSERT INTO `acudientes` (`ID_acudiente`, `ID_usuario_fk`, `celular`, `direccion`, `emergencia_cel`) VALUES
(3, 27, '3103303030', '1324 casa', '4104303070'),
(4, 22, '1111111111', 'casa 3 avenia sur', '2222222222');

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
  `info_eps` varchar(350) DEFAULT NULL,
  `foto_alumno` varchar(350) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumno`
--

INSERT INTO `alumno` (`ID_alumno`, `ID_tutor`, `ID_grupo_fk`, `nombre_a`, `apellido_a`, `doc_identidad`, `fecha_nacimiento`, `edad`, `info_eps`, `foto_alumno`) VALUES
(6, 22, 4, 'Pancho', 'Saavedra', '1120123', '2023-01-10', NULL, 'public/pdf/668af092dd981_Congirar datos git .pdf', 'public/img/669310886738e_foto.jpg'),
(7, 22, 4, 'no', 'se', '12345', '2023-06-04', 1, 'public/pdf/668af092dd981_Congirar datos git .pdf', 'public/img/66a2aa1a24ea4_perro_traje.jpeg'),
(8, 22, NULL, 'edwin david', 'Reyes varon', '128899', '2023-06-13', 1, 'public/pdf/668af092dd981_Congirar datos git .pdf', 'public/img/669310886738e_foto.jpg'),
(9, 22, NULL, 'hoho', 'zz', '123', '2023-02-08', 1, 'public/pdf/66860903aa257_reporte.pdf', 'public/img/669310886738e_foto.jpg'),
(10, 22, 4, 'jj', 'rene', '2345678', '2023-02-25', 1, 'public/pdf/66a2aa1a24ea1_Congirar datos git .pdf', 'public/img/66a2aa1a24ea4_perro_traje.jpeg'),
(12, 22, NULL, 'pruba de combinacion ', 'de la matricula', '999', '2023-06-25', 1, 'public/pdf/66a2f9f34a00a_reporte.pdf', 'public/img/669310886738e_foto.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `atencion_cliente`
--

CREATE TABLE `atencion_cliente` (
  `ID_cunsulta` int(11) NOT NULL,
  `nombre_a_cl` varchar(250) DEFAULT NULL,
  `apellido_a_cl` varchar(250) DEFAULT NULL,
  `correo_a_cl` varchar(250) DEFAULT NULL,
  `consuta_a_cl` varchar(350) DEFAULT NULL,
  `date_consulta_a_cl` datetime DEFAULT NULL,
  `respuesta_a_cl` varchar(350) DEFAULT NULL,
  `ID_receptor` int(11) DEFAULT NULL,
  `date_respuesta_a_cl` varchar(350) DEFAULT NULL,
  `estado_consulta` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos_clases`
--

CREATE TABLE `grupos_clases` (
  `ID_g_c` int(11) NOT NULL,
  `ficha` varchar(100) DEFAULT NULL,
  `num_aula` int(11) DEFAULT NULL,
  `id_profesor_fk` int(11) DEFAULT NULL,
  `nivel` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grupos_clases`
--

INSERT INTO `grupos_clases` (`ID_g_c`, `ficha`, `num_aula`, `id_profesor_fk`, `nivel`) VALUES
(4, '1A', 101, 3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nivel_educ`
--

CREATE TABLE `nivel_educ` (
  `ID_nivel` int(11) NOT NULL,
  `nombre_nivel` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `nivel_educ`
--

INSERT INTO `nivel_educ` (`ID_nivel`, `nombre_nivel`) VALUES
(1, 'sala cuna'),
(2, 'parvulos'),
(3, 'prejardin'),
(4, 'jardin'),
(5, 'transicion');

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
(9, 'nueve pueba ', '2024-06-24 16:59:26', 6),
(11, 'ocho ', '2024-06-27 01:04:56', 6),
(12, 'ajax nuevo two alert', '2024-06-27 01:06:28', 7);

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
(13, 14, 's', '3333333339', 10);

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
(13, 'Karen', 'carolg', 'dayanna@gmail.com', '$2y$10$bmE1a4QIPEHYCOsAesRyfOa9sIJxZzAlXuCwsE1FD6CNia9TN.v2q', 1, 1, '2005-02-05'),
(14, 'Dayannaprueba', 'Saavedra-p2', 'saavedr@gmail.com', '$2y$10$77MojASbju6UtKA2MH7q3.kb7aW4vZPVrChPZ80JyTkzjbMQTLhK.', 2, 1, '2004-01-01'),
(15, 'Karen', 'Saavedra', 'carok20@gmail.com', '$2y$10$XvVyowj/l7CucToDkhB3D.XM1BLkerfGI5zYwDcQB56JlaiDHlmzC', 2, 1, '2004-01-01'),
(16, 'ed', 're', 'ed@gmail.com', '$2y$10$ADTMZKZhMZp5vpc8dpiJb.KsM5GKVYpg/Vo0vsCRps4E4lFTeV2i.', 6, 1, '2004-01-01'),
(17, 'Karen', 'Saavedra Caro', 'saavedrcarok20@gmail.com', '$2y$10$gLi/FFfn/z2VHk0lLL/aQOIGQg7RtSHTNw4.ZTP9E63YViAyOz0tG', 4, 1, '0000-00-00'),
(18, 'Jhon', 'Vaquiro', 'jevl@sena.edu.co', '$2y$10$vtW1yvOZavmT3v15ZAD7Me3YAXaqP3iSwFcmRcmeM5ZCmi8ZUaqbC', 6, 0, '0000-00-00'),
(19, 'Pedro two', 'Saavedra', 'fs025@gmail.com', '$2y$10$x4cbVxodH2/.4nqPQ/08j.K0p1yvslhx96A0yvHPnSJAbd05BDY2i', 5, 1, '1974-09-25'),
(22, 'darley', 'caro', 'darley@gmail.com', '$2y$10$c0alU.pS5Mue.Yat/p05QOgBRDc0sVS6sfVMf9p.ojcukhEXoqL7O', 3, 1, '1985-05-08'),
(23, 'prueba two', 'edwin', 'gg@gm.com', '$2y$10$VlN2vGYUrb1kLkA/CcE7vORubHnwH1yfDRvZCSbaI7WVCTkPrgnI6', 2, 1, '2004-02-29'),
(24, 'edwin david', 'Reyes varon', 'eddareva@gmail.com', '$2y$10$pSeMCbUjfZSSLDIrlar1.O7mJPhqJOrAEFeBqlQ64bldQBSB38mi.', NULL, 0, '2000-10-30'),
(25, 'edwin david', 'Reyes varon', 'd@g.com', '$2y$10$DT4WmBaO2j4st1IPiVhSpO5Er70Li6/5K80H/hvSNg/7lpE4am02u', 4, 1, '2000-01-05'),
(26, 'edwin david', 'Reyes varon', 'scd@fsd.com', '$2y$10$PspcCCcWoM6SJoYhLZ.b2O95g6gukiUG7xLBYcHiAzgHCwnYONPHe', NULL, 0, '2000-01-01'),
(27, 'Karomi', 'gordo', 'e@g.com', '$2y$10$EG5hYNg2RL/zgxwLYmPnl.HWDoqhnxF3UuvgB6qbRKSgEcfmVWZ/y', 3, 1, '2000-01-01'),
(28, 'shinsi', 'res', 'f@g.com', '$2y$10$tDF3AeylAobpxgjqIvSof.YDe9Fm.q9M7I4Ik.U7jkhBCVgQJ/JaK', NULL, 0, '2000-01-01'),
(29, 'kaklan', 'kj', 'k8@g.com', '$2y$10$wVWBk/c.H2EMrvYc3T1y3uyv1fE0lF8xAwBrUzX5mtbLX6AlfkdCu', NULL, 0, '2000-01-01'),
(30, 'overflow', 'overlord', 'h@g.com', '$2y$10$t2acB5hpl0a6Fk3Qa21uEu2/agwDIC4Al2BfyrlQsSyH9IKEpeHTS', NULL, 0, '2000-01-01');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `acudientes`
--
ALTER TABLE `acudientes`
  ADD PRIMARY KEY (`ID_acudiente`),
  ADD KEY `ID_usuario_fk` (`ID_usuario_fk`);

--
-- Indices de la tabla `alumno`
--
ALTER TABLE `alumno`
  ADD PRIMARY KEY (`ID_alumno`),
  ADD UNIQUE KEY `doc_identidad` (`doc_identidad`),
  ADD KEY `ID_tutor` (`ID_tutor`),
  ADD KEY `ID_grupo_fk` (`ID_grupo_fk`);

--
-- Indices de la tabla `atencion_cliente`
--
ALTER TABLE `atencion_cliente`
  ADD PRIMARY KEY (`ID_cunsulta`),
  ADD KEY `ID_receptor` (`ID_receptor`);

--
-- Indices de la tabla `grupos_clases`
--
ALTER TABLE `grupos_clases`
  ADD PRIMARY KEY (`ID_g_c`),
  ADD KEY `id_profesor_fk` (`id_profesor_fk`),
  ADD KEY `nivel` (`nivel`);

--
-- Indices de la tabla `nivel_educ`
--
ALTER TABLE `nivel_educ`
  ADD PRIMARY KEY (`ID_nivel`);

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
-- AUTO_INCREMENT de la tabla `acudientes`
--
ALTER TABLE `acudientes`
  MODIFY `ID_acudiente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `alumno`
--
ALTER TABLE `alumno`
  MODIFY `ID_alumno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `atencion_cliente`
--
ALTER TABLE `atencion_cliente`
  MODIFY `ID_cunsulta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `grupos_clases`
--
ALTER TABLE `grupos_clases`
  MODIFY `ID_g_c` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `nivel_educ`
--
ALTER TABLE `nivel_educ`
  MODIFY `ID_nivel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `observaciones`
--
ALTER TABLE `observaciones`
  MODIFY `id_observacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
  MODIFY `ID_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `acudientes`
--
ALTER TABLE `acudientes`
  ADD CONSTRAINT `acudientes_ibfk_1` FOREIGN KEY (`ID_usuario_fk`) REFERENCES `usuarios` (`ID_usuario`);

--
-- Filtros para la tabla `alumno`
--
ALTER TABLE `alumno`
  ADD CONSTRAINT `alumno_ibfk_1` FOREIGN KEY (`ID_tutor`) REFERENCES `usuarios` (`ID_usuario`),
  ADD CONSTRAINT `alumno_ibfk_2` FOREIGN KEY (`ID_grupo_fk`) REFERENCES `grupos_clases` (`ID_g_c`);

--
-- Filtros para la tabla `atencion_cliente`
--
ALTER TABLE `atencion_cliente`
  ADD CONSTRAINT `atencion_cliente_ibfk_1` FOREIGN KEY (`ID_receptor`) REFERENCES `usuarios` (`ID_usuario`);

--
-- Filtros para la tabla `grupos_clases`
--
ALTER TABLE `grupos_clases`
  ADD CONSTRAINT `grupos_clases_ibfk_1` FOREIGN KEY (`id_profesor_fk`) REFERENCES `profesor` (`ID_tabla_p`),
  ADD CONSTRAINT `grupos_clases_ibfk_2` FOREIGN KEY (`nivel`) REFERENCES `nivel_educ` (`ID_nivel`);

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
