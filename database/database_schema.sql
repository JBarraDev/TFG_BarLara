-- ============================================
--   CREACIÓN DE TABLAS DEL PROYECTO CAFÉ LARA
-- ============================================

CREATE DATABASE IF NOT EXISTS proyectoDAW_cafe_bar_lara;

USE proyectoDAW_cafe_bar_lara;

-- ======================
--  TABLA: roles
-- ======================
CREATE TABLE roles (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50)
);

-- ======================
--  TABLA: usuarios
-- ======================
CREATE TABLE usuarios (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user VARCHAR(50),
    email VARCHAR(100),
    telefono VARCHAR(9),
    password VARCHAR(255),
    id_rol INT(11),

    FOREIGN KEY (id_rol) REFERENCES roles(id)
);

-- ======================
--  TABLA: estados
-- ======================
CREATE TABLE estados (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50)
);

-- ======================
--  TABLA: reservas
-- ======================
CREATE TABLE reservas (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_user INT(11),
    fecha DATETIME,
    num_personas INT(11),
    zona ENUM('dentro', 'fuera'),
    id_estado INT(11),

    FOREIGN KEY (id_user) REFERENCES usuarios(id),
    FOREIGN KEY (id_estado) REFERENCES estados(id)
);

-- ======================
--  TABLA: pedidos
-- ======================
CREATE TABLE pedidos (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_user INT(11),
    hora_pedido DATETIME,
    hora_recogida DATETIME,
    id_estado INT(11),

    FOREIGN KEY (id_user) REFERENCES usuarios(id),
    FOREIGN KEY (id_estado) REFERENCES estados(id)
);

-- ======================
--  TABLA: categorias
-- ======================
CREATE TABLE categorias (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100)
);

-- ======================
--  TABLA: menu_items
-- ======================
CREATE TABLE menu_items (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200),
    precio DECIMAL(10,2),
    id_categoria INT(11),
    disponible TINYINT(1),
    imagen_url VARCHAR(255),
    fecha_creacion DATETIME,

    FOREIGN KEY (id_categoria) REFERENCES categorias(id)
);

-- ======================
--  TABLA: lineas_pedido
-- ======================
CREATE TABLE lineas_pedido (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT(11),
    id_menu_item INT(11),
    cantidad INT(11),
    importe DECIMAL(10,2),

    FOREIGN KEY (id_pedido) REFERENCES pedidos(id),
    FOREIGN KEY (id_menu_item) REFERENCES menu_items(id)
);

-- ======================
--  TABLA: alergenos
-- ======================
CREATE TABLE alergenos (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    icono_url VARCHAR(255)
);

-- ======================
--  TABLA: menu_items_alergenos
-- ======================
CREATE TABLE menu_items_alergenos (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_menu_item INT(11),
    id_alergeno INT(11),

    FOREIGN KEY (id_menu_item) REFERENCES menu_items(id),
    FOREIGN KEY (id_alergeno) REFERENCES alergenos(id)
);

-- ======================
--  TABLA: valoraciones
-- ======================
CREATE TABLE valoraciones (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_user INT(11),
    id_menu_item INT(11),
    valoracion TINYINT(1),
    comentario TEXT,
    fecha_creacion DATETIME,
    fecha_actualizacion DATETIME,

    FOREIGN KEY (id_user) REFERENCES usuarios(id),
    FOREIGN KEY (id_menu_item) REFERENCES menu_items(id)
);

-- ======================
--  Fin del Script
-- ======================
