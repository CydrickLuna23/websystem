<?php
session_start(); // Simulan ang session para magamit ang $_SESSION variables

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // I-check kung ang request ay POST
    $conn = new mysqli('sql209.infinityfree.com', 'if0_37942168', 'i2CzDbucPVZC8r', 'if0_37942168_doodledesk');

    if ($conn->connect_error) { // I-verify kung may error sa koneksyon
        die("Connection failed: " . $conn->connect_error); // Mag-output ng error message kung hindi makakonekta
    }

    $username = $_POST['username']; // Kunin ang username mula sa POST data
    $password = $_POST['password']; // Kunin ang password mula sa POST data

    // Gumamit ng prepared statements para maiwasan ang SQL injection
    $stmt = $conn->prepare("SELECT user_id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username); // Bind ang username bilang string
    $stmt->execute(); // I-execute ang query
    $result = $stmt->get_result(); // Kunin ang resulta ng query

    if ($result->num_rows > 0) { // I-check kung may nahanap na row
        $user = $result->fetch_assoc(); // Kunin ang impormasyon ng user
        if (password_verify($password, $user['password'])) { // I-verify ang password gamit ang `password_verify`
            $_SESSION['user_id'] = $user['user_id']; // I-save ang `user_id` sa session
            
            // I-redirect ang user sa home page gamit ang absolute URL
            header("Location: ../home.php");
            exit(); // Mag-exit pagkatapos ng redirection
        } else {
            echo "Invalid password."; // Error message kung mali ang password
        }
    } else {
        echo "No user found with that username."; // Error message kung walang user na nahanap
    }

    $stmt->close(); // Isara ang prepared statement. para hindi mag memory leak
    $conn->close(); // Isara ang database connection. para hindi mag memory leak
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <form action="login.php" method="POST">
        <label>Username: <input type="text" name="username" required></label><br>
        <label>Password: <input type="password" name="password" required></label><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>