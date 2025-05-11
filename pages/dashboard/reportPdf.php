<?php

require '../../vendor/autoload.php';

use Dompdf\Dompdf;

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

require_once __DIR__ . '/../../utils/auth.php';
require_once __DIR__ . '/../../services/reservas.php';
require_once __DIR__ . '/../../services/usuarios.php';
require_once __DIR__ . '/../../services/servicios.php';

// Obtener todas las reservas según el rol del usuario
if (isset($_SESSION['usuario_rol'])) {
  if ($_SESSION['usuario_rol'] === 'cliente') {
    $reservas = obtenerReservas(null, $_SESSION['usuario_id']);
  } else if ($_SESSION['usuario_rol'] === 'artista') {
    $reservas = obtenerReservas(null, null, $_SESSION['usuario_id']);
  } else {
    $reservas = obtenerReservas(); // Para admin u otros roles
  }
} else {
  // Si no hay sesión, redirigir al login
  header('Location: ../../pages/login.php');
  exit;
}

ob_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reporte de Reservas</title>
  <style>
    body {
      font-family: Arial, sans-serif;
    }

    .container-fluid {
      width: 100%;
      padding: 15px;
    }

    h2 {
      background-color: #343a40;
      color: white;
      padding: 10px;
      border-radius: 5px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th {
      background-color: #343a40;
      color: white;
      text-align: left;
      padding: 8px;
    }

    td {
      border: 1px solid #ddd;
      padding: 8px;
    }

    tr:nth-child(even) {
      background-color: #f2f2f2;
    }

    .badge {
      padding: 5px 10px;
      border-radius: 4px;
      font-size: 12px;
    }

    .bg-success {
      background-color: #28a745;
      color: white;
    }

    .bg-warning {
      background-color: #ffc107;
      color: black;
    }

    .bg-danger {
      background-color: #dc3545;
      color: white;
    }

    .bg-info {
      background-color: #17a2b8;
      color: white;
    }

    .bg-secondary {
      background-color: #6c757d;
      color: white;
    }
  </style>
</head>

<body>
  <div class="container-fluid">
    <h2>Reporte de Reservas</h2>


    <div class="card shadow mt-4">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover table-light">
            <thead class="thead-dark">
              <tr>
                <th>Cliente</th>
                <th>Artista</th>
                <th>Servicio</th>
                <th>Fecha y Hora</th>
                <th>Estado</th>
                <th>Observaciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($reservas as $reserva): ?>
                <tr>
                  <td><?= htmlspecialchars($reserva['cliente_nombre'] . ' ' . $reserva['cliente_apellido']) ?></td>
                  <td><?= htmlspecialchars($reserva['artista_nombre'] . ' ' . $reserva['artista_apellido']) ?></td>
                  <td><?= htmlspecialchars($reserva['servicio_nombre']) ?> - <?= htmlspecialchars($reserva['servicio_precio']) ?>€</td>
                  <td><?= date('d/m/Y H:i', strtotime($reserva['fecha_hora'])) ?></td>
                  <td><span class="badge bg-<?= getBadgeClass($reserva['estado']) ?>"><?= htmlspecialchars($reserva['estado']) ?></span></td>
                  <td><?= htmlspecialchars($reserva['observaciones'] ?? 'N/A') ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</body>

</html>

<?php
// Función para determinar la clase de badge según el estado
function getBadgeClass($estado)
{
  switch (strtolower($estado)) {
    case 'pendiente':
      return 'warning';
    case 'confirmada':
      return 'success';
    case 'cancelada':
      return 'danger';
    case 'completada':
      return 'info';
    default:
      return 'secondary';
  }
}

$html = ob_get_clean();
//echo $html;

// instantiate and use the dompdf class
$dompdf = new Dompdf();
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream("Reservas.pdf", array("Attachment" => true));

?>