CREATE DATABASE IF NOT EXISTS UO289684_DB;
USE UO289684_DB;

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    codigo_identificacion INT NOT NULL,
    profesion VARCHAR(100) NOT NULL,
    edad INT NOT NULL,
    genero ENUM('Masculino', 'Femenino', 'Otro') NOT NULL,
    pericia INT NOT NULL
);

CREATE TABLE resultados (
    id_resultado INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    dispositivo ENUM('Ordenador', 'Tableta', 'Telefono') NOT NULL,
    tiempo DECIMAL(10,3) NOT NULL,
    completado BOOLEAN NOT NULL,
    comentarios_usuario VARCHAR(500),
    propuestas VARCHAR(500),
    valoracion INT NOT NULL CHECK(valoracion >= 0 AND valoracion <= 10),
    respuestas VARCHAR(500),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

CREATE TABLE observaciones (
    id_observaciones INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    comentarios_observador VARCHAR(3000),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);
