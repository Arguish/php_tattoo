# TODO.md

Lista de tareas organizadas por apartados para el proyecto "Gestión de Centro de Tatuajes".

## 📦 Configuración del Entorno

- [ ] Instalar PHP 8.x y extensiones necesarias (pdo, mbstring)
- [ ] Configurar servidor web (Apache/Nginx)
- [ ] Instalar MySQL/MariaDB y crear usuario de base de datos
- [ ] Instalar Composer y dependencias (Dompdf, PHPMailer)
- [ ] Configurar archivo `config/database.php` con credenciales
- [ ] Configurar archivo `config/mail.php` con datos SMTP
- [ ] Ajustar permisos de carpetas `storage/` y `logs/`

## 🗄️ Base de Datos

- [ ] Definir y documentar el esquema ER
- [ ] Crear script SQL `schema.sql` para tablas: `usuarios`, `servicios`, `reservas`, `facturas`
- [ ] Probar importación del esquema en entorno local
- [ ] Escribir migraciones (opcional) para versiones futuras

## 🔐 Autenticación y Roles

- [ ] Implementar sistema de registro y login (sessions)
- [ ] Crear middleware / control de acceso por rol
- [ ] Panel de administración de usuarios (CRUD roles)
- [ ] Validar campos y gestionar contraseñas con hashing (password_hash)

## 💼 Gestión de Servicios

- [ ] CRUD de `servicios` en back‑end (PHP)
- [ ] Formularios Bootstrap para creación/edición de servicios
- [ ] Validaciones con JavaScript/jQuery y server‑side
- [ ] Listado de servicios en panel de Admin y Artista

## 📅 Reservas (Entidad Cita)

- [ ] CRUD de `reservas` en back‑end
- [ ] Formulario de reserva para Cliente (selección artista, servicio, fecha, obs.)
- [ ] Calendario de disponibilidad de artistas (Bootstrap calendar o plugin)
- [ ] Validación de conflictos de horario (AJAX request)
- [ ] Panel de Recepcionista para confirmar/rechazar reservas

## 🖥️ Front‑end UI

- [ ] Maquetación de layout principal con Bootstrap
- [ ] Navbar dinámica según rol logueado
- [ ] Páginas de dashboard por rol (Admin, Artista, Cliente, Recepcionista)
- [ ] Tablas responsivas de reservas y facturas
- [ ] Modal de detalles y generación de PDF
- [ ] Implementar AJAX para operaciones CRUD sin refrescar

## 📜 Generación de PDF

- [ ] Crear vistas HTML/CSS para factura y historial de reservas (`views/pdf/`)
- [ ] Integrar Dompdf en `PdfController.php`
- [ ] Método `generateInvoice($reservaId)`
- [ ] Método `generateHistory($clienteId)`
- [ ] Pruebas de calidad de PDF (fuentes, saltos de página)

## ✉️ Envío de Correos

- [ ] Configurar PHPMailer en `MailService.php`
- [ ] Método `sendReservationConfirmation($user, $pdfPath)`
- [ ] Método `sendReminder($user, $reservation)`
- [ ] Programar cron job para recordatorios 24 h antes
- [ ] Plantillas de correo (HTML/text) en `views/emails/`
- [ ] Pruebas de envío en entorno de desarrollo

## 🧪 Pruebas y Calidad

- [ ] Test de unidad para funciones críticas (PHPUnit)
- [ ] Test de integración de reservas y notificaciones
- [ ] Pruebas manuales de flujos por cada rol
- [ ] Validar seguridad: SQLi, XSS, CSRF tokens
- [ ] Revisar logs y manejar errores/graceful fallback

## 🚀 Despliegue

- [ ] Preparar script de despliegue (rsync, git hooks)
- [ ] Configurar entorno de producción (variables, SSL)
- [ ] Configurar backups automáticos de base de datos
- [ ] Documentar pasos de despliegue en `DEPLOY.md`

## 📖 Documentación

- [ ] Completar `README.md` con ejemplos de uso
- [ ] Documentar endpoints API (si aplica)
- [ ] Crear guía de estilo de código y convenciones
- [ ] Añadir diagramas (ER, casos de uso)

## 🔧 Mantenimiento y Extensiones

- [ ] Añadir logs de actividad de usuarios
- [ ] Panel de estadísticas (nº reservas, ingresos)
- [ ] Exportar datos CSV/Excel
- [ ] Internacionalización (i18n) de la interfaz
- [ ] Soporte de múltiples centros/sucursales
