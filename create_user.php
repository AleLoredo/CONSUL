<?php
// Include database connection
include('db.php');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Debug: Check form input values
    var_dump($_POST);  // This will display all form data

    $username = $_POST['username'];
    $password = $_POST['password'];
    $enabled = isset($_POST['enabled']) ? 1 : 0;  // Checkbox for enabled
    $isadmin = isset($_POST['isadmin']) ? 1 : 0;  // Checkbox for admin
    $issuperadmin = isset($_POST['issuperadmin']) ? 1 : 0;  // Checkbox for superadmin

    // Hash the password using password_hash (default algorithm is bcrypt)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Prepare an insert statement
        $stmt = $pdo->prepare("INSERT INTO sysusers (user, password, enabled, isadmin, issuperadmin) VALUES (:username, :password, :enabled, :isadmin, :issuperadmin)");

        // Execute the query with the user input
        $stmt->execute([
            ':username' => $username,
            ':password' => $hashedPassword,
            ':enabled' => $enabled,
            ':isadmin' => $isadmin,
            ':issuperadmin' => $issuperadmin
        ]);

        echo "User successfully created!";
    } catch (PDOException $e) {
        // Handle errors
        echo "Error: " . $e->getMessage();
    }
}
?>

<!-- HTML form remains the same -->


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="d-flex vh-100 justify-content-center align-items-center bg-primary">

    <div class="card p-4 shadow-lg">
        <form action="create_user.php" method="POST">
            <h3 class="mb-3">Crear Usuario</h3>

            <div class="mb-3">
                <input type="text" class="form-control" name="username" placeholder="Nombre de usuario" required>
            </div>

            <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="Contraseña" required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" name="enabled">
                <label class="form-check-label">Usuario habilitado</label>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" name="isadmin">
                <label class="form-check-label">Es administrador</label>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" name="issuperadmin">
                <label class="form-check-label">Es superadministrador</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">Crear Usuario</button>
        </form>
    </div>

</body>
</html>
