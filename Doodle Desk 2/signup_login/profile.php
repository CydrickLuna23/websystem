<?php
session_start();

// Check if the user is logged in
// Tinitiyak kung naka-login ang user sa pamamagitan ng pag-check sa session variable na `user_id`.
if (!isset($_SESSION['user_id'])) {
    // Kapag hindi naka-login, ire-redirect ang user sa `login.php`.
    header("Location: login.php");
    exit();
}

// Establish database connection
// Nagse-set up ng koneksyon sa MySQL database gamit ang mysqli.
$conn = new mysqli('sql209.infinityfree.com', 'if0_37942168', 'i2CzDbucPVZC8r', 'if0_37942168_doodledesk');

// Check connection
// Kung may error sa koneksyon, agad na ipapakita ang error message at ihihinto ang script.
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id']; // Kinukuha ang user ID mula sa session para magamit sa query.

// Fetch user details
// Gumagamit ng prepared statement para makuha ang impormasyon ng user sa database.
$stmt = $conn->prepare("SELECT username, fullname, email, phone_number, birthdate, image_path FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id); // `i` para sa integer na parameter binding.
$stmt->execute();
$result = $stmt->get_result(); // Kinukuha ang resulta ng query.

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc(); // Kinukuha ang user data bilang associative array.
} else {
    // Kung walang nahanap na user, magpapakita ng error at ihihinto ang script.
    echo "User not found.";
    exit();
}

// Close statement and connection
// Isinasara ang prepared statement at database connection para maiwasan ang resource leaks.
$stmt->close();
$conn->close();

// Handle logout
// Kapag nag-click ang user sa logout button, idi-destroy ang session at ire-redirect sa login page.
if (isset($_POST['logout'])) {
    session_destroy(); // Dinidestroy ang session data.
    header("Location: login.php"); // Redirect sa login page.
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="profile.css"> <!-- Link sa external CSS file para sa styling. -->
</head>
<body>
    <img src="../images/doodle.png" class="logo"> <!-- Logo ng website. -->

    <!-- Display user profile -->
    <div>
        <!-- Ipinapakita ang profile image kung mayroon. -->
        <?php if ($user['image_path']): ?>
            <img src="<?php echo $user['image_path']; ?>" alt="Profile Image" width="100">
        <?php else: ?>
            <p>No profile image</p> <!-- Default message kapag walang profile image. -->
        <?php endif; ?>

        <!-- Ipinapakita ang user details gamit ang data mula sa database. -->
        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['fullname']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($user['phone_number']); ?></p>
        <p><strong>Birthdate:</strong> <?php echo htmlspecialchars($user['birthdate']); ?></p>
    </div>

    <!-- Logout Button -->
    <form action="profile.php" method="POST">
        <button type="submit" name="logout">Logout</button> <!-- Button na nagse-send ng logout request. -->
    </form>
</body>
</html>
