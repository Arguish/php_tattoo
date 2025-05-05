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
   CREATE DATABASE IF NOT EXISTS tattoo_db;
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

## Esquema de Base de Datos

```mermaid
erDiagram
    usuarios ||--o{ reservas : "cliente"
    usuarios ||--o{ reservas : "artista"
    servicios ||--o{ reservas : "servicio"
    reservas ||--o{ facturas : "genera"

    usuarios {
        int id PK
        varchar(50) nombre
        varchar(100) email UK
        varchar(255) password
        enum rol
        varchar(15) telefono
        datetime fecha_registro
        boolean activo
    }

    servicios {
        int id PK
        varchar(100) nombre
        text descripcion
        int duracion
        decimal(8,2) precio
        int artista_id FK
        boolean activo
    }

    reservas {
        int id PK
        int cliente_id FK
        int artista_id FK
        int servicio_id FK
        datetime fecha_hora
        enum estado
        text observaciones
        datetime fecha_creacion
    }

    facturas {
        int id PK
        int reserva_id FK UK
        decimal(10,2) total
        datetime fecha_emision
        enum metodo_pago
        boolean pagada
    }
```

### Descripción de Tablas

- **usuarios**: Almacena información de usuarios (nombre único, credenciales y roles)
- **servicios**: Catálogo de servicios disponibles para reservar
- **reservas**: Registro detallado de cada cita programada
- **facturas**: Información financiera de las reservas completadas

### Importar Esquema

```bash
mysql -u usuario -p nombre_base_datos < schema.sql
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
