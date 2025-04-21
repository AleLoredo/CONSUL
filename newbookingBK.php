<?php
include('session.php'); // security check 
include ('db.php'); // Include the database connection
include('functions.php'); // incluir funciones especiales
include('id_contacto_nombre.php'); // revisar si hay un ID de contacto y obtener su nombre y apellido en variables

// inicialización de arrays
$dates = [];
$times = [];

// almacenar mes y año en variables solo si ya fueron posteadas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['month'], $_POST['year'])) {
    $selectedMonth = $_POST['month'];
    $selectedYear = $_POST['year'];} 
else {
    $selectedMonth = date('n'); // Mes actual
    $selectedYear = date('Y'); // Año actual
}

    // obtener dia actual en GMT-3 Arg time
    $today = getTodayGMTMinus3();
    // Prepare and execute the SQL query to fetch dates using $today as filter 
    $stmt = $pdo->prepare("SELECT date FROM dates WHERE MONTH(date) = ? AND YEAR(date) = ? AND date >= ?");
    $stmt->execute([$selectedMonth, $selectedYear, $today]);
    $dates = $stmt->fetchAll(PDO::FETCH_ASSOC);


    
    // Fetch available times from the times table
    $stmt = $pdo->query("SELECT time FROM times");
    $allTimes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Esto obtiene las reservas existentes de manera ineficiente
//    $stmt = $pdo->prepare("SELECT date, time FROM bookings WHERE MONTH(date) = ? AND YEAR(date) = ? AND isactive = 1");
//    $stmt->execute([$selectedMonth, $selectedYear]);
//    $bookedTimes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //nueva manera mas optimizada de obtener todas las reservas existentes
    $stmt = $pdo->prepare("SELECT date, time FROM bookings 
                       WHERE date BETWEEN ? AND ? 
                       AND isactive = 1");
    $startDate = "$selectedYear-$selectedMonth-01";  // First day of the month
    $endDate = date("Y-m-t", strtotime($startDate)); // Last day of the month

    $stmt->execute([$startDate, $endDate]);
    $bookedTimes = $stmt->fetchAll(PDO::FETCH_ASSOC);



    // Reestructurar los horarios reservados en un array asociativo con la fecha como clave
$reservedByDate = [];
foreach ($bookedTimes as $booking) {
    $reservedByDate[$booking['date']][] = $booking['time'];
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Booking</title>
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


                <div class="col-md-10">
                    <!-- continua hibridizando a partir de este punto -->
                        <div class="container mt-4 border p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3><i class="bi bi-calendar"></i> 
                                <?php 
                                echo getMonthName($selectedMonth) . ' ' . $selectedYear . ' | Paciente: ' . $contactlastname . ', ' . $contactname;
                                ?>
                                </h3>

                                <form method="POST" action="newbooking.php?id=<?php echo $contact_id ?>">
                                

                                    <select name="month" class="form-select w-auto d-inline-block" required>
                                        <option value="" disabled selected>Mes</option>    
                                        <option value="1">Enero</option>
                                        <option value="2">Febrero</option>
                                        <option value="3">Marzo</option>
                                        <option value="4">Abril</option>
                                        <option value="5">Mayo</option>
                                        <option value="6">Junio</option>
                                        <option value="7">Julio</option>
                                        <option value="8">Agosto</option>
                                        <option value="9">Septiembre</option>
                                        <option value="10">Octubre</option>
                                        <option value="11">Noviembre</option>
                                        <option value="12">Diciembre</option>
                                    </select>
                                    
                                    <select name="year" class="form-select w-auto d-inline-block">
                                        <option value="" disabled>Año</option>
                                        <option value="2025" <?php echo ($selectedYear == 2025) ? 'selected' : ''; ?>>2025</option>
                                        <option value="2026" <?php echo ($selectedYear == 2026) ? 'selected' : ''; ?>>2026</option>
                                        <option value="<?php echo $selectedYear; ?>" selected><?php echo $selectedYear; ?></option>
                                    </select>


                                    <!-- <select name="year" class="form-select w-auto d-inline-block">
                                        <option value="" disabled selected>Año</option>
                                        <option value="2025">2025</option>
                                        <option value="2026">2026</option>
                                    </select> -->
                                    <button type="submit" class="btn btn-primary">CONSULTAR</button>
                                </form>
                            </div>
                            <hr>

                            <div class="row mt-5">
                                
                                <!-- Sección Agendar Turno -->
                                <div class="col-md-6">
                                    <h5 class="fw-bold">AGENDAR TURNO</h5>

                                    <div class="card p-2">
                                        <!-- Inicia repetidor de turnos -->
                                        <?php foreach ($dates as $date): 
                                            $currentDate = $date['date']; // Fecha actual en el loop ?>
                                             <form action="newbooking2.php?id=<?php echo $contact_id ?>" method="POST" class="d-flex align-items-center border-bottom p-2">
                                                <span class="me-2">   
                                                    <?php 
                                                        $formattedDate = date('d-m-Y', strtotime($currentDate));
                                                        echo getDayOfWeek($currentDate) . " | " . $formattedDate; 
                                                    ?>
                                                </span>
                                                <input type="hidden" name="selected_date" value="<?php echo $currentDate; ?>">
                                                <input type="hidden" name="isoverbook" value="0">

                                                <select name="selected_time" class="form-select me-2" style="width: auto;" required>
                                                    <option value="" disabled selected>Hora</option>
                                                    <?php 
                                                    foreach ($allTimes as $time): 
                                                        $timeValue = $time['time'];

                                                        // Verificar si el horario ya está reservado en la fecha actual
                                                        if (!isset($reservedByDate[$currentDate]) || !in_array($timeValue, $reservedByDate[$currentDate])): 
                                                    ?>
                                                            <option value="<?php echo $timeValue; ?>">
                                                                <?php echo date('H:i', strtotime($timeValue)); ?>
                                                            </option>
                                                    <?php 
                                                        endif;
                                                    endforeach; 
                                                    ?>
                                                </select>
                                                
                                                <button type="submit" class="btn btn-primary">                                            
                                                    <i class="bi bi-arrow-right"></i>
                                                </button>
                                            </form>
                                        <?php endforeach; ?>


                                    </div>
                                </div>

                                <!-- Sección Agendar Sobreturno -->
                                
                                <div class="col-md-6">
                                    <h5 class="fw-bold">SOBRETURNOS</h5>

                                    <div class="card p-2">
                                        <!-- Inicia repetidor de sobreturnos -->
                                        
                                        <?php 
                                        
                                        foreach ($dates as $date): ?>
                                        <form action="newbooking2.php?id=<?php echo $contact_id ?>" method="POST" class="d-flex align-items-center border-bottom p-2">
                                            
                                            <input type="hidden" name="selected_date" value="<?php echo $date['date']; ?>">
                                            <input type="hidden" name="isoverbook" value="1">
                                            <select name="selected_time" class="form-select me-2" style="width: auto;" required>
                                                <option value="" disabled selected>Hora</option>
                                                <?php foreach ($allTimes as $time): ?>
                                                    <option value="<?php echo $time['time']; ?>">
                                                    <?php echo date('H:i', strtotime($time['time'])); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" class="btn btn-info">                                            
                                                <i class="bi bi-arrow-right"></i>
                                            </button>
                                        </form>
                                        <?php endforeach; ?>
                                        

                                    </div>
                                </div>
                            </div>

                        </div>

                </div>
            </div>
    </div>         

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
