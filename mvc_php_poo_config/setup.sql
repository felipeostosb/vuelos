-- Ejecuta este script en phpMyAdmin o en la consola MySQL

-- Se usa utf8mb4 para soporte completo de caracteres y emojis
CREATE DATABASE IF NOT EXISTS mvc_demo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE mvc_demo;

-- Tabla de usuarios original
CREATE TABLE IF NOT EXISTS usuarios (
    id       INT          AUTO_INCREMENT PRIMARY KEY,
    nombre   VARCHAR(100) NOT NULL,
    email    VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    creado   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

/* ====================================================================
  FUTURA IMPLEMENTACIÓN: Base de datos para Ayuda y Soporte
  (Descomentar esta sección cuando se comience a trabajar con la BD)
====================================================================

CREATE TABLE IF NOT EXISTS soporte_tickets (
    id       INT          AUTO_INCREMENT PRIMARY KEY,
    nombre   VARCHAR(100) NOT NULL,
    email    VARCHAR(150) NOT NULL,
    mensaje  TEXT         NOT NULL,
    estado   VARCHAR(20)  DEFAULT 'Pendiente',
    creado   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

*/