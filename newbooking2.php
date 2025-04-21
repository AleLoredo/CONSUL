<?php
// Incluir archivo de conexión a la base de datos
require_once 'db.php';
include('id_contacto_nombre.php'); // revisar si hay un ID de contacto y obtener su nombre y apellido en variables


// Inicializar variables
$id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : 'No disponible';
$selected_date = isset($_POST['selected_date']) ? htmlspecialchars($_POST['selected_date']) : 'No disponible';
$selected_time = isset($_POST['selected_time']) ? htmlspecialchars($_POST['selected_time']) : 'No disponible';
$isoverbook = isset($_POST['isoverbook']) ? htmlspecialchars($_POST['isoverbook']) : 'No disponible';

// Formatear fecha para mostrar
$formatted_date = ($selected_date !== 'No disponible') ? date('d-m-Y', strtotime($selected_date)) : 'No disponible';

// Formatear hora para mostrar
$formatted_time = ($selected_time !== 'No disponible') ? date('H:i', strtotime($selected_time)) : 'No disponible';

// Variable para controlar si la reserva fue realizada
$booking_completed = false;

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_booking'])) {
    try {
        // Recoger datos adicionales del formulario
        $observations = isset($_POST['observations']) ? htmlspecialchars($_POST['observations']) : '';
        $isfirsttime = isset($_POST['isfirsttime']) ? 1 : 0;
        
        // Preparar la consulta SQL para insertar en la tabla bookings
        $query = "INSERT INTO bookings (contact, date, time, isoverbook, isfirsttime, observations) 
                  VALUES (:contact, :date, :time, :isoverbook, :isfirsttime, :observations)";
        
        $stmt = $pdo->prepare($query);
        
        // Bind de parámetros
        $stmt->bindParam(':contact', $id);
        $stmt->bindParam(':date', $selected_date);
        $stmt->bindParam(':time', $selected_time);
        $stmt->bindParam(':isoverbook', $isoverbook);
        $stmt->bindParam(':isfirsttime', $isfirsttime);
        $stmt->bindParam(':observations', $observations);
        
        // Ejecutar la consulta
        $stmt->execute();
        
        // Obtener el ID de la reserva creada
        $booking_id = $pdo->lastInsertId();
        
        // Indicar que la reserva se completó exitosamente
        $booking_completed = true;
        
        // Ya no redirigimos, ahora mostramos el toast en la misma página
        // header("Location: booking_confirmation.php?id=" . $booking_id);
        // exit;
        
    } catch (PDOException $e) {
        // Manejar error de base de datos
        $error_message = "Error al crear la reserva: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Reserva</title>
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
        .form-check-input:checked {
            background-color: #198754;
            border-color: #198754;
        }
        /* Estilo para el Toast */
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
                <strong class="me-auto">Reserva Exitosa</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                El turno ha sido agendado correctamente.
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
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex align-items-center">
                            <div class="header-icon bg-white">
                                <i class="bi bi-calendar-check text-primary fs-4"></i>
                            </div>
                            <h3 class="mb-0">Detalles de la Reserva</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            Los siguientes datos han sido recibidos correctamente.
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
                                    <div class="data-label">Fecha Seleccionada:</div>
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
                                    <div class="data-label">Hora Seleccionada:</div>
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
                        
                        <!-- Formulario para confirmar la reserva -->
                        <form method="POST" action="">
                            <!-- Información adicional -->
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="isfirsttime" name="isfirsttime" <?php echo $booking_completed ? 'disabled' : 'checked'; ?>>
                                    <label class="form-check-label" for="isfirsttime">Primera Consulta</label>
                                </div>
                            </div>
                            
                            <!-- Campo de observaciones -->
                            <div class="mb-3">
                                <label for="observations" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observations" name="observations" rows="3" placeholder="Ingrese cualquier observación relevante para esta reserva..." <?php echo $booking_completed ? 'disabled' : ''; ?>></textarea>
                            </div>
                            
                            <!-- Pasar los datos recibidos como campos ocultos -->
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <input type="hidden" name="selected_date" value="<?php echo $selected_date; ?>">
                            <input type="hidden" name="selected_time" value="<?php echo $selected_time; ?>">
                            <input type="hidden" name="isoverbook" value="<?php echo $isoverbook; ?>">
                            
                            <div class="d-flex justify-content-between mt-4">
                            <?php if($booking_completed): ?>
                                <a href="dashboard.php" class="btn btn-primary">
                                    <i class="bi bi-arrow-left me-2"></i>Inicio
                                </a>
                                <?php else: ?>
                                    <a href="javascript:history.back()" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Volver
                                </a>
                                <?php endif; ?>
                                
                                
                                <?php if($booking_completed): ?>
                                <button type="button" class="btn btn-outline-secondary" disabled>
                                    <i class="bi bi-check-circle me-2"></i>Turno agendado
                                </button>
                                <?php else: ?>
                                <button type="submit" name="confirm_booking" class="btn btn-success">
                                    <i class="bi bi-check-circle me-2"></i>Confirmar Reserva
                                </button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Datos recibidos (Debug) -->
                <div class="card mt-4 d-none">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Información Técnica (Debug)</h5>
                    </div>
                    <div class="card-body">
                        <h6>Datos GET:</h6>
                        <pre><?php print_r($_GET); ?></pre>
                        
                        <h6>Datos POST:</h6>
                        <pre><?php print_r($_POST); ?></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script para controlar la duración del toast -->
    <script>
        // Inicializar todos los toasts
        document.addEventListener('DOMContentLoaded', function() {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'));
            var toastList = toastElList.map(function(toastEl) {
                return new bootstrap.Toast(toastEl, {
                    autohide: true,
                    delay: 5000
                });
            });
            
            // Mostrar toasts automáticamente
            toastList.forEach(toast => toast.show());
        });
    </script>
</body>
</html>