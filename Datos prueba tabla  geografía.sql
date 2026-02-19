-- PAÍSES
INSERT INTO paises (siglaPais, codMoneda, nombrePais) VALUES
('CL', 'CLP', 'Chile'),
('AR', 'ARS', 'Argentina'),
('PE', 'PEN', 'Perú');

-- REGIONES (para Chile)
INSERT INTO regiones (idPais, nombreRegion, codRegion) VALUES
(1, 'Metropolitana', 'RM'),
(1, 'Valparaíso', 'V');

-- CIUDADES
INSERT INTO ciudades (idRegion, nombreCiudad) VALUES
(1, 'Santiago'),
(1, 'Puente Alto'),
(2, 'Valparaíso'),
(2, 'Viña del Mar');

-- COMUNAS
INSERT INTO comunas (idCiudad, nomComuna) VALUES
(1, 'Santiago Centro'),
(1, 'Providencia'),
(1, 'Las Condes'),
(2, 'Puente Alto'),
(3, 'Valparaíso'),
(4, 'Viña del Mar');