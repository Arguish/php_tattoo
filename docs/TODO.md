# TODO.md

Lista de tareas completadas para el proyecto "Gestión de Centro de Tatuajes".

## 📦 Configuración del Entorno

- [x] Instalar PHP 8.x y extensiones necesarias (pdo, mbstring)
- [x] Configurar servidor web (Apache/Nginx)
- [x] Instalar MySQL/MariaDB y crear usuario de base de datos
- [x] Instalar Composer y dependencias
- [x] Configurar archivo `config/database.php` con credenciales
- [x] Ajustar permisos de carpetas `logs/`

## 🗄️ Base de Datos

- [x] Crear script SQL `schema.sql` para tablas: `usuarios`, `servicios`, `reservas`

## 🔐 Autenticación y Roles

- [x] Implementar sistema de registro y login (sessions)
- [x] Crear control de acceso por rol
- [x] Panel de administración de usuarios (CRUD)
- [x] Validar campos y gestionar contraseñas con hashing

## 📅 Reservas (Entidad Cita)

- [x] CRUD de `reservas` en back‑end
- [x] Formulario de reserva para Cliente (selección artista, servicio, fecha, obs.)
- [x] Panel para confirmar/rechazar reservas

## 🖥️ Front‑end UI

- [x] Maquetación de layout principal con Bootstrap
- [x] Navbar dinámica según rol logueado
- [x] Páginas de dashboard por rol (Admin, Artista, Cliente, Recepcionista)
- [x] Tablas responsivas de reservas

## 📖 Documentación

- [x] Completar `README.md` con ejemplos de uso

## Próximas mejoras

- Generación de PDF para facturas e historiales
- Envío de correos para notificaciones y recordatorios
- Estadísticas y reportes avanzados
- Exportación de datos
