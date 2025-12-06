-- ============================================================
-- SCRIPT DE DATOS DE MUESTRA - TFG Café-Bar Lara
-- ============================================================
-- Este script inserta datos de ejemplo en todas las tablas
-- para poder probar el funcionamiento de la aplicación
-- 
-- Base de datos: tfg_cafe_bar_lara
-- ============================================================

USE proyectoDAW_cafe_bar_lara;

-- ======================
-- ROLES
-- ======================
INSERT INTO roles (nombre) VALUES
('administrador'),
('cliente');

-- ======================
-- USUARIOS
-- ======================
INSERT INTO usuarios (user, email, telefono, password, id_rol) VALUES
('admin', 'admin@cafelara.com', '666123456', '$2y$10$uCo9v6Cdvbx2ecUtj1bkqu8yJ2w0lMfdyg46aKrkGCSEdVo//CxCi', 1),
('cliente1', 'cliente1@email.com', '612345678', '$2y$10$DZGxTEiHUoCi5MiN0KPgLeImlK9h5eVJqTwz4SOQUYvISwqwi0eDG', 2),
('cliente2', 'cliente2@email.com', '623456789', '$2y$10$o7kf3ZJvyzwNMWpicLNNw.FzHoPaiXnqrp1Kuo5osGGDUrEyYmKDG', 2);

-- ======================
-- ESTADOS
-- ======================
INSERT INTO estados (nombre) VALUES
('Pendiente'),
('Aceptado'),
('Rechazado');

-- ======================
-- CATEGORÍAS
-- ======================
INSERT INTO categorias (nombre) VALUES
('Raciones'),
('Bocadillos'),
('Postres'),
('Bebidas');

-- ======================
-- ALÉRGENOS
-- ======================
INSERT INTO alergenos (nombre, icono_url) VALUES
('Gluten', 'assets/img/icon/alergenos/gluten.svg'),
('Lácteos', 'assets/img/icon/alergenos/lactosa.svg'),
('Frutos secos', 'assets/img/icon/alergenos/frutos_secos.svg'),
('Huevo', 'assets/img/icon/alergenos/huevo.svg'),
('Pescado', 'assets/img/icon/alergenos/pescado.svg'),
('Soja', 'assets/img/icon/alergenos/soja.svg');

-- ======================
-- ITEMS DEL MENÚ
-- ======================
INSERT INTO menu_items (nombre, precio, id_categoria, disponible, imagen_url, fecha_creacion) VALUES
('Pincho de Tortilla', 1.80, 1, 1, 'assets/img/items/tortilla.png', NOW()),
('Croquetas (6ud)', 3.20, 1, 1, 'assets/img/items/croquetas.png', NOW()),
('Ensaladilla', 2.40, 1, 1, 'assets/img/items/ensaladilla.png', NOW()),
('Vegetal', 3.40, 2, 1, 'assets/img/items/bocadillo_vegetal.png', NOW()),
('Perrito Caliente', 2.95, 2, 1, 'assets/img/items/perrito.png', NOW()),
('Cerveza', 1.65, 4, 1, 'assets/img/items/cerveza.png', NOW()),
('Café', 1.15, 4, 1, 'assets/img/items/cafe.png', NOW()),
('Vino', 1.20, 4, 1, 'assets/img/items/vino.png', NOW()),
('Zumo', 2.15, 4, 1, 'assets/img/items/zumo.png', NOW()),
('Tarta Tres Chocolates', 4.15, 3, 1, 'assets/img/items/tarta.png', NOW()
);

-- ======================
-- RELACIÓN MENÚ - ALÉRGENOS
-- ======================
INSERT INTO menu_items_alergenos (id_menu_item, id_alergeno) VALUES
(1, 4),
(2, 1),
(3, 2),
(3, 4),
(3, 5),
(6, 1),
(10, 1),
(10, 3),
(10, 4);

INSERT INTO valoraciones (id_user, id_menu_item, valoracion, comentario, fecha_creacion, fecha_actualizacion) VALUES
(2, 1, 5, 'La mejor tortilla que heprobado', NOW(), NOW()),
(3, 1, 2, 'Muy salada', NOW(), NOW()),
(3, 10, 5, 'No querrás solo un trozo', NOW(), NOW()),
(3, 2, 4, 'Bien jugosas', NOW(), NOW());


-- ============================================================
-- FIN DEL SCRIPT
-- ============================================================