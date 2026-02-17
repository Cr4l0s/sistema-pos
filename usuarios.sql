-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-12-2024 a las 23:33:06
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
-- Base de datos: `practica_sventas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `idUsuario` int(11) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_cambio` timestamp NOT NULL DEFAULT current_timestamp(),
  `ApPaterno` varchar(100) DEFAULT NULL,
  `ApMaterno` varchar(100) DEFAULT NULL,
  `NombreUsuario` varchar(50) NOT NULL,
  `fonofijo` varchar(50) DEFAULT NULL,
  `fonocelular1` varchar(50) DEFAULT NULL,  
  `fonocelular2` varchar(50) DEFAULT NULL,    
  `vigente` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`idUsuario`, `nombres`, `email`, `password`, `fecha_registro`, `fecha_cambio`, `ApPaterno`, `ApMaterno`, `NombreUsuario`, `fonofijo`, `fonocelular1`,  `fonocelular2`, `vigente`) VALUES
(1, 'Jorge', 'jbravoh@gmail.com', '$2y$10$KBxtAPqWVBl0GXjlUmhkQOs8w0dYfwrRJ1EVmxOFu2g5HOoW0A0Uy', '2026-01-31 18:28:00', '2026-01-31 18:28:00', 'Bravo', 'Hinojosa', 'Jorge', '993123552','993123552', '993123552', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`idUsuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
