-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-03-2026 a las 00:23:32
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
-- Base de datos: `practica_sventas_desa`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paises`
--

CREATE TABLE `paises` (
  `idPais` int(11) NOT NULL,
  `nombrePais` varchar(100) NOT NULL,
  `siglaPais` varchar(10) DEFAULT NULL,
  `codMoneda` varchar(10) DEFAULT NULL,
  `vigente` tinyint(1) DEFAULT 1,
  `simbolo_moneda` varchar(10) NOT NULL DEFAULT '$' COMMENT 'Símbolo de la moneda de curso legal ($, US$, €, etc.)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paises`
--

INSERT INTO `paises` (`idPais`, `nombrePais`, `siglaPais`, `codMoneda`, `vigente`, `simbolo_moneda`) VALUES
(1, 'Chile', 'CL', 'CLP', 1, '$'),
(2, 'Argentina', 'AR', 'ARS', 0, '$'),
(3, 'Perú', 'PE', 'PEN', 0, '$'),
(4, 'Argentina', 'AR', 'ARS', 0, '$'),
(5, 'Argentina', 'AR', 'ARS', 0, '$'),
(6, 'Argentina', 'AR', 'ARS', 0, '$'),
(7, 'ARGENTINA', 'AR', 'ARS', 1, '$'),
(8, 'BRASIL', 'BR', 'BRL', 0, '$'),
(9, 'CHILE', 'CL', 'CLP', 1, '$'),
(10, 'COLOMBIA', 'CO', 'COP', 1, '$'),
(11, 'COSTA RICA', 'CR', 'CRC', 1, '$'),
(12, 'EL SALVADOR', 'SV', 'USD', 1, '$'),
(13, 'GUATEMALA', 'GT', 'GTQ', 1, '$'),
(14, 'HONDURAS', 'HN', 'HNL', 1, '$'),
(15, 'MEXICO', 'MX', 'MXN', 1, '$'),
(16, 'PERU', 'PE', 'PEN', 1, '$'),
(17, 'Argentina', 'Ar', 'ARS', 0, '$'),
(18, 'Argentina', 'Ar', 'ARS', 0, '$'),
(19, 'Argentina', 'Ar', 'ARS', 0, '$'),
(20, 'Argentina', 'Ar', 'ARS', 0, '$'),
(21, 'Argentina', 'Ar', 'ARS', 0, '$'),
(22, 'Argentina', 'Ar', 'ARS', 0, '$'),
(23, 'Argentina', 'Ar', 'ARS', 0, '$'),
(24, 'Argentina', 'Ar', 'ARS', 0, '$'),
(25, 'Argentina', 'Ar', 'ARS', 0, '$'),
(26, 'Argentina', 'Ar', 'ARS', 0, '$'),
(27, 'ARGENTINA', 'AR', 'ARS', 0, '$'),
(28, 'BOLIVIA', 'BO', 'BOB', 1, 'Bs.'),
(29, 'BRASIL', 'BR', 'BRL', 1, 'R$'),
(30, 'CANADÁ', 'CA', 'CAD', 1, 'C$'),
(31, 'CHILE', 'CL', 'CLP', 1, '$'),
(32, 'COLOMBIA', 'CO', 'COP', 1, '$'),
(33, 'COSTA RICA', 'CR', 'CRC', 1, '₡'),
(34, 'CUBA', 'CU', 'CUP', 1, '$'),
(35, 'REPÚBLICA DOMINICANA', 'DO', 'DOP', 1, 'RD$'),
(36, 'ECUADOR', 'EC', 'USD', 1, '$'),
(37, 'EL SALVADOR', 'SV', 'USD', 1, '$'),
(38, 'GUATEMALA', 'GT', 'GTQ', 1, 'Q'),
(39, 'HONDURAS', 'HN', 'HNL', 1, 'L'),
(40, 'MÉXICO', 'MX', 'MXN', 1, '$'),
(41, 'NICARAGUA', 'NI', 'NIO', 1, 'C$'),
(42, 'PANAMÁ', 'PA', 'PAB', 1, 'B/.'),
(43, 'PARAGUAY', 'PY', 'PYG', 1, '₲'),
(44, 'PERÚ', 'PE', 'PEN', 1, 'S/'),
(45, 'PUERTO RICO', 'PR', 'USD', 1, '$'),
(46, 'URUGUAY', 'UY', 'UYU', 1, '$'),
(47, 'VENEZUELA', 'VE', 'VES', 1, 'Bs.'),
(48, 'ESTADOS UNIDOS', 'US', 'USD', 1, 'US$'),
(49, 'ALBANIA', 'AL', 'ALL', 1, 'L'),
(50, 'ALEMANIA', 'DE', 'EUR', 1, '€'),
(51, 'ANDORRA', 'AD', 'EUR', 1, '€'),
(52, 'AUSTRIA', 'AT', 'EUR', 1, '€'),
(53, 'BÉLGICA', 'BE', 'EUR', 1, '€'),
(54, 'BIELORRUSIA', 'BY', 'BYN', 1, 'Br'),
(55, 'BOSNIA Y HERZEGOVINA', 'BA', 'BAM', 1, 'KM'),
(56, 'BULGARIA', 'BG', 'BGN', 1, 'лв'),
(57, 'CROACIA', 'HR', 'EUR', 1, '€'),
(58, 'DINAMARCA', 'DK', 'DKK', 1, 'kr'),
(59, 'ESLOVAQUIA', 'SK', 'EUR', 1, '€'),
(60, 'ESLOVENIA', 'SI', 'EUR', 1, '€'),
(61, 'ESPAÑA', 'ES', 'EUR', 1, '€'),
(62, 'ESTONIA', 'EE', 'EUR', 1, '€'),
(63, 'FINLANDIA', 'FI', 'EUR', 1, '€'),
(64, 'FRANCIA', 'FR', 'EUR', 1, '€'),
(65, 'GRECIA', 'GR', 'EUR', 1, '€'),
(66, 'HUNGRÍA', 'HU', 'HUF', 1, 'Ft'),
(67, 'IRLANDA', 'IE', 'EUR', 1, '€'),
(68, 'ISLANDIA', 'IS', 'ISK', 1, 'kr'),
(69, 'ITALIA', 'IT', 'EUR', 1, '€'),
(70, 'KOSOVO', 'XK', 'EUR', 1, '€'),
(71, 'LETONIA', 'LV', 'EUR', 1, '€'),
(72, 'LIECHTENSTEIN', 'LI', 'CHF', 1, 'Fr'),
(73, 'LITUANIA', 'LT', 'EUR', 1, '€'),
(74, 'LUXEMBURGO', 'LU', 'EUR', 1, '€'),
(75, 'MACEDONIA DEL NORTE', 'MK', 'MKD', 1, 'ден'),
(76, 'MALTA', 'MT', 'EUR', 1, '€'),
(77, 'MOLDOVA', 'MD', 'MDL', 1, 'L'),
(78, 'MÓNACO', 'MC', 'EUR', 1, '€'),
(79, 'MONTENEGRO', 'ME', 'EUR', 1, '€'),
(80, 'NORUEGA', 'NO', 'NOK', 1, 'kr'),
(81, 'PAÍSES BAJOS', 'NL', 'EUR', 1, '€'),
(82, 'POLONIA', 'PL', 'PLN', 1, 'zł'),
(83, 'PORTUGAL', 'PT', 'EUR', 1, '€'),
(84, 'REINO UNIDO', 'GB', 'GBP', 1, '£'),
(85, 'REPÚBLICA CHECA', 'CZ', 'CZK', 1, 'Kč'),
(86, 'RUMANIA', 'RO', 'RON', 1, 'lei'),
(87, 'RUSIA', 'RU', 'RUB', 1, '₽'),
(88, 'SAN MARINO', 'SM', 'EUR', 1, '€'),
(89, 'SERBIA', 'RS', 'RSD', 1, 'дин'),
(90, 'SUECIA', 'SE', 'SEK', 1, 'kr'),
(91, 'SUIZA', 'CH', 'CHF', 1, 'Fr'),
(92, 'UCRANIA', 'UA', 'UAH', 1, '₴'),
(93, 'VATICANO', 'VA', 'EUR', 1, '€'),
(94, 'CHAD', 'CF', 'AUD', 1, 'A$'),
(95, 'ARABIA SAUDITA', 'SA', 'SAR', 1, '﷼'),
(96, 'ARMENIA', 'AM', 'AMD', 1, '֏'),
(97, 'AZERBAIYÁN', 'AZ', 'AZN', 1, '₼'),
(98, 'BAHRÉIN', 'BH', 'BHD', 1, '.د.ب'),
(99, 'BANGLADESH', 'BD', 'BDT', 1, '৳'),
(100, 'BRUNÉI', 'BN', 'BND', 1, '$'),
(101, 'BUTÁN', 'BT', 'BTN', 1, 'Nu.'),
(102, 'CAMBOYA', 'KH', 'KHR', 1, '៛'),
(103, 'CHINA', 'CN', 'CNY', 1, '¥'),
(104, 'COREA DEL SUR', 'KR', 'KRW', 1, '₩'),
(105, 'EMIRATOS ÁRABES UNIDOS', 'AE', 'AED', 1, 'د.إ'),
(106, 'FILIPINAS', 'PH', 'PHP', 1, '₱'),
(107, 'GEORGIA', 'GE', 'GEL', 1, '₾'),
(108, 'INDIA', 'IN', 'INR', 1, '₹'),
(109, 'INDONESIA', 'ID', 'IDR', 1, 'Rp'),
(110, 'IRAK', 'IQ', 'IQD', 1, 'د.ع'),
(111, 'IRÁN', 'IR', 'IRR', 1, '﷼'),
(112, 'ISRAEL', 'IL', 'ILS', 1, '₪'),
(113, 'JAPÓN', 'JP', 'JPY', 1, '¥'),
(114, 'JORDANIA', 'JO', 'JOD', 1, 'د.ا'),
(115, 'KAZAJISTÁN', 'KZ', 'KZT', 1, '₸'),
(116, 'KUWAIT', 'KW', 'KWD', 1, 'د.ك'),
(117, 'LAOS', 'LA', 'LAK', 1, '₭'),
(118, 'LÍBANO', 'LB', 'LBP', 1, 'ل.ل'),
(119, 'MALASIA', 'MY', 'MYR', 1, 'RM'),
(120, 'MALDIVAS', 'MV', 'MVR', 1, 'Rf'),
(121, 'MONGOLIA', 'MN', 'MNT', 1, '₮'),
(122, 'MYANMAR', 'MM', 'MMK', 1, 'K'),
(123, 'NEPAL', 'NP', 'NPR', 1, 'रू'),
(124, 'OMÁN', 'OM', 'OMR', 1, 'ر.ع.'),
(125, 'PAKISTÁN', 'PK', 'PKR', 1, '₨'),
(126, 'QATAR', 'QA', 'QAR', 1, 'ر.ق'),
(127, 'SINGAPUR', 'SG', 'SGD', 1, '$'),
(128, 'SRI LANKA', 'LK', 'LKR', 1, '₨'),
(129, 'TAILANDIA', 'TH', 'THB', 1, '฿'),
(130, 'TAIWÁN', 'TW', 'TWD', 1, 'NT$'),
(131, 'TAYIKISTÁN', 'TJ', 'TJS', 1, 'ЅМ'),
(132, 'TURQUÍA', 'TR', 'TRY', 1, '₺'),
(133, 'TURKMENISTÁN', 'TM', 'TMT', 1, 'm'),
(134, 'UZBEKISTÁN', 'UZ', 'UZS', 1, 'soʻm'),
(135, 'VIETNAM', 'VN', 'VND', 1, '₫'),
(136, 'YEMEN', 'YE', 'YER', 1, '﷼'),
(137, 'ARGELIA', 'DZ', 'DZD', 1, 'د.ج'),
(138, 'ANGOLA', 'AO', 'AOA', 1, 'Kz'),
(139, 'BENÍN', 'BJ', 'XOF', 1, 'CFA'),
(140, 'BOTSUANA', 'BW', 'BWP', 1, 'P'),
(141, 'BURKINA FASO', 'BF', 'XOF', 1, 'CFA'),
(142, 'BURUNDI', 'BI', 'BIF', 1, 'FBu'),
(143, 'CABO VERDE', 'CV', 'CVE', 1, '$'),
(144, 'CAMERÚN', 'CM', 'XAF', 1, 'FCFA'),
(145, 'CHAD', 'TD', 'XAF', 1, 'FCFA'),
(146, 'COMORAS', 'KM', 'KMF', 1, 'CF'),
(147, 'COSTA DE MARFIL', 'CI', 'XOF', 1, 'CFA'),
(148, 'EGIPTO', 'EG', 'EGP', 1, '£'),
(149, 'ETIOPÍA', 'ET', 'ETB', 1, 'Br'),
(150, 'GABÓN', 'GA', 'XAF', 1, 'FCFA'),
(151, 'GAMBIA', 'GM', 'GMD', 1, 'D'),
(152, 'GHANA', 'GH', 'GHS', 1, '₵'),
(153, 'GUINEA', 'GN', 'GNF', 1, 'FG'),
(154, 'GUINEA-BISÁU', 'GW', 'XOF', 1, 'CFA'),
(155, 'KENIA', 'KE', 'KES', 1, 'KSh'),
(156, 'LESOTHO', 'LS', 'LSL', 1, 'L'),
(157, 'LIBERIA', 'LR', 'LRD', 1, '$'),
(158, 'LIBIA', 'LY', 'LYD', 1, 'ل.د'),
(159, 'MADAGASCAR', 'MG', 'MGA', 1, 'Ar'),
(160, 'MALAWI', 'MW', 'MWK', 1, 'MK'),
(161, 'MALÍ', 'ML', 'XOF', 1, 'CFA'),
(162, 'MARRUECOS', 'MA', 'MAD', 1, 'د.م.'),
(163, 'MAURICIO', 'MU', 'MUR', 1, '₨'),
(164, 'MAURITANIA', 'MR', 'MRU', 1, 'UM'),
(165, 'MOZAMBIQUE', 'MZ', 'MZN', 1, 'MT'),
(166, 'NAMIBIA', 'NA', 'NAD', 1, '$'),
(167, 'NÍGER', 'NE', 'XOF', 1, 'CFA'),
(168, 'NIGERIA', 'NG', 'NGN', 1, '₦'),
(169, 'KENIA', 'KE', 'KES', 1, 'KSh'),
(170, 'REPÚBLICA CENTROAFRICANA', 'CF', 'XAF', 1, 'FCFA'),
(171, 'REPÚBLICA DEMOCRÁTICA DEL CONGO', 'CD', 'CDF', 1, 'FC'),
(172, 'RUANDA', 'RW', 'RWF', 1, 'RF'),
(173, 'SENEGAL', 'SN', 'XOF', 1, 'CFA'),
(174, 'SEYCHELLES', 'SC', 'SCR', 1, '₨'),
(175, 'SIERRA LEONA', 'SL', 'SLL', 1, 'Le'),
(176, 'SOMALIA', 'SO', 'SOS', 1, 'Sh'),
(177, 'SUDÁFRICA', 'ZA', 'ZAR', 1, 'R'),
(178, 'SUDÁN', 'SD', 'SDG', 1, 'ج.س.'),
(179, 'SUDÁN DEL SUR', 'SS', 'SSP', 1, '£'),
(180, 'TANZANIA', 'TZ', 'TZS', 1, 'TSh'),
(181, 'TOGO', 'TG', 'XOF', 1, 'CFA'),
(182, 'TÚNEZ', 'TN', 'TND', 1, 'د.ت'),
(183, 'UGANDA', 'UG', 'UGX', 1, 'USh'),
(184, 'YIBUTI', 'DJ', 'DJF', 1, 'Fdj'),
(185, 'ZAMBIA', 'ZM', 'ZMW', 1, 'ZK'),
(186, 'ZIMBABUE', 'ZW', 'ZWL', 1, '$'),
(187, 'AUSTRALIA', 'AU', 'AUD', 1, '$'),
(188, 'FIYI', 'FJ', 'FJD', 1, '$'),
(189, 'KIRIBATI', 'KI', 'AUD', 1, '$'),
(190, 'ISLAS MARSHALL', 'MH', 'USD', 1, '$'),
(191, 'MICRONESIA', 'FM', 'USD', 1, '$'),
(192, 'NAURU', 'NR', 'AUD', 1, '$'),
(193, 'NUEVA ZELANDA', 'NZ', 'NZD', 1, '$'),
(194, 'PALAU', 'PW', 'USD', 1, '$'),
(195, 'PAPÚA NUEVA GUINEA', 'PG', 'PGK', 1, 'K'),
(196, 'SAMOA', 'WS', 'WST', 1, 'T'),
(197, 'ISLAS SALOMÓN', 'SB', 'SBD', 1, '$'),
(198, 'TONGA', 'TO', 'TOP', 1, 'T$'),
(199, 'TUVALU', 'TV', 'AUD', 1, '$'),
(200, 'VANUATU', 'VU', 'VUV', 1, 'VT'),
(201, 'Afganistán', 'AF', 'USD', 1, '$'),
(202, 'Chile', 'CL', 'PEN', 1, 'S/'),
(203, 'CHAD', 'CN', NULL, 1, '$'),
(204, 'CHAD', 'JP', NULL, 1, '$'),
(205, 'CHAD', 'AU', NULL, 1, '$'),
(206, 'KAZAJISTÁN', 'JP', NULL, 1, '$');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `paises`
--
ALTER TABLE `paises`
  ADD PRIMARY KEY (`idPais`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `paises`
--
ALTER TABLE `paises`
  MODIFY `idPais` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=207;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
