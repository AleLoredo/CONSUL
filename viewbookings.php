<?php
include('session.php'); // security check 
include ('db.php'); // Include the database connection
include('functions.php'); // incluir funciones especiales

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['date'])) {
    // Convert DD/MM/YYYY to YYYY-MM-DD
    $selectDate = DateTime::createFromFormat('d/m/Y', $_POST['date'])->format('Y-m-d');

    $stmt = $pdo->prepare("
    SELECT 
        b.*, 
        c.phone1,
        c.id AS contact_id, 
        c.name AS contact_name, 
        c.lastname AS contact_lastname,
        i.insurname AS contact_insurname
    FROM bookings b
    INNER JOIN contacts c ON b.contact = c.id
    LEFT JOIN insurance_providers i ON c.insurid = i.id
    WHERE b.date = ? AND b.isactive = 1
    ORDER BY b.time ASC
");
    $stmt->execute([$selectDate]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $selectDate = getTodayGMTMinus3();
}




?>




<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">



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
                    INFORME DE AGENDA: <?php echo $selectDate; ?>
                </h4>
                
                <!-- Date Picker -->
                <form action="viewbookings.php" method="post"> 
                <div class="cs-form mb-3">
                            <label for="date" class="form-label"></label>
                            <div class="input-group date" id="datepicker">
                                <input type="text" class="form-control" name="date" id="date" placeholder="DD/MM/YYYY">
                                <div class="input-group-append">
                                
                                </div>
                            </div>
                </div>
                <button type="submit" class="btn btn-primary">CONSULTAR</button>
                </form>



                <div class="table-responsive mt-4">
                    <hr>
                    <table class="table text-center">
                        <thead>
                            <tr>
                                
                                <th>HORA</th>
                                <th>PACIENTE</th>
                                <th>1RA VEZ</th>
                                <th>TIPO</th>
                                <th>COBERTURA</th>
                                <th>OBSERVACIONES</th>
                                <th>ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($bookings)): ?>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    
                                    <td><?php echo date('H:i', strtotime($booking['time'])); ?></td>

                                    <td><?php echo $booking['contact_lastname'] . ', ' . $booking['contact_name'] ?></td>

                                    <td><?php echo ($booking['isfirsttime'] == 0) ? '---' : 'Si'; ?></td>

                                    <td>
                                        <?php echo ($booking['isoverbook'] == 0) ? '---' : 'Sobreturno'; ?>
                                    </td>
                                    <td>
                                        <?php echo$booking['contact_insurname']; ?>
                                    </td>
                                    <td><?php 
                                    if (!empty($booking['observations'])):
                                        echo htmlspecialchars($booking['observations']);
                                    else:
                                        echo '---';
                                    endif; ?></td>
                                    <td>

                                    <button class="btn btn-outline-danger" onclick="window.location.href='newcancel.php?bookid=<?php echo $booking['id']; ?>&id=<?php echo $booking['contact_id']; ?>'">X</button>
                                    <?php
                                    $phone = htmlspecialchars($booking['phone1']);
                                    $name = urlencode($booking['contact_lastname'] . ', ' . $booking['contact_name']);
                                    $date = urlencode(date('d-m-Y', strtotime($booking['date'])));
                                    $time = urlencode(date('H:i', strtotime($booking['time'])));
                                    $address = rawurlencode('Marcelo T. Alvear 2323 1° A. Saludos!');
                                    $text = "Hola%20$name%2C%20este%20es%20un%20recordatorio%20de%20tu%20turno%20el%20dia%20$date%20a%20las%20$time%20en%20$address";
                                    $whatsappUrl = "https://wa.me/+{$phone}?text={$text}";
                                    ?>

                                        <button class="btn btn-outline-secondary flex-grow-1" 
                                        onclick="window.open('<?php echo $whatsappUrl; ?>', '_blank')">
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
<script>
    $(document).ready(function () {
        $('#datepicker').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true
        });
    });
</script>
</html>
