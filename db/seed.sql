-- Active: 1746372293750@@127.0.0.1@3366@tattoo_db
-- Script para poblar la base de datos con datos de prueba
USE tattoo_db;

-- Limpiar datos existentes manteniendo integridad referencial
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS;
SET FOREIGN_KEY_CHECKS=0;
TRUNCATE TABLE facturas;
TRUNCATE TABLE reservas;
TRUNCATE TABLE servicios;
TRUNCATE TABLE usuarios;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;

-- Insertar usuarios con diferentes roles
INSERT INTO usuarios (nombre, email, password, rol, telefono) VALUES
('Admin Principal', 'admin@tattoo.com', SHA2('admin123', 256), 'admin', '555-0001'),
('Maria Gonzalez', 'maria.admin@tattoo.com', SHA2('maria123', 256), 'admin', '555-0002'),
('Juan Pérez', 'juan.artista@tattoo.com', SHA2('juan123', 256), 'artista', '555-1001'),
('Ana Martínez', 'ana.artista@tattoo.com', SHA2('ana123', 256), 'artista', '555-1002'),
('Carlos Rodríguez', 'carlos.artista@tattoo.com', SHA2('carlos123', 256), 'artista', '555-1003'),
('Laura Sánchez', 'laura.recep@tattoo.com', SHA2('laura123', 256), 'recepcionista', '555-2001'),
('Pedro Díaz', 'pedro.recep@tattoo.com', SHA2('pedro123', 256), 'recepcionista', '555-2002'),
('Sofia López', 'sofia.cliente@email.com', SHA2('sofia123', 256), 'cliente', '555-3001'),
('Miguel Torres', 'miguel.cliente@email.com', SHA2('miguel123', 256), 'cliente', '555-3002'),
('Elena Ruiz', 'elena.cliente@email.com', SHA2('elena123', 256), 'cliente', '555-3003'),
('Diego Morales', 'diego.cliente@email.com', SHA2('diego123', 256), 'cliente', '555-3004'),
('Carmen Jiménez', 'carmen.cliente@email.com', SHA2('carmen123', 256), 'cliente', '555-3005');

-- Insertar servicios variados
INSERT INTO servicios (nombre, descripcion, duracion, precio, artista_id) VALUES
('Tatuaje Pequeño', 'Diseños simples de hasta 5cm', 60, 80.00, 3),
('Tatuaje Mediano', 'Diseños detallados de 5-15cm', 120, 160.00, 3),
('Tatuaje Grande', 'Diseños complejos de 15-30cm', 240, 300.00, 4),
('Tatuaje Personalizado', 'Diseño único según especificaciones del cliente', 180, 250.00, 4),
('Cover-Up Pequeño', 'Cubrir tatuajes pequeños existentes', 90, 120.00, 5),
('Cover-Up Grande', 'Cubrir tatuajes grandes existentes', 300, 400.00, 5),
('Diseño Tradicional', 'Estilo old school americano', 150, 200.00, 3),
('Diseño Realista', 'Tatuajes de estilo fotorrealista', 210, 280.00, 4),
('Diseño Minimalista', 'Líneas finas y diseños simples', 90, 100.00, 5);

-- Insertar reservas con diferentes estados
INSERT INTO reservas (cliente_id, artista_id, servicio_id, fecha_hora, estado, observaciones) VALUES
(8, 3, 1, DATE_SUB(NOW(), INTERVAL 5 DAY), 'completada', 'Cliente satisfecho con el resultado'),
(9, 4, 3, DATE_SUB(NOW(), INTERVAL 3 DAY), 'completada', 'Se ajustó el diseño durante la sesión'),
(10, 5, 5, DATE_SUB(NOW(), INTERVAL 2 DAY), 'completada', 'Cover-up exitoso'),
(11, 3, 2, DATE_ADD(NOW(), INTERVAL 2 DAY), 'confirmada', 'Cliente solicita diseño previo'),
(12, 4, 4, DATE_ADD(NOW(), INTERVAL 3 DAY), 'confirmada', 'Primera sesión de diseño personalizado'),
(8, 5, 6, DATE_ADD(NOW(), INTERVAL 4 DAY), 'confirmada', 'Sesión adicional de cover-up'),
(9, 3, 7, DATE_ADD(NOW(), INTERVAL 7 DAY), 'pendiente', 'Pendiente de confirmar diseño'),
(10, 4, 8, DATE_ADD(NOW(), INTERVAL 8 DAY), 'pendiente', 'Consulta previa necesaria'),
(11, 5, 9, DATE_SUB(NOW(), INTERVAL 1 DAY), 'cancelada', 'Cliente no pudo asistir'),
(12, 3, 1, DATE_SUB(NOW(), INTERVAL 4 DAY), 'cancelada', 'Reprogramar para el próximo mes');

-- Insertar facturas para las reservas completadas
INSERT INTO facturas (reserva_id, total, metodo_pago, pagada) VALUES
(1, 80.00, 'efectivo', TRUE),
(2, 300.00, 'tarjeta', TRUE),
(3, 120.00, 'transferencia', TRUE);

-- Verificar la inserción de datos
SELECT 'Usuarios insertados: ' AS '', COUNT(*) FROM usuarios;
SELECT 'Servicios insertados: ' AS '', COUNT(*) FROM servicios;
SELECT 'Reservas insertadas: ' AS '', COUNT(*) FROM reservas;
SELECT 'Facturas insertadas: ' AS '', COUNT(*) FROM facturas;