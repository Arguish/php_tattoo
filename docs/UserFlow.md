# UserFlow.md

Este documento describe los flujos de usuario (user flows) para cada uno de los roles del sistema: Administrador, Artista, Cliente y Recepcionista. Se incluyen diagramas en Mermaid para visualizar el recorrido de cada actor.

---

## 1. Administrador

### Flujos principales

1. **Autenticación**
2. **Gestión de usuarios** (CRUD)
3. **Gestión de servicios** (CRUD)
4. **Generación de reportes PDF**
5. **Visualización de estadísticas**

```mermaid
flowchart TD
    A[Inicio de sesión] --> B[Dashboard Admin]
    B --> C[Gestionar Usuarios]
    C --> C1[Crear Usuario]
    C --> C2[Editar Usuario]
    C --> C3[Eliminar Usuario]
    B --> D[Gestionar Servicios]
    D --> D1[Crear Servicio]
    D --> D2[Editar Servicio]
    D --> D3[Eliminar Servicio]
    B --> E[Generar Reporte PDF]
    E --> E1[Seleccionar rango de fechas]
    E --> E2[Descargar PDF]
    B --> F[Ver Estadísticas]
    F --> F1[Gráficos ingresos]
    F --> F2[Número de reservas]
```

---

## 2. Artista

### Flujos principales

1. **Login**
2. **Ver calendario de reservas**
3. **Actualizar estado de reserva y observaciones**
4. **Generar historial de trabajo (PDF)**

```mermaid
flowchart TD
    A[Login Artista] --> B[Dashboard Artista]
    B --> C[Ver Calendario de Reservas]
    C --> C1[Seleccionar Reserva]
    C1 --> D[Agregar Observaciones]
    D --> E[Guardar y notificar]
    B --> F[Generar Historial PDF]
    F --> F1[Elegir período]
    F1 --> F2[Descargar PDF]
```

---

## 3. Cliente

### Flujos principales

1. **Registro / Login**
2. **Seleccionar servicio y artista**
3. **Reservar cita**
4. **Recibir confirmación y PDF**
5. **Ver historial de reservas**

```mermaid
flowchart TD
    A[Registro/Login Cliente] --> B[Dashboard Cliente]
    B --> C[Ver Servicios Disponibles]
    C --> C1[Seleccionar Servicio]
    C1 --> D[Seleccionar Artista y Fecha]
    D --> E[Enviar Solicitud de Reserva]
    E --> F[Mostrar estado Pendiente]
    F --> G[Recibir Email Confirmación]
    G --> G1[Adjunto PDF Factura]
    B --> H[Historial de Reservas]
    H --> H1[Ver Detalle]
    H1 --> H2[Descargar PDF]
```

---

## 4. Recepcionista

### Flujos principales

1. **Login**
2. **Ver reservas pendientes**
3. **Confirmar/Rechazar reserva**
4. **Enviar recordatorio 24h antes**

```mermaid
flowchart TD
    A[Login Recepcionista] --> B[Dashboard Recepcionista]
    B --> C[Ver Reservas Pendientes]
    C --> C1[Seleccionar Reserva]
    C1 --> D{Decisión}
    D -->|Confirmar| E[Generar Factura PDF]
    E --> F[Enviar Email Confirmación]
    D -->|Rechazar| G[Enviar Email Rechazo]
    B --> H[Programar Recordatorios]
    H --> H1[Cron Job 24h antes]
    H1 --> H2[Enviar Email Recordatorio]
```
