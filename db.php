<?php
// $host = '127.0.0.1'; // or 'localhost'
// $dbname = 'CONSUL';  // The name of your database
// $username = 'root';  // Your database username
// $password = '@aleabc123';      // Your database password

$host = 'localhost'; // or 'localhost'
$dbname = 'CONSUL2';  // The name of your database
$username = 'cerudb2user';  // Your database username
$password = '@cerudbuserpw';      // Your database password


try {
    // Creating a new PDO connection to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle the error if the connection fails
    die("Connection failed: " . $e->getMessage());
}
?>
