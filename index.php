<?php
// Start the session to track login status
session_start();

// If the user is already logged in, redirect them to the dashboard or home page
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');  // Change 'dashboard.php' to your target page
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="d-flex vh-100 justify-content-center align-items-center bg-primary">

    <div class="card p-4 shadow-lg">
        <!-- Display error message if any -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="login.php" method="POST">
            <div class="mb-3">
                <input type="text" class="form-control text-center" name="username" placeholder="Usuario" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control text-center" name="password" placeholder="Contraseña" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
        </form>
    </div>

</body>
</html>
