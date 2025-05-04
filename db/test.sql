-- Script de prueba para validar el esquema de la base de datos

-- Verificar creación de tablas
SHOW TABLES;

-- Verificar estructura de usuarios
DESCRIBE usuarios;

-- Verificar datos iniciales
SELECT * FROM usuarios WHERE rol = 'admin';
SELECT * FROM servicios;

-- Prueba de relaciones básicas
SELECT 
    r.id AS reserva_id,
    c.nombre AS cliente,
    a.nombre AS artista,
    s.nombre AS servicio,
    r.fecha_hora
FROM reservas r
JOIN usuarios c ON r.cliente_id = c.id
JOIN usuarios a ON r.artista_id = a.id
JOIN servicios s ON r.servicio_id = s.id;

-- Limpieza para pruebas (opcional)
-- DELETE FROM facturas;
-- DELETE FROM reservas;
-- DELETE FROM servicios;
-- DELETE FROM usuarios WHERE id > 2;

-- Instrucciones de uso:
-- 1. mysql -u root -p < schema.sql
-- 2. mysql -u root -p < test.sql
-- 3. Verificar que no hay errores y los resultados esperados