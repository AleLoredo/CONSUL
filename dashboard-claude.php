<?php
// Incluir el archivo de verificación de sesión y conexión a la base de datos
include('session.php');
include('db.php'); // Asegúrate de tener un archivo db.php con la conexión a la base de datos

$search_results = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $query = "SELECT c.id, c.lastname, c.name, c.doctype, c.docnum, c.phone1, c.phone2, c.email, i.insurname 
                  FROM contacts c 
                  LEFT JOIN insurance_providers i ON c.insurid = i.id 
                  WHERE 1=1";
        $params = [];

        if (!empty($_POST['doctype']) && !empty($_POST['docnum'])) {
            $query .= " AND c.doctype = ? AND c.docnum = ?";
            array_push($params, $_POST['doctype'], $_POST['docnum']);
        }
        if (!empty($_POST['name']) && !empty($_POST['lastname'])) {
            $query .= " AND c.name LIKE ? AND c.lastname LIKE ?";
            array_push($params, "%" . $_POST['name'] . "%", "%" . $_POST['lastname'] . "%");
        }
        if (!empty($_POST['email'])) {
            $query .= " AND c.email LIKE ?";
            array_push($params, "%" . $_POST['email'] . "%");
        }
        if (!empty($_POST['phone'])) {
            $query .= " AND c.phone1 LIKE ?";
            array_push($params, "%" . $_POST['phone'] . "%");
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($results) {
            foreach ($results as $row) {
                $search_results .= "<div class='card mb-3 shadow-sm'>
                    <div class='card-body'>
                        <h5 class='card-title'>{$row['lastname']}, {$row['name']}</h5>
                        <p class='card-text mb-1'>
                            <i class='bi bi-card-text text-secondary me-2'></i> {$row['doctype']} {$row['docnum']}
                        </p>
                        <p class='card-text mb-1'>
                            <i class='bi bi-telephone text-secondary me-2'></i> {$row['phone1']}
                        </p>";
                
                if (!empty($row['phone2'])) {
                    $search_results .= "<p class='card-text mb-1'>
                        <i class='bi bi-phone text-secondary me-2'></i> {$row['phone2']}
                    </p>";
                }
                
                $search_results .= "<p class='card-text mb-1'>
                            <i class='bi bi-envelope text-secondary me-2'></i> {$row['email']}
                        </p>
                        <p class='card-text mb-3 fw-bold text-primary'>
                            <i class='bi bi-shield-check me-2'></i> {$row['insurname']}
                        </p>
                        <div class='btn-group w-100'>
                            <button class='btn btn-outline-primary' onclick=\"location.href='edit.php?id={$row['id']}'\">
                                <i class='bi bi-person-lines-fill'></i>
                            </button>
                            <button class='btn btn-outline-success' onclick=\"window.open('https://wa.me/+{$row['phone1']}', '_blank')\">
                                <i class='bi bi-whatsapp'></i>
                            </button>
                            <button class='btn btn-outline-primary' onclick=\"location.href='newbooking.php?id={$row['id']}'\">
                                <i class='bi bi-calendar'></i>
                            </button>
                            <button class='btn btn-outline-primary' onclick=\"location.href='contactbookings.php?id={$row['id']}'\">
                                <i class='bi bi-check-circle'></i>
                            </button>
                        </div>
                    </div>
                </div>";
            }
        } else {
            $search_results = "<div class='alert alert-warning'>
                <i class='bi bi-exclamation-triangle-fill me-2'></i>
                No se encontraron resultados.
            </div>";
        }
    } catch (PDOException $e) {
        die("Error en la consulta: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Search</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 bg-white shadow-sm p-3 vh-100">
                <div class="mb-4 pb-3 border-bottom">
                    <h5 class="fw-bold mb-1">User: <?php echo htmlspecialchars($_SESSION['username']); ?></h5>
                    <a href="logout.php" class="text-primary text-decoration-none">
                        <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                    </a>
                </div>
                
                <div class="d-grid gap-2">
                    <button class="btn btn-primary text-start" onclick="location.href='dashboard.php'">
                        <i class="bi bi-search me-2"></i> Búsqueda
                    </button>
                    <button class="btn btn-outline-primary text-start" onclick="location.href='newcontact.php'">
                        <i class="bi bi-person-plus me-2"></i> Nuevo paciente
                    </button>
                    <button class="btn btn-outline-primary text-start" onclick="location.href='viewbookings.php'">
                        <i class="bi bi-calendar-week me-2"></i> Ver agenda
                    </button>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 text-primary">
                            <i class="bi bi-search me-2"></i> BÚSQUEDA DE PACIENTES
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Document Search Form -->
                        <form action="dashboard.php" method="POST" class="mb-3">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="input-group">
                                        <select class="form-select" name="doctype">
                                            <option value="" disabled selected>Tipo de documento</option>
                                            <option value="DNI">DNI</option>
                                            <option value="LE">LE</option>
                                            <option value="PASAPORTE">PASAPORTE</option>
                                        </select>
                                        <input type="text" class="form-control" name="docnum" placeholder="Número de documento">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                        <!-- Name Search Form -->
                        <form action="dashboard.php" method="POST" class="mb-3">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="name" placeholder="Nombre" required>
                                        <input type="text" class="form-control" name="lastname" placeholder="Apellido" required>
                                        <button class="btn btn-primary" type="submit">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                        <!-- Email Search Form -->
                        <form action="dashboard.php" method="POST" class="mb-3">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-envelope"></i>
                                        </span>
                                        <input type="text" class="form-control" name="email" placeholder="E-mail" required>
                                        <button class="btn btn-primary" type="submit">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                        <!-- Phone Search Form -->
                        <form action="dashboard.php" method="POST">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-telephone"></i>
                                        </span>
                                        <input type="text" class="form-control" name="phone" placeholder="Teléfono" required>
                                        <button class="btn btn-primary" type="submit">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Search Results -->
                <div id="searchresult" class="mt-3">
                    <?php echo $search_results; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>