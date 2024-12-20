<?php
// Ang block na ito ay tumitiyak na ang code ay tatakbo lamang kung ang request method ay POST (kung ang form ay sinubmit).
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Kumokonekta sa MySQL database gamit ang mysqli. Ang mga parameter ay hostname, username, password, at database name.
    $conn = new mysqli('sql209.infinityfree.com', 'if0_37942168', 'i2CzDbucPVZC8r', 'if0_37942168_doodledesk');

    // Kung may error sa connection, ihinto ang script at ipakita ang error message.
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Kinukuha ang mga value mula sa form gamit ang $_POST array.
    $username = $_POST['username'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $birthdate = $_POST['birthdate'];
    // Ang password ay sinisecure gamit ang password_hash function bago ito isave sa database.
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $image_path = ''; // Initial value ng image path.

    // Sine-check kung mayroong inupload na file at walang error sa upload.
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Nagse-set ng target directory para sa uploads at kinakalkula ang final path ng file.
        $target_dir = "uploads/";
        $image_path = $target_dir . basename($_FILES['image']['name']);
        // Inililipat ang uploaded file mula sa temporary location patungo sa target directory.
        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
    }

    // Gumagamit ng prepared statement para maiwasan ang SQL injection.
    $stmt = $conn->prepare("INSERT INTO users (username, fullname, email, phone_number, birthdate, image_path, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
    // Ibine-bind ang mga parameter sa prepared statement. Ang "sssssss" ay tumutukoy sa data type ng bawat parameter (string lahat).
    $stmt->bind_param("sssssss", $username, $fullname, $email, $phone_number, $birthdate, $image_path, $password);

    // Ine-execute ang prepared statement. Kung successful, nagpapakita ng success message; kung hindi, error message.
    if ($stmt->execute()) {
        echo "Signup successful! <a href='login.php'>Login here</a>";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Sinasara ang prepared statement at database connection para ma-release ang resources.
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <!-- Ina-link ang external CSS file para sa styling ng page. -->
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <!-- HTML form para sa signup. Ang "enctype='multipart/form-data'" ay kinakailangan para sa file uploads. -->
    <form action="signup.php" method="POST" enctype="multipart/form-data">
        <!-- Mga input fields para sa user data. Required ang ilang fields. -->
        <label>Username: <input type="text" name="username" required></label><br>
        <label>Full Name: <input type="text" name="fullname" required></label><br>
        <label>Email: <input type="email" name="email" required></label><br>
        <label>Phone Number: <input type="text" name="phone_number"></label><br>
        <label>Birthdate: <input type="date" name="birthdate"></label><br>
        <label>Image: <input type="file" name="image"></label><br>
        <label>Password: <input type="password" name="password" required></label><br>
        <!-- Submit button para maipasa ang form data. -->
        <button type="submit">Signup</button>
    </form>
</body>
</html>
