<?php
// Incluir el archivo de verificación de sesión
include('session.php');
?>

<?php
include('db.php');

// Obtener los registros de la tabla insurance_providers
$query = "SELECT id, insurname FROM insurance_providers";
$stmt = $pdo->query($query);
$insuranceProviders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_SESSION['form_submitted'])) {
    // Obtener los valores del formulario
    $name = trim($_POST['name']);
    $lastname = trim($_POST['lastname']);
    
    // Verificar que los campos obligatorios no estén vacíos
    if (empty($name) || empty($lastname)) {
        $error = "Los campos Nombre y Apellido son obligatorios";
    } else {
        // Obtener el resto de valores del formulario
        $email = !empty($_POST['email']) ? trim($_POST['email']) : null;
        $doctype = !empty($_POST['doctype']) ? trim($_POST['doctype']) : null;
        $docnum = !empty($_POST['docnum']) ? trim($_POST['docnum']) : null;
        $phone1 = !empty($_POST['phone1']) ? trim($_POST['phone1']) : null;
        $phone2 = !empty($_POST['phone2']) ? trim($_POST['phone2']) : null;
        $insurid = !empty($_POST['insurid']) ? $_POST['insurid'] : null;

        try {
            // Insertar los datos en la tabla contacts
            $insertQuery = "INSERT INTO contacts (name, lastname, email, doctype, docnum, phone1, phone2, insurid) 
                            VALUES (:name, :lastname, :email, :doctype, :docnum, :phone1, :phone2, :insurid)";
            $stmt = $pdo->prepare($insertQuery);
            
            // Bind the parameters
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':lastname', $lastname);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':doctype', $doctype, PDO::PARAM_STR);
            $stmt->bindParam(':docnum', $docnum, PDO::PARAM_STR);
            $stmt->bindParam(':phone1', $phone1, PDO::PARAM_STR);
            $stmt->bindParam(':phone2', $phone2, PDO::PARAM_STR);
            $stmt->bindParam(':insurid', $insurid, PDO::PARAM_INT);
            
            // Execute the query
            $success = $stmt->execute();
            
            if ($success) {
                // Marcar como enviado y redirigir para evitar reinserción
                $_SESSION['form_submitted'] = true;
                header("Location: newcontact.php?success=1");
                exit;
            } else {
                $error = "Error al guardar los datos: " . implode(", ", $stmt->errorInfo());
            }
        } catch (PDOException $e) {
            $error = "Error de base de datos: " . $e->getMessage();
        }
    }
}

// Limpiar la marca de formulario enviado después de mostrar el mensaje de éxito
if (isset($_GET['success']) && $_GET['success'] == '1') {
    unset($_SESSION['form_submitted']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo paciente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .sidebar {
            background: #f4f0ed;
            height: 100vh;
            padding: 20px;
        }
        .edit-container {
            padding: 20px;
            border-bottom: 2px solid #6c63ff;
        }
        .btn-save {
            background: #6c63ff;
            color: white;
        }
        .btn-cancel {
            background: #8b0000;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-2 sidebar d-flex flex-column">
            <h5><strong>User: <?php echo htmlspecialchars($_SESSION['username']); ?></strong></h5>
            <a href="logout.php" class="text-primary">Cerrar sesión</a>
                <button class="btn btn-primary mt-3" onclick="location.href='dashboard.php'">Búsqueda</button>
                <button class="btn btn-primary mt-2" onclick="location.href='newcontact.php'">Nuevo paciente</button>
                <button class="btn btn-primary mt-2" onclick="location.href='viewbookings.php'">Ver agenda</button>
            </div>
            <div class="col-md-10 p-4">
                <div class="edit-container border rounded p-3">
                    <h4><i class="bi bi-person"></i> NUEVO PACIENTE</h4>

                    <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                    <?php endif; ?>

                    <div class="row g-2 mt-2 mx-4">
                        
                            <form action="newcontact.php" method="POST">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="lastname" class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="lastname" name="lastname" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="email" name="email">
                                </div>
                                <div class="mb-3">
                                    <label for="doctype" class="form-label">Tipo</label>
                                    <select class="form-select" id="doctype" name="doctype">
                                        <option value="" selected>Seleccione un tipo</option>
                                        <option value="DNI">DNI</option>
                                        <option value="LE">LE</option>
                                        <option value="PASAPORTE">PASAPORTE</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="docnum" class="form-label">Documento</label>
                                    <input type="text" class="form-control" id="docnum" name="docnum">
                                </div>
                                <div class="mb-3">
                                    <label for="phone1" class="form-label">Teléfono 1</label>
                                    <input type="text" class="form-control" id="phone1" name="phone1" placeholder="Válido como # de WHATSAPP. Ingresar con codigo pais sin el + | Ej: 541134447777)">
                                </div>
                                <div class="mb-3">
                                    <label for="phone2" class="form-label">Teléfono 2</label>
                                    <input type="text" class="form-control" id="phone2" name="phone2">
                                </div>
                                <div class="mb-3">
                                    <label for="insurid" class="form-label">Cobertura</label>
                                    <select class="form-select" id="insurid" name="insurid">
                                        <!-- <option value="" selected>Seleccione una cobertura</option> -->
                                        <option value="4" selected>Sin info</option>
                                  
                                        <?php
                                        foreach ($insuranceProviders as $provider) {
                                            echo "<option value='" . $provider['id'] . "'>" . htmlspecialchars($provider['insurname']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </form>    
                       
                    </div>

                </div>
            </div>
        </div>
                                
<!-- Modal de éxito -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="successModalLabel">Éxito</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        El contacto ha sido agregado exitosamente.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

    </div>

    <script>
    // Mostrar el modal si en la URL hay ?success=1
    document.addEventListener("DOMContentLoaded", function () {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
            // Remover el parámetro de la URL sin recargar la página
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });
</script>

</body>
</html>