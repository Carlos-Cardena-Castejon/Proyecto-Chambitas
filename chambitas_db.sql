-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-04-2026 a las 00:56:15
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `chambitas_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones`
--

CREATE TABLE `calificaciones` (
  `id_calificacion` int(11) NOT NULL,
  `id_contratacion` int(11) DEFAULT NULL,
  `id_calificador` int(11) NOT NULL,
  `id_calificado` int(11) NOT NULL,
  `puntuacion` int(11) DEFAULT NULL CHECK (`puntuacion` between 1 and 5),
  `comentario` varchar(500) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `calificaciones`
--

INSERT INTO `calificaciones` (`id_calificacion`, `id_contratacion`, `id_calificador`, `id_calificado`, `puntuacion`, `comentario`, `fecha`) VALUES
(1, 1, 22, 21, 5, 'Excelente trabajador ', '2026-04-07 21:50:23'),
(2, 2, 21, 22, 4, '', '2026-04-08 00:02:05'),
(3, 3, 22, 59, 5, 'Excelente', '2026-04-08 14:08:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`, `descripcion`, `estado`) VALUES
(1, 'Hogar', NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contrataciones`
--

CREATE TABLE `contrataciones` (
  `id_contratacion` int(11) NOT NULL,
  `id_publicacion` int(11) NOT NULL,
  `id_propuesta` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_contratacion` datetime DEFAULT current_timestamp(),
  `estado` varchar(50) DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `contrataciones`
--

INSERT INTO `contrataciones` (`id_contratacion`, `id_publicacion`, `id_propuesta`, `id_usuario`, `fecha_contratacion`, `estado`) VALUES
(1, 2, 2, 21, '2026-04-07 21:50:04', 'Finalizado'),
(2, 4, 6, 22, '2026-04-08 00:01:58', 'Finalizado'),
(3, 8, 9, 59, '2026-04-08 14:07:39', 'Finalizado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagenestrabajo`
--

CREATE TABLE `imagenestrabajo` (
  `id_imagen` int(11) NOT NULL,
  `id_publicacion` int(11) NOT NULL,
  `ruta_imagen` varchar(255) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `fecha_subida` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `imagenestrabajo`
--

INSERT INTO `imagenestrabajo` (`id_imagen`, `id_publicacion`, `ruta_imagen`, `descripcion`, `fecha_subida`) VALUES
(2, 2, 'uploads/imagenes/1775587210_Escalera.jfif', NULL, '2026-04-07 12:40:10'),
(4, 4, 'uploads/imagenes/1775623737_IMG_20260404_195112.jpg', NULL, '2026-04-07 22:48:57'),
(8, 8, 'uploads/imagenes/1775678730_Gemini_Generated_Image_bwv9s3bwv9s3bwv9.png', NULL, '2026-04-08 14:05:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id_mensaje` int(11) NOT NULL,
  `id_usuario_envia` int(11) NOT NULL,
  `id_usuario_recibe` int(11) NOT NULL,
  `id_publicacion` int(11) DEFAULT NULL,
  `id_contratacion` int(11) DEFAULT NULL,
  `mensaje` varchar(500) NOT NULL,
  `fecha_envio` datetime DEFAULT current_timestamp(),
  `leido` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`id_mensaje`, `id_usuario_envia`, `id_usuario_recibe`, `id_publicacion`, `id_contratacion`, `mensaje`, `fecha_envio`, `leido`) VALUES
(1, 22, 21, 2, NULL, 'hola', '2026-04-07 12:41:03', 1),
(2, 21, 22, 2, NULL, 'Ey', '2026-04-07 12:41:53', 1),
(3, 22, 21, 2, NULL, 'que pedales pa', '2026-04-07 12:42:01', 1),
(4, 22, 21, 2, NULL, 'ey', '2026-04-07 13:12:29', 1),
(5, 21, 22, 2, NULL, 'Buenas ', '2026-04-07 13:13:28', 1),
(6, 21, 22, 2, NULL, 'Ey', '2026-04-07 13:13:36', 1),
(7, 21, 22, 2, NULL, 'Ey', '2026-04-07 13:13:38', 1),
(8, 21, 22, 2, NULL, 'Eyy', '2026-04-07 13:13:42', 1),
(9, 22, 21, 2, NULL, 'Hola', '2026-04-07 21:49:37', 1),
(10, 21, 22, 2, NULL, 'wowowoowow', '2026-04-07 21:49:47', 1),
(11, 21, 22, 2, NULL, 'neta', '2026-04-07 21:49:51', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id_notificacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `tipo` varchar(50) DEFAULT 'info',
  `leida` tinyint(1) DEFAULT 0,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id_notificacion`, `id_usuario`, `mensaje`, `tipo`, `leida`, `fecha_registro`, `link`) VALUES
(1, 22, 'Se actualizó el presupuesto a $1,000.00 en la chamba: Cotización de Escalera de Madera', 'edicion', 1, '2026-04-07 12:37:28', 'trabajo.php?id=1'),
(5, 60, 'Estimado usuario, su publicación fue eliminada por incumplir nuestras normas comunitarias: No hagas estas publicaciones crack.', 'sancion', 1, '2026-04-07 22:50:34', NULL),
(6, 60, 'Estimado usuario, su publicación fue eliminada por incumplir nuestras normas comunitarias: BRO, ya borra tus mamadas.', 'sancion', 0, '2026-04-08 00:03:39', NULL),
(7, 60, 'Estimado usuario, su publicación fue eliminada por incumplir nuestras normas comunitarias: que pedo pa.', 'sancion', 0, '2026-04-08 00:09:05', NULL),
(8, 60, 'Estimado usuario, su publicación fue eliminada por incumplir nuestras normas comunitarias: Ya pa dejate de mamadas.', 'sancion', 0, '2026-04-08 00:15:36', NULL),
(9, 55, 'Estimado usuario, su publicación fue eliminada por incumplir nuestras normas comunitarias: Bro tremendo auto.', 'sancion', 0, '2026-04-08 00:38:54', NULL),
(12, 55, 'Hemos tomado medidas respecto a tu reporte sobre Juanito Perezo. ¡Muchas gracias por ayudar a Chambitas a ser un lugar más seguro!', 'info', 0, '2026-04-08 00:44:42', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `propuestas`
--

CREATE TABLE `propuestas` (
  `id_propuesta` int(11) NOT NULL,
  `id_publicacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `mensaje` varchar(500) DEFAULT NULL,
  `precio_oferta` decimal(10,2) DEFAULT NULL,
  `tiempo_estimado` varchar(50) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `estado` varchar(50) DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `propuestas`
--

INSERT INTO `propuestas` (`id_propuesta`, `id_publicacion`, `id_usuario`, `mensaje`, `precio_oferta`, `tiempo_estimado`, `fecha`, `estado`) VALUES
(2, 2, 21, 'Ssssddsdfffffd', 100.00, '11', '2026-04-07 12:40:48', 'Aceptada'),
(6, 4, 22, 'Hkojsbs', 100.00, '11', '2026-04-08 00:01:48', 'Aceptada'),
(8, 8, 21, '111111', 4567.00, '10', '2026-04-08 14:05:55', 'Rechazada'),
(9, 8, 59, '444444', 5555.00, '99', '2026-04-08 14:07:18', 'Aceptada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicaciones`
--

CREATE TABLE `publicaciones` (
  `id_publicacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_servicio` int(11) DEFAULT NULL,
  `titulo` varchar(150) NOT NULL,
  `descripcion` varchar(500) NOT NULL,
  `presupuesto` decimal(10,2) NOT NULL,
  `fecha_publicacion` datetime DEFAULT current_timestamp(),
  `estado` varchar(50) DEFAULT 'Activa',
  `colonia` varchar(100) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `servicio_personalizado` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `publicaciones`
--

INSERT INTO `publicaciones` (`id_publicacion`, `id_usuario`, `id_servicio`, `titulo`, `descripcion`, `presupuesto`, `fecha_publicacion`, `estado`, `colonia`, `imagen`, `servicio_personalizado`) VALUES
(2, 22, 29, 'Cotización de Escalera de Madera', '1234567', 10000.00, '2026-04-07 12:40:10', 'Finalizado', NULL, NULL, NULL),
(4, 21, NULL, 'Reparación de fuga en tubería de agua.', '0', 1000.00, '2026-04-07 22:48:57', 'Finalizado', NULL, NULL, 'Poliester'),
(8, 22, 27, 'Ejemplo', '1.111111111111111e15', 11111.00, '2026-04-08 14:05:30', 'Finalizado', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportes_publicaciones`
--

CREATE TABLE `reportes_publicaciones` (
  `id_reporte` int(11) NOT NULL,
  `id_publicacion` int(11) NOT NULL,
  `id_usuario_reporta` int(11) NOT NULL,
  `id_usuario_dueno` int(11) NOT NULL,
  `motivo` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `fecha_reporte` datetime DEFAULT current_timestamp(),
  `estado` enum('Pendiente','Revisado','Descartado') DEFAULT 'Pendiente',
  `evidencia_ruta` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reportes_publicaciones`
--

INSERT INTO `reportes_publicaciones` (`id_reporte`, `id_publicacion`, `id_usuario_reporta`, `id_usuario_dueno`, `motivo`, `descripcion`, `fecha_reporte`, `estado`, `evidencia_ruta`) VALUES
(1, 1, 22, 21, 'Venta de Producto', '123456', '2026-04-07 12:36:42', 'Revisado', NULL),
(2, 3, 55, 60, 'Estafa', 'Tremenda publicación engañosa cracks', '2026-04-07 22:37:12', 'Revisado', NULL),
(3, 3, 21, 60, 'Spam', '1111111111', '2026-04-08 00:01:20', 'Revisado', NULL),
(4, 3, 21, 60, 'Venta de Producto', '1111111111111111111111111111111111111111111111', '2026-04-08 00:08:52', 'Revisado', NULL),
(5, 3, 21, 60, 'Spam', '123', '2026-04-08 00:15:23', 'Revisado', NULL),
(6, 6, 21, 55, 'Venta de Producto', 'Ey', '2026-04-08 00:38:36', 'Revisado', NULL),
(7, 7, 22, 21, 'Spam', 'Qjshavuvuvuvu', '2026-04-08 00:43:25', 'Revisado', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportes_usuarios`
--

CREATE TABLE `reportes_usuarios` (
  `id_reporte` int(11) NOT NULL,
  `id_usuario_reporta` int(11) NOT NULL,
  `id_usuario_reportado` int(11) NOT NULL,
  `motivo` varchar(255) NOT NULL,
  `detalles` text DEFAULT NULL,
  `fecha_reporte` datetime DEFAULT current_timestamp(),
  `estado` enum('Pendiente','Revisado','Sancionado') DEFAULT 'Pendiente',
  `evidencia_ruta` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reportes_usuarios`
--

INSERT INTO `reportes_usuarios` (`id_reporte`, `id_usuario_reporta`, `id_usuario_reportado`, `motivo`, `detalles`, `fecha_reporte`, `estado`, `evidencia_ruta`) VALUES
(1, 55, 60, 'Acoso / Mensajes Ofensivos', 'Es tremendo crack', '2026-04-07 22:34:06', '', NULL),
(2, 21, 22, 'Spam / Fraude', '11111111111111111111111', '2026-04-08 00:44:27', '', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id_servicio` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id_servicio`, `id_categoria`, `nombre`, `descripcion`, `estado`) VALUES
(24, 1, 'Plomeria', NULL, 1),
(25, 1, 'Albañileria', NULL, 1),
(26, 1, 'Electricidad', NULL, 1),
(27, 1, 'Pintura', NULL, 1),
(28, 1, 'Jardineria', NULL, 1),
(29, 1, 'Carpinteria', NULL, 1),
(30, 1, 'Limpieza', NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicaciones`
--

CREATE TABLE `ubicaciones` (
  `id_ubicacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_publicacion` int(11) NOT NULL,
  `latitud` decimal(10,8) NOT NULL,
  `longitud` decimal(11,8) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ubicaciones`
--

INSERT INTO `ubicaciones` (`id_ubicacion`, `id_usuario`, `id_publicacion`, `latitud`, `longitud`, `direccion`) VALUES
(2, 22, 2, 25.65452330, -100.21766490, NULL),
(4, 21, 4, 25.97173400, -99.68443200, NULL),
(8, 22, 8, 25.67710270, -100.28218600, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido_paterno` varchar(100) NOT NULL,
  `apellido_materno` varchar(100) NOT NULL,
  `curp` varchar(18) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `estado` tinyint(1) DEFAULT 1,
  `descripcion` text DEFAULT NULL,
  `rol` enum('admin','usuario') DEFAULT 'usuario',
  `foto_perfil` varchar(255) DEFAULT NULL,
  `razon_bloqueo` text DEFAULT NULL,
  `fin_suspension` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido_paterno`, `apellido_materno`, `curp`, `correo`, `telefono`, `contrasena`, `fecha_registro`, `estado`, `descripcion`, `rol`, `foto_perfil`, `razon_bloqueo`, `fin_suspension`) VALUES
(19, 'Admin', 'Sistema', 'Principal', 'ADMS000101HDFXXX01', 'admin@chambitas.com', '0000000000', '$2y$10$/dU00PVmZQ1J4grVbpBCbOWenm1cU8rMTBUBCY7LYAzR.YFB7Zvou', '2026-03-09 11:51:10', 1, NULL, 'admin', NULL, NULL, NULL),
(20, 'Pedro Yahir', 'Castro Rivera', 'Rivera', 'CARP060529HNLSVDA7', 'pedrolml29@gmai.com', '8136353775', '$2y$10$9bWlwCGIHmO.ByGTVVd/RuC9Ha9izEIPlcpNtdq4IQZBxdoSvZdCe', '2026-03-10 22:12:43', 0, NULL, 'usuario', NULL, NULL, NULL),
(21, 'Carlos Daniel ', 'Cardena', 'Castejon', 'CACC060922HNLRSRA5', 'Carlos@Cardena.UT', '8124415599', '$2y$10$RIR3dYZxQ2zw.BYH80o8/eGZbTD4Y9kUYZIBg4P6AKTOVlLB/glTC', '2026-03-15 11:15:16', 0, 'Ksjsjsjjsjsjsjsjkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooo', 'usuario', 'uploads/perfiles/69ba2c67277ee_IMG_20260223_201142_741.webp', 'CABRON CABRON', '2026-04-14 09:31:17'),
(22, 'Bruno', 'Thomas', 'Diaz', 'CACC060922HNLRSRA6', 'BrunoDays@correo.com', '8124419955', '$2y$10$SWiRD5HLdmrwRQcj02/xyezLk/rljrBaDAhOQGBCLV1k6n9Ykws9m', '2026-03-15 14:03:33', 1, 'ppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppooooooooooooooooooooooooooo\r\n\r\n', 'usuario', 'uploads/perfiles/chambitas_69d6b7cbc4c3b.png', NULL, NULL),
(55, 'Ivan', 'Castillo', 'Ortiz', 'CAOC060118HNLSRR89', 'ivanca@gmail.com', '8128164398', '$2y$10$5YgSHMs.swQdzCeDa7qpgOfcwMRmjAgavdbJOvIjGoQ5zvyX6FLu6', '2026-04-07 11:47:14', 1, 'Hola, soy buen trabajador, contratame y tendrás un trabajo de excelente calidad', 'usuario', NULL, NULL, NULL),
(59, 'Ana Jazmin', 'Cardenas', 'Castejon', 'CACC060922HNLRSPA5', 'ana@jaz.com', '8120202070', '$2y$10$cXsw6jtn6lKlftSDjHz3HOYgiFQxp0cv3IMUpkamwulO/.KHQm5YS', '2026-04-07 21:58:59', 1, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'usuario', NULL, NULL, NULL),
(60, 'Juanito', 'Perezo', 'Perezo', 'CAOC060118HNLSRRB6', 'perez@gmail.com', '1943785309', '$2y$10$OVSYl7dfns05mTJDGBNJXOGpY3vdUBpLLZGN/TGZ5.n/7MAA/xyoS', '2026-04-07 22:20:40', 1, 'Hola este soy yo ', 'usuario', NULL, NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD PRIMARY KEY (`id_calificacion`),
  ADD UNIQUE KEY `id_contratacion` (`id_contratacion`),
  ADD KEY `id_calificador` (`id_calificador`),
  ADD KEY `id_calificado` (`id_calificado`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `contrataciones`
--
ALTER TABLE `contrataciones`
  ADD PRIMARY KEY (`id_contratacion`),
  ADD UNIQUE KEY `unica_contratacion_publicacion` (`id_publicacion`),
  ADD KEY `id_publicacion` (`id_publicacion`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `fk_contratacion_propuesta` (`id_propuesta`);

--
-- Indices de la tabla `imagenestrabajo`
--
ALTER TABLE `imagenestrabajo`
  ADD PRIMARY KEY (`id_imagen`),
  ADD KEY `id_publicacion` (`id_publicacion`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id_mensaje`),
  ADD KEY `id_usuario_envia` (`id_usuario_envia`),
  ADD KEY `id_usuario_recibe` (`id_usuario_recibe`),
  ADD KEY `id_publicacion` (`id_publicacion`),
  ADD KEY `fk_mensaje_contratacion` (`id_contratacion`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id_notificacion`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `propuestas`
--
ALTER TABLE `propuestas`
  ADD PRIMARY KEY (`id_propuesta`),
  ADD UNIQUE KEY `unica_propuesta_usuario` (`id_publicacion`,`id_usuario`),
  ADD KEY `id_publicacion` (`id_publicacion`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `publicaciones`
--
ALTER TABLE `publicaciones`
  ADD PRIMARY KEY (`id_publicacion`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_servicio` (`id_servicio`);

--
-- Indices de la tabla `reportes_publicaciones`
--
ALTER TABLE `reportes_publicaciones`
  ADD PRIMARY KEY (`id_reporte`),
  ADD KEY `fk_reporte_pub` (`id_publicacion`),
  ADD KEY `fk_reporte_emisor` (`id_usuario_reporta`),
  ADD KEY `fk_reporte_receptor` (`id_usuario_dueno`);

--
-- Indices de la tabla `reportes_usuarios`
--
ALTER TABLE `reportes_usuarios`
  ADD PRIMARY KEY (`id_reporte`),
  ADD KEY `id_usuario_reporta` (`id_usuario_reporta`),
  ADD KEY `id_usuario_reportado` (`id_usuario_reportado`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id_servicio`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indices de la tabla `ubicaciones`
--
ALTER TABLE `ubicaciones`
  ADD PRIMARY KEY (`id_ubicacion`),
  ADD KEY `fk_publicacion_ubicacion` (`id_publicacion`),
  ADD KEY `fk_usuario_ubicacion` (`id_usuario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `curp` (`curp`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `telefono` (`telefono`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  MODIFY `id_calificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `contrataciones`
--
ALTER TABLE `contrataciones`
  MODIFY `id_contratacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `imagenestrabajo`
--
ALTER TABLE `imagenestrabajo`
  MODIFY `id_imagen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id_mensaje` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `propuestas`
--
ALTER TABLE `propuestas`
  MODIFY `id_propuesta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `publicaciones`
--
ALTER TABLE `publicaciones`
  MODIFY `id_publicacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `reportes_publicaciones`
--
ALTER TABLE `reportes_publicaciones`
  MODIFY `id_reporte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `reportes_usuarios`
--
ALTER TABLE `reportes_usuarios`
  MODIFY `id_reporte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `ubicaciones`
--
ALTER TABLE `ubicaciones`
  MODIFY `id_ubicacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD CONSTRAINT `calificaciones_ibfk_1` FOREIGN KEY (`id_contratacion`) REFERENCES `contrataciones` (`id_contratacion`),
  ADD CONSTRAINT `calificaciones_ibfk_2` FOREIGN KEY (`id_calificador`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `calificaciones_ibfk_3` FOREIGN KEY (`id_calificado`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `contrataciones`
--
ALTER TABLE `contrataciones`
  ADD CONSTRAINT `contrataciones_ibfk_1` FOREIGN KEY (`id_publicacion`) REFERENCES `publicaciones` (`id_publicacion`),
  ADD CONSTRAINT `contrataciones_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `fk_contratacion_propuesta` FOREIGN KEY (`id_propuesta`) REFERENCES `propuestas` (`id_propuesta`);

--
-- Filtros para la tabla `imagenestrabajo`
--
ALTER TABLE `imagenestrabajo`
  ADD CONSTRAINT `imagenestrabajo_ibfk_1` FOREIGN KEY (`id_publicacion`) REFERENCES `publicaciones` (`id_publicacion`);

--
-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `fk_mensaje_contratacion` FOREIGN KEY (`id_contratacion`) REFERENCES `contrataciones` (`id_contratacion`),
  ADD CONSTRAINT `mensajes_ibfk_1` FOREIGN KEY (`id_usuario_envia`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `mensajes_ibfk_2` FOREIGN KEY (`id_usuario_recibe`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `mensajes_ibfk_3` FOREIGN KEY (`id_publicacion`) REFERENCES `publicaciones` (`id_publicacion`);

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `propuestas`
--
ALTER TABLE `propuestas`
  ADD CONSTRAINT `propuestas_ibfk_1` FOREIGN KEY (`id_publicacion`) REFERENCES `publicaciones` (`id_publicacion`),
  ADD CONSTRAINT `propuestas_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `publicaciones`
--
ALTER TABLE `publicaciones`
  ADD CONSTRAINT `fk_publicacion_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `publicaciones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `publicaciones_ibfk_2` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`);

--
-- Filtros para la tabla `reportes_publicaciones`
--
ALTER TABLE `reportes_publicaciones`
  ADD CONSTRAINT `fk_reporte_emisor` FOREIGN KEY (`id_usuario_reporta`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `fk_reporte_receptor` FOREIGN KEY (`id_usuario_dueno`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `reportes_usuarios`
--
ALTER TABLE `reportes_usuarios`
  ADD CONSTRAINT `reportes_usuarios_ibfk_1` FOREIGN KEY (`id_usuario_reporta`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `reportes_usuarios_ibfk_2` FOREIGN KEY (`id_usuario_reportado`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD CONSTRAINT `servicios_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`);

--
-- Filtros para la tabla `ubicaciones`
--
ALTER TABLE `ubicaciones`
  ADD CONSTRAINT `fk_publicacion_ubicacion` FOREIGN KEY (`id_publicacion`) REFERENCES `publicaciones` (`id_publicacion`),
  ADD CONSTRAINT `fk_usuario_ubicacion` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `ubicaciones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
