<?php
// Incluir el archivo de verificación de sesión y conexión a la base de datos
include('session.php');
include('db.php'); // Asegúrate de tener un archivo db.php con la conexión a la base de datos

$search_results = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $query = "SELECT c.id, c.lastname, c.name, c.doctype, c.docnum, c.phone1, c.phone2, c.email, c.isactive, i.insurname 
                  FROM contacts c 
                  LEFT JOIN insurance_providers i ON c.insurid = i.id 
                  WHERE 1=1";
        $params = [];

        if (!empty($_POST['doctype']) && !empty($_POST['docnum'])) {
            $query .= " AND c.doctype = ? AND c.docnum = ?";
            array_push($params, $_POST['doctype'], $_POST['docnum']);
        }
        if (!empty($_POST['lastname'])) {
            $query .= " AND c.lastname LIKE ?";
            array_push($params, "%" . $_POST['lastname'] . "%");
        }
        // busqueda por doble condicions
        // if (!empty($_POST['name']) && !empty($_POST['lastname'])) {
        //     $query .= " AND c.name LIKE ? AND c.lastname LIKE ?";
        //     array_push($params, "%" . $_POST['name'] . "%", "%" . $_POST['lastname'] . "%");
        // }
        if (!empty($_POST['email'])) {
            $query .= " AND c.email LIKE ?";
            array_push($params, "%" . $_POST['email'] . "%");
        }
        if (!empty($_POST['phone'])) {
            $query .= " AND c.phone1 LIKE ? or c.phone2 LIKE ?";
            array_push($params, "%" . $_POST['phone'] . "%", "%" . $_POST['phone'] . "%");
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($results) {
            foreach ($results as $row) {
                if ($row['isactive'] != 0) {                                    
                $search_results .= "<div class='patient-card'>
                    <strong>{$row['lastname']}, {$row['name']}</strong><br>
                    <span>{$row['doctype']} {$row['docnum']}</span><br>
                    <span>{$row['phone1']}</span><br>
                    <span>{$row['phone2']}</span><br>
                    <span>{$row['email']}</span><br>
                    <span><strong>{$row['insurname']}</strong></span>
                    <div class='mt-2 d-flex gap-2'>

                        <button class='btn btn-outline-primary flex-grow-1' onclick=\"location.href='edit.php?id={$row['id']}'\">
                            <i class='bi bi-person-lines-fill'></i>
                        </button>

                        <button class='btn btn-outline-success flex-grow-1' onclick=\"window.open('https://wa.me/+{$row['phone1']}', '_blank')\">
                            <i class='bi bi-whatsapp'></i>
                        </button>
                        <button class='btn btn-outline-primary flex-grow-1' onclick=\"location.href='newbooking.php?id={$row['id']}'\">
                            <i class='bi bi-calendar'></i>
                        </button>
                        <button class='btn btn-outline-primary flex-grow-1' onclick=\"location.href='contactbookings.php?id={$row['id']}'\">
                            <i class='bi bi-check-circle'></i>
                        </button>
                    </div>
                </div>";
                }

            }
        } else {
            $search_results = "<div class='alert alert-warning'>No se encontraron resultados.</div>";
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
    <style>
        .sidebar {
            background: #f4f0ed;
            height: 100vh;
            padding: 20px;
        }
        .search-container {
            padding: 20px;
            border-bottom: 2px solid #6c63ff;
        }
        .btn-search {
            background: #000000;
            color: white;
        }
        .patient-card {
            border: 2px solid #6c63ff;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
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
                <div class="search-container border rounded p-3">
                    <h4><i class="bi bi-search"></i> BÚSQUEDA DE PACIENTES</h4>
                    <form action="dashboard.php" method="POST">
                        <div class="row d-flex">
                            <div class="col d-flex align-items-center">
                                <select class="form-select me-2" name="doctype" required>
                                    <option value="" disabled selected>Tipo de documento</option>
                                    <option value="DNI">DNI</option>
                                    <option value="LE">LE</option>
                                    <option value="PASAPORTE">PASAPORTE</option>
                                </select>
                                <input type="text" class="form-control me-2" name="docnum" placeholder="Documento">
                                <button class="btn btn-search" type="submit">▶</button>
                            </div>
                        </div>
                    </form>



                    <form action="dashboard.php" method="POST">
                        <div class="row d-flex mt-4 gy-3 ">
                            <div class="col d-flex align-items-center">
                                <!-- disabled search by name
                                 <input type="text" class="form-control me-2" name="name" placeholder="Nombre" required>    -->
                                <input type="text" class="form-control me-2" name="lastname" placeholder="Apellido" required>
                                <button class="btn btn-search" type="submit">▶</button>
                            </div>
                        </div>
                    </form>




                    
                    <form action="dashboard.php" method="POST">
                        <div class="row d-flex mt-4 gy-3 w-50">
                            <div class="col d-flex align-items-center">                                
                                <input type="text" name="email" class="form-control me-2" placeholder="E-mail" required>
                                <button class="btn btn-search" type="submit">▶</button>
                            </div>
                        </div>
                    </form>


                    <form action="dashboard.php" method="POST">
                        <div class="row d-flex mt-4 gy-3 w-50">
                            <div class="col d-flex align-items-center">                                
                                <input type="text" name="phone" class="form-control me-2" placeholder="Teléfono" required>
                                <button class="btn btn-search" type="submit">▶</button>
                            </div>
                        </div>
                    </form>


                </div>

                <!-- se cargan los resultados de la busqueda -->
                <div id="searchresult" class="mt-3"> <?php echo $search_results; ?> 
                </div>


            </div>
        </div>
    </div>
</body>
</html>
