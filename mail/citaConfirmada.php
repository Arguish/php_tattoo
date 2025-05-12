<?php
// Modelo para enviar correo de confirmación de reserva
// Este archivo recibe parámetros externos para personalizar el correo

// Parámetros que debe recibir:
// $nombreCliente - Nombre del cliente
// $fechaHora - Fecha y hora de la cita
// $nombreArtista - Nombre del artista asignado
// $servicio - Servicio solicitado
// $observaciones - Observaciones adicionales (opcional)
// $direccionEstudio - Dirección del estudio (opcional)
// $telefonoEstudio - Teléfono de contacto (opcional)

function generarEmailCitaConfirmada($nombreCliente, $fechaHora, $nombreArtista, $servicio, $observaciones = '', $direccionEstudio = 'Calle Principal 123, Ciudad', $telefonoEstudio = '123-456-7890')
{
  // Formatear fecha para mostrar de manera amigable
  $fechaFormateada = date('d/m/Y', strtotime($fechaHora));
  $horaFormateada = date('H:i', strtotime($fechaHora));

  // Generar el contenido HTML del correo
  $htmlContent = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Cita - Tattoo Studio</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #222;
            color: #fff;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border-left: 1px solid #ddd;
            border-right: 1px solid #ddd;
        }
        .footer {
            background-color: #222;
            color: #fff;
            padding: 15px;
            text-align: center;
            font-size: 0.8em;
            border-radius: 0 0 5px 5px;
        }
        h1 {
            color: #fff;
            margin: 0;
        }
        h2 {
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .details {
            background-color: #fff;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .details p {
            margin: 5px 0;
        }
        .details strong {
            color: #222;
        }
        .instructions {
            background-color: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .instructions h3 {
            margin-top: 0;
            color: #222;
        }
        .contact {
            margin-top: 20px;
            text-align: center;
        }
        @media only screen and (max-width: 480px) {
            body {
                padding: 10px;
            }
            .header, .content, .footer {
                padding: 15px 10px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Tattoo Studio</h1>
        <p>Tu cita ha sido confirmada</p>
    </div>
    
    <div class="content">
        <h2>Hola, {$nombreCliente}!</h2>
        <p>Nos complace confirmar tu cita para un tatuaje en nuestro estudio.</p>
        
        <div class="details">
            <p><strong>Fecha:</strong> {$fechaFormateada}</p>
            <p><strong>Hora:</strong> {$horaFormateada}</p>
            <p><strong>Artista:</strong> {$nombreArtista}</p>
            <p><strong>Servicio:</strong> {$servicio}</p>
            <p><strong>Observaciones:</strong> {$observaciones}</p>
        </div>
        
        <div class="instructions">
            <h3>Recomendaciones previas:</h3>
            <ul>
                <li>Descansa bien la noche anterior</li>
                <li>Come algo antes de venir</li>
                <li>Hidrátate adecuadamente</li>
                <li>No consumas alcohol 24 horas antes</li>
                <li>Llega 15 minutos antes de tu cita</li>
            </ul>
        </div>
        
        <div class="contact">
            <p>Si necesitas cambiar o cancelar tu cita, contáctanos con al menos 24 horas de anticipación.</p>
            <p><strong>Dirección:</strong> {$direccionEstudio}</p>
            <p><strong>Teléfono:</strong> {$telefonoEstudio}</p>
        </div>
    </div>
    
    <div class="footer">
        <p>© 2023 Tattoo Studio. Todos los derechos reservados.</p>
        <p>Este correo fue enviado automáticamente, por favor no responder.</p>
    </div>
</body>
</html>
HTML;

  return $htmlContent;
}

// Ejemplo de uso (solo para pruebas):
// Si se llama directamente a este archivo, muestra un ejemplo
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
  // Datos de ejemplo
  $nombreCliente = "Carlos Rodríguez";
  $fechaHora = "2023-12-15 14:30:00";
  $nombreArtista = "Laura Martínez";
  $servicio = "Tatuaje Tribal Mediano";
  $observaciones = "Cliente prefiere tinta negra. Primera sesión.";

  // Generar y mostrar el correo de ejemplo
  echo generarEmailCitaConfirmada($nombreCliente, $fechaHora, $nombreArtista, $servicio, $observaciones);
}
