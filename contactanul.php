<?php
// Incluir archivo de conexión a la base de datos
require_once 'db.php';

// Inicializar la variable contact_anulled
$contact_anulled = false;
$error_message = null;

include('id_contacto_nombre.php'); // Obtiene $contact_id, $contactname y $contactlastname

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_baja'])) { // Corregido el nombre del campo
    try {
        // Consulta simplificada para marcar al contacto como inactivo
        $stmt = $pdo->prepare("UPDATE contacts SET isactive = 0 WHERE id = ?");
        $stmt->execute([$contact_id]);
        
        // Marcar como cancelada
        $contact_anulled = true;
        
    } catch (PDOException $e) {
        $error_message = "Error al dar de baja al contacto " . $e->getMessage();
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
        <?php if($contact_anulled): ?>
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong class="me-auto">Baja Exitosa</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                El contacto ha sido dado de baja exitosamente.
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
                    <?php echo htmlspecialchars($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <div class="d-flex align-items-center">
                            <div class="header-icon bg-white">
                                <i class="bi bi-person-lines-fill text-danger fs-4"></i>
                            </div>
                            <h3 class="mb-0">Dar de baja al contacto</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?php if($contact_anulled): ?>
                                Datos del contacto dado de baja.
                            <?php else: ?>
                                Está a punto de dar de baja a este contacto. Esta acción no se puede deshacer.
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
                            
                            <!-- Se eliminan filas vacías -->
                        </div>
                        
                        <!-- Formulario simplificado para confirmar la cancelación -->
                        <form method="POST" action="">
                            <div class="d-flex justify-content-between mt-4">
                                <?php if($contact_anulled): ?>
                                <a href="dashboard.php" class="btn btn-primary">
                                    <i class="bi bi-arrow-left me-2"></i>Volver al Inicio
                                </a>
                                <?php else: ?>
                                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Volver
                                </a>
                                <?php endif; ?>
                                
                                <?php if($contact_anulled): ?>
                                <button type="button" class="btn btn-outline-secondary" disabled>
                                    <i class="bi bi-check-circle me-2"></i>Contacto dado de baja
                                </button>
                                <?php else: ?>
                                <button type="submit" name="confirm_baja" class="btn btn-danger">
                                    <i class="bi bi-x-circle me-2"></i>Confirmar dar de baja
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