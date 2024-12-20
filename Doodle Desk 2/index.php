<?php
// Connect to the database
$servername = "sql209.infinityfree.com"; // Pangalan ng server kung saan naka-host ang database
$username = "if0_37942168"; // Username na ginagamit para sa database connection
$password = "i2CzDbucPVZC8r"; // Password para sa database connection
$dbname = "if0_37942168_doodledesk"; // Pangalan ng database

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname); 
// Gumagawa ng bagong koneksyon sa database gamit ang MySQLi

// Check the connection
if ($conn->connect_error) { // Sinusuri kung may error sa koneksyon
    die("Database connection failed: " . $conn->connect_error); 
    // Kung may error, ihinto ang script at ipakita ang error message
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- Itinatakda ang character encoding sa UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <!-- Itinatakda ang viewport para sa responsive design -->
    <title>DoodleDesk</title> <!-- Title ng web page -->
    <link rel="stylesheet" href="index.css"> <!-- Link sa external CSS file para sa styling -->
</head>
<body>
    <img src="images/doodle.png" alt="Doodle Logo"> 
    <!-- Logo ng website na naka-save sa "images" folder -->
    <a href="signup_login/login.php">Login</a> 
    <!-- Link papunta sa Login page -->
    <a href="signup_login/signup.php">Sign Up</a> 
    <!-- Link papunta sa Sign-Up page -->
</body>
</html>

