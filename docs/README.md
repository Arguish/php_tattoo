# Gestión de Centro de Tatuajes

## Descripción

Aplicación web para la gestión integral de un centro de tatuajes. Permite a administradores, tatuadores, clientes y recepcionistas:

- Registrar y gestionar usuarios con distintos roles.
- Reservar, confirmar y llevar el histórico de citas.
- Gestionar perfiles de usuario.

## Características implementadas

- **Roles y autenticación**: Sistema de login y registro con roles de Administrador, Artista, Cliente y Recepcionista.
- **Gestión de usuarios**: Panel de administración para crear, editar y eliminar usuarios.
- **Reservas**: Sistema para gestionar citas entre clientes y artistas, con estados (pendiente, confirmada, completada, cancelada).
- **Interfaz responsive**: Diseño adaptable con Bootstrap 5 y JavaScript para validaciones.
- **Navegación intuitiva**: Menú de navegación dinámico según el rol del usuario.
- **Facturación**: Sistema completo para la gestión de facturas asociadas a reservas.
- **Reportes PDF**: Generación de informes en formato PDF para reservas y otros datos relevantes.
- **Notificaciones por email**: Sistema de envío de correos electrónicos para confirmaciones de citas y otras notificaciones.
- **Gestión de servicios**: Catálogo completo de servicios ofrecidos con precios y duración.
- **Sistema de logging**: Registro detallado de actividades y errores del sistema para facilitar el mantenimiento.

## Stack tecnológico

- **Back-end**: PHP 8.x (POO, PDO/MySQLi)
- **Front-end**: HTML5, CSS3, Bootstrap 5, JavaScript, jQuery
- **Base de datos**: MySQL o MariaDB
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
4. Configura el envío de correos en `config/mail.php` o usando variables de entorno en `.env`:
   ```php
   // Ejemplo de configuración en .env
   MAIL_HOST=smtp.example.com
   MAIL_PORT=587
   MAIL_USER=usuario@example.com
   MAIL_PASSWORD=contraseña
   MAIL_ENCRYPTION=tls
   MAIL_FROM=noreply@example.com
   ```
5. Importa el esquema SQL:
   ```sql
   CREATE DATABASE IF NOT EXISTS tattoo_db;
   USE tatuajes;
   -- Ejecuta el script schema.sql
   ```
6. Ajusta permisos de la carpeta `logs/` para registros del sistema.

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
        int reserva_id FK
        decimal(10,2) monto
        datetime fecha_emision
        boolean pagada
        varchar(100) metodo_pago
    }
```

### Descripción de Tablas

- **usuarios**: Almacena información de usuarios (nombre único, credenciales y roles)
- **servicios**: Catálogo de servicios disponibles para reservar
- **reservas**: Registro detallado de cada cita programada

### Importar Esquema

```bash
mysql -u usuario -p nombre_base_datos < schema.sql
```

## Uso

1. Accede a la URL principal y regístrate como Cliente o inicia sesión.
2. Como Cliente, solicita una nueva reserva seleccionando artista, servicio y fecha.
3. La Recepcionista o Administrador puede confirmar la reserva desde su panel.
4. Consulta tu historial de citas desde tu panel de usuario.
5. Genera reportes PDF de tus reservas desde la sección correspondiente.
6. Recibe notificaciones por correo electrónico cuando tus citas sean confirmadas.
7. Como Administrador, gestiona las facturas asociadas a las reservas completadas.
8. Consulta los registros del sistema en la carpeta de logs para solucionar problemas.

## Contribuciones

1. Haz un fork del proyecto.
2. Crea una rama (`git checkout -b feature/nueva-funcion`).
3. Haz commit de tus cambios (`git commit -am 'Añade función X'`).
4. Push a la rama (`git push origin feature/nueva-funcion`).
5. Abre un Pull Request.

## Licencia

Este proyecto está bajo la licencia MIT.
