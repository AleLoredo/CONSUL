<?php
// Include the database connection
include('db.php');

// Start the session to track login status
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user input
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        // Prepare a statement to fetch the user from the sysusers table
        $stmt = $pdo->prepare("SELECT * FROM sysusers WHERE user = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the user exists in the database
        if ($user) {
            // Verify the password against the hashed password in the database
            if (password_verify($password, $user['password'])) {
                // Regenerate the session ID to prevent session fixation attacks    
                session_regenerate_id(true);
                // Store the user session for logged-in status
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['user'];
                $_SESSION['isadmin'] = $user['isadmin'];
                $_SESSION['issuperadmin'] = $user['issuperadmin'];
                $_SESSION['enabled'] = $user['enabled'];

                // Redirect to the home page or dashboard
                header('Location: dashboard.php');  // Change 'dashboard.php' to the appropriate page
                exit;
            } else {
                // Incorrect password
                header('Location: index.php?error=Usuario o contraseña incorrectos. Intenta de nuevo.');
                exit;
            }
        } else {
            // User doesn't exist
            header('Location: index.php?error=Usuario o contraseña incorrectos. Intenta de nuevo.');
            exit;
        }
    } catch (PDOException $e) {
        // Handle any database connection errors
        header('Location: index.php?error=Error al conectar con la base de datos.');
        exit;
    }
}
                // Regenerate the session ID to prevent session fixation attacks
                session_regenerate_id(true);
?>