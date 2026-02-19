CREATE TABLE comunas (
    idComuna INT PRIMARY KEY AUTO_INCREMENT,
    idCiudad INT NOT NULL,
    nomComuna VARCHAR(100) NOT NULL,
    vigente BOOLEAN DEFAULT 1,
    FOREIGN KEY (idCiudad) REFERENCES ciudades(idCiudad)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;