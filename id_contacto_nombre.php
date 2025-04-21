<?php
// Si no está presente el ID, volver al dashboard 
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}
// Se convierte el ID a entero para mayor seguridad
$contact_id = (int) $_GET['id'];

// Prepare and execute the SQL query to fetch contact's name and lastname
$stmt = $pdo->prepare("SELECT name, lastname, phone1 FROM contacts WHERE id = ?");
$stmt->execute([$contact_id]);
$contact = $stmt->fetch(PDO::FETCH_ASSOC);

// Inicializar variables por si no hay resultados
$contactname = '';
$contactlastname = '';

if ($contact) {
    $contactname = htmlspecialchars($contact['name']);
    $contactlastname = htmlspecialchars($contact['lastname']);
}
?>
    