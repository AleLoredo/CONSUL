<?php
// Incluir archivo de conexión a la base de datos
require_once 'db.php';
include('id_contacto_nombre.php'); // revisar si hay un ID de contacto y obtener su nombre y apellido en variables

if (!isset($_GET['bookid'])) {
    header("Location: dashboard.php");
    exit;
} else { 
    $bookid = $_GET['bookid'];

    // Consulta para obtener información de la reserva
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
    $stmt->execute([$bookid]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        header("Location: dashboard.php");
        exit;
    }

    // Inicializar variables
    $selected_date = $booking['date'];
    $selected_time = $booking['time'];
    $isoverbook = $booking['isoverbook'];

    // Formatear fecha y hora
    $formatted_date = ($selected_date !== 'No disponible') ? date('d-m-Y', strtotime($selected_date)) : 'No disponible';
    $formatted_time = ($selected_time !== 'No disponible') ? date('H:i', strtotime($selected_time)) : 'No disponible';

    // Estado de cancelación
    $booking_completed = false;
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_booking'])) {
    try {
        // Consulta simplificada para marcar la reserva como inactiva
        $stmt = $pdo->prepare("UPDATE bookings SET isactive = 0 WHERE id = ?");
        $stmt->execute([$bookid]);
        
        // Marcar como cancelada
        $booking_completed = true;
        
    } catch (PDOException $e) {
        $error_message = "Error al cancelar el turno. " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancelación de turno</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .data-card {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .data-label {
            font-weight: bold;
            color: #495057;
        }
        .data-value {
            font-size: 1.1rem;
            color: #212529;
        }
        .header-icon {
            background-color: #e9ecef;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
    </style>
</head>
<body>
    <!-- Toast container -->
    <div class="toast-container">
        <?php if($booking_completed): ?>
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong class="me-auto">Cancelación Exitosa</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                El turno ha sido cancelado correctamente.
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <?php if (isset($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <div class="d-flex align-items-center">
                            <div class="header-icon bg-white">
                                <i class="bi bi-calendar-x text-danger fs-4"></i>
                            </div>
                            <h3 class="mb-0">Cancelación de Turno</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?php if($booking_completed): ?>
                                Datos del turno cancelado.
                            <?php else: ?>
                                Está a punto de cancelar este turno. Esta acción no se puede deshacer.
                            <?php endif; ?>
                        </div>
                        
                        <div class="data-card">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="data-label">Paciente:</div>
                                </div>
                                <div class="col-md-8">
                                    <div class="data-value"><?php echo $contactlastname . ', ' . $contactname; ?></div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="data-label">Fecha:</div>
                                </div>
                                <div class="col-md-8">
                                    <div class="data-value">
                                        <i class="bi bi-calendar3 me-2 text-primary"></i>
                                        <?php echo $formatted_date; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="data-label">Hora:</div>
                                </div>
                                <div class="col-md-8">
                                    <div class="data-value">
                                        <i class="bi bi-clock me-2 text-primary"></i>
                                        <?php echo $formatted_time; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="data-label">Tipo de turno:</div>
                                </div>
                                <div class="col-md-8">
                                    <div class="data-value">
                                        <?php if($isoverbook == '1'): ?>
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i>Sobreturno
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle-fill me-1"></i>Normal
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Formulario simplificado para confirmar la cancelación -->
                        <form method="POST" action="">
                            <div class="d-flex justify-content-between mt-4">
                                <?php if($booking_completed): ?>
                                <a href="dashboard.php" class="btn btn-primary">
                                    <i class="bi bi-arrow-left me-2"></i>Volver al Inicio
                                </a>
                                <?php else: ?>
                                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Volver
                                </a>
                                <?php endif; ?>
                                
                                <?php if($booking_completed): ?>
                                <button type="button" class="btn btn-outline-secondary" disabled>
                                    <i class="bi bi-check-circle me-2"></i>Turno cancelado
                                </button>
                                <?php else: ?>
                                <button type="submit" name="confirm_booking" class="btn btn-danger">
                                    <i class="bi bi-x-circle me-2"></i>Confirmar cancelación
                                </button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script para el toast -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'));
            var toastList = toastElList.map(function(toastEl) {
                return new bootstrap.Toast(toastEl, {
                    autohide: true,
                    delay: 5000
                });
            });
            
            toastList.forEach(toast => toast.show());
        });
    </script>
</body>
</html>