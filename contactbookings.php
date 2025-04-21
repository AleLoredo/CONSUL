<?php
include('session.php'); // security check 
include ('db.php'); // Include the database connection
include('functions.php'); // incluir funciones especiales
include('id_contacto_nombre.php'); // revisar si hay un ID de contacto o volver y si hay obtener su nombre y apellido en variables

// obtener día actual en GMT-3 Arg time
$today = getTodayGMTMinus3();

// Prepare and execute the SQL query to fetch "bookings" where date >= $today as filter 
$stmt = $pdo->prepare("SELECT * FROM bookings AS b WHERE contact = ? AND isactive = 1 AND date >= ? ORDER BY b.date ASC");
$stmt->execute([$contact_id, $today]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

        <div class="col-md-10 p-4" id="cuerpocentral">
            <div class="text-center edit-container border rounded p-3 ">
                <h4 class="mb-3 mt-3">
                    <i class="bi bi-check-square-fill text-primary"></i>
                    TURNOS DEL PACIENTE
                </h4>
                <h5>
                    <?php echo $contactlastname . ', ' . $contactname; ?>
                <button class="btn btn-outline-success m-2" onclick="window.open('https://wa.me/+<?php echo htmlspecialchars($contact['phone1']); ?>', '_blank')">
                                    <i class="bi bi-whatsapp"></i>
                                </button></h5>

                <div class="table-responsive mt-4">
                    <hr>
                    <table class="table text-center">
                        <thead>
                            <tr>
                                <th>FECHA</th>
                                <th>HORA</th>
                                <th>TIPO</th>
                                <th>OBSERVACIONES</th>
                                <th>ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($bookings)): ?>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td><?php echo date('d-m-Y', strtotime($booking['date'])); ?></td>
                                    <td><?php echo date('H:i', strtotime($booking['time'])); ?></td>
                                    <td>
                                        <?php echo ($booking['isoverbook'] == 0) ? 'Normal' : 'Sobreturno'; ?>
                                    </td>
                                    <td><?php 
                                    if (!empty($booking['observations'])):
                                        echo htmlspecialchars($booking['observations']);
                                    else:
                                        echo '---';
                                    endif; ?></td>
                                    <td>
                                   
                                    
                                <button class="btn btn-outline-danger" onclick="window.location.href='newcancel.php?bookid=<?php echo $booking['id']; ?>&id=<?php echo $contact_id; ?>'">Cancelar</button>

                                <button class="btn btn-outline-secondary flex-grow-1" 
                                            onclick="window.open('https://wa.me/+<?php echo htmlspecialchars($contact['phone1']); ?>?text=Hola%20<?php echo urlencode($contactlastname . ', ' . $contactname); ?>%2C%20este%20es%20un%20recordatorio%20de%20tu%20turno%20el%20dia%20<?php 
                                            // echo urlencode($booking['date']);
                                            echo $formattedDate = urlencode(date('d-m-Y', strtotime($booking['date'])));
                                            ?>%20a%20las%20<?php 
                                            echo urlencode(date('H:i', strtotime($booking['time']))); ?>%20en%20<?php
                                            echo rawurlencode('Marcelo T. Alvear 2323 1° A. Saludos!'); ?>%20', '_blank')">
                                            Recordatorio
                                        </button>


                                       


                                        
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5">No hay turnos programados</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
</body>
</html>
