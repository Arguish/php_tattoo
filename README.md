# Gestión de Centro de Tatuajes

## Descripción

Aplicación web para la gestión integral de un centro de tatuajes. Permite a administradores, tatuadores, clientes y recepcionistas:

- Registrar y gestionar usuarios con distintos roles.
- Reservar, confirmar y llevar el histórico de citas.
- Generar facturas e historiales de reservas en PDF.
- Enviar notificaciones y recordatorios por correo electrónico.

## Características principales

- **Roles y autenticación**: Administrador, Artista, Cliente, Recepcionista con áreas privadas y CRUD.
- **Reservas**: Entidad `reserva` relaciona cliente, artista, servicio, fecha/hora y observaciones.
- **Gestión de servicios**: CRUD de servicios ofrecidos (tipos de tatuaje, tarifas).
- **PDF dinámico**: Facturas y reportes de historial de tatuajes (Dompdf).
- **Email**: Notificaciones automáticas y recordatorios (PHPMailer + SMTP/TLS).
- **Interfaz responsive**: Bootstrap 5, JavaScript y jQuery para validaciones y AJAX.
- **Base de datos relacional**: MySQL/MariaDB.

## Stack tecnológico

- **Back-end**: PHP 8.x (POO, PDO/MySQLi)
- **Front-end**: HTML5, CSS3, Bootstrap 5, JavaScript, jQuery
- **Base de datos**: MySQL o MariaDB
- **Generación de PDF**: Dompdf (o FPDF/TCPDF)
- **Envío de correo**: PHPMailer
- **Servidor**: Apache o Nginx

## Requisitos previos

- PHP 8.x instalado
- Servidor web (Apache/Nginx)
- MySQL o MariaDB
- Composer (para dependencias PHP)

## Instalación y configuración

1. Clona el repositorio:
   ```bash
   git clone https://.../tatuajes.git
   cd tatuajes
   ```
2. Instala dependencias PHP:
   ```bash
   composer install
   ```
3. Configura la base de datos en `config/database.php`:
   ```php
   return [
     'host' => 'localhost',
     'db'   => 'tatuajes',
     'user' => 'usuario',
     'pass' => 'contraseña',
   ];
   ```
4. Importa el esquema SQL:
   ```sql
   CREATE DATABASE tatuajes;
   USE tatuajes;
   -- Ejecuta el script schema.sql
   ```
5. Configura SMTP en `config/mail.php`:
   ```php
   return [
     'host'       => 'smtp.ejemplo.com',
     'username'   => 'user@ejemplo.com',
     'password'   => 'tu_pass',
     'port'       => 587,
     'encryption' => 'tls',
   ];
   ```
6. Ajusta permisos de la carpeta `storage/` para PDFs y logs.

## Estructura de la base de datos

```sql
-- Usuarios con roles
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100),
  email VARCHAR(100) UNIQUE,
  rol ENUM('admin','artista','cliente','recepcionista'),
  password VARCHAR(255),
  creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Servicios ofrecidos
CREATE TABLE servicios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100),
  precio DECIMAL(8,2)
);

-- Reservas de cita
CREATE TABLE reservas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT,
  artista_id INT,
  servicio_id INT,
  fecha DATETIME,
  observaciones TEXT,
  FOREIGN KEY (cliente_id) REFERENCES usuarios(id),
  FOREIGN KEY (artista_id) REFERENCES usuarios(id),
  FOREIGN KEY (servicio_id) REFERENCES servicios(id)
);

-- Facturas generadas
CREATE TABLE facturas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reserva_id INT,
  generado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (reserva_id) REFERENCES reservas(id)
);
```

## Uso

1. Accede a la URL principal y regístrate como Cliente o inicia sesión.
2. Como Cliente, solicita una nueva reserva seleccionando artista, servicio y fecha.
3. La Recepcionista confirma la reserva desde su panel.
4. Al confirmarse, se genera y envía la factura en PDF al cliente y artista.
5. 24 h antes, un recordatorio automático llega por correo.
6. Consulta tu historial de citas y descarga tus reportes en PDF.

## Generación de PDF

- Plantillas HTML/CSS en `views/pdf/`
- Controlador `PdfController.php` usa Dompdf para convertir vistas a PDF.

## Envío de correos

- PHPMailer configurado en `MailService.php`.
- Métodos:
  - `sendReservationConfirmation($user, $pdfPath)`
  - `sendReminder($user, $reservation)`

## Contribuciones

1. Haz un fork del proyecto.
2. Crea una rama (`git checkout -b feature/nueva-funcion`).
3. Haz commit de tus cambios (`git commit -am 'Añade función X'`).
4. Push a la rama (`git push origin feature/nueva-funcion`).
5. Abre un Pull Request.

## Licencia

Este proyecto está bajo la licencia MIT.
