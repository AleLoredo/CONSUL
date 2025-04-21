<?php
include('db.php');
include('session.php');

if (isset($_POST['id'])) {
    // Proceso de actualización de datos
    $contact_id = $_POST['id'];
    $name = $_POST['name'];
    $lastname = $_POST['lastname'];
    $doctype = $_POST['doctype'];
    $docnum = $_POST['docnum'];
    $phone1 = $_POST['phone1'];
    $phone2 = $_POST['phone2'];
    $email = $_POST['email'];
    $insurid = $_POST['insurid'];

    try {
        $query = "UPDATE contacts SET name = ?, lastname = ?, doctype = ?, docnum = ?, phone1 = ?, phone2 = ?, email = ?, insurid = ? WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$name, $lastname, $doctype, $docnum, $phone1, $phone2, $email, $insurid, $contact_id]);

        // Redirigir a edit.php con ID y success=1
        header("Location: edit.php?id=$contact_id&success=1");
        exit;
    } catch (PDOException $e) {
        die("Error en la actualización: " . $e->getMessage());
    }
}

// Obtener los datos del contacto si hay un ID en la URL
if (isset($_GET['id'])) {
    $contact_id = $_GET['id'];

    try {
        $query = "SELECT * FROM contacts WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$contact_id]);
        $contact = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$contact) {
            die("Contacto no encontrado.");
        }
    } catch (PDOException $e) {
        die("Error en la consulta: " . $e->getMessage());
    }
} else {
    header("Location: dashboard.php");
    exit;
}

// Obtener registros de la tabla insurance_providers
$query = "SELECT id, insurname FROM insurance_providers";
$stmt = $pdo->query($query);
$insuranceProviders = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar paciente</title>
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
            <div class="container mt-4 border p-3">
                <h4 class="mb-3">Editar Contacto</h4>
                <form action="edit.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">



                <div class="mb-3 row align-items-center">
                    <div class="col-auto">
                        <label for="name" class="col-form-label">Nombre</label>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" id="name" name="name" 
                            value="<?php echo htmlspecialchars($contact['name']); ?>" required>
                    </div>
                    <div class="col-auto">
                        <label for="lastname" class="col-form-label">Apellido</label>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" id="lastname" name="lastname" 
                            value="<?php echo htmlspecialchars($contact['lastname']); ?>" required>
                    </div>
                </div>


                <div class="mb-3 row align-items-center">
                    <div class="col-auto">
                        <label for="doctype" class="col-form-label">Tipo</label>
                    </div>
                    <div class="col">
                        <select class="form-select" id="doctype" name="doctype" required>
                            <option value="DNI" <?php echo ($contact['doctype'] == 'DNI') ? 'selected' : ''; ?>>DNI</option>
                            <option value="LE" <?php echo ($contact['doctype'] == 'LE') ? 'selected' : ''; ?>>LE</option>
                            <option value="PASAPORTE" <?php echo ($contact['doctype'] == 'PASAPORTE') ? 'selected' : ''; ?>>PASAPORTE</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <label for="docnum" class="col-form-label">Documento</label>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" id="docnum" name="docnum" 
                            value="<?php echo htmlspecialchars($contact['docnum']); ?>" required>
                    </div>
                </div>


                <div class="mb-3 row align-items-center">
                    <div class="col-auto">
                        <label for="phone1" class="col-form-label">Teléfono 1</label>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" id="phone1" name="phone1" 
                            value="<?php echo htmlspecialchars($contact['phone1']); ?>" required>
                    </div>
                    <div class="col-auto">
                        <label for="phone2" class="col-form-label">Teléfono 2</label>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" id="phone2" name="phone2" 
                            value="<?php echo htmlspecialchars($contact['phone2']); ?>">
                    </div>
                </div>

                <div class="mb-3 row align-items-center">
                    <div class="col-auto">
                        <label for="email" class="col-form-label">Correo Electrónico</label>
                    </div>
                    <div class="col">
                        <input type="email" class="form-control" id="email" name="email" 
                            value="<?php echo htmlspecialchars($contact['email']); ?>" required>
                    </div>
                    <div class="col-auto">
                        <label for="insurid" class="col-form-label">Cobertura</label>
                    </div>
                    <div class="col">
                        <select class="form-select" id="insurid" name="insurid" required>
                            <option value="" disabled>Seleccione una cobertura</option>
                            <?php
                            foreach ($insuranceProviders as $provider) {
                                $selected = ($provider['id'] == $contact['insurid']) ? 'selected' : '';
                                echo "<option value='" . $provider['id'] . "' $selected>" . htmlspecialchars($provider['insurname']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>



                    
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
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
        El contacto ha sido actualizado exitosamente.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });
</script>


</body>
</html>
