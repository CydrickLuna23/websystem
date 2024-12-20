<?php
session_start();

// Database connection setup
$servername = "sql209.infinityfree.com"; // Server address ng database
$username = "if0_37942168";              // Username para sa database
$password = "i2CzDbucPVZC8r";           // Password para sa database
$dbname = "if0_37942168_doodledesk";    // Pangalan ng database

$conn = new mysqli($servername, $username, $password, $dbname);

// Check kung may connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Error message kung hindi maka-connect sa database
}

// Pag-update ng order status
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_status'])) {
        // Mapping ng status para sa paglipat sa susunod na stage
        $status_map = [
            'To Pay' => 'To Ship',
            'To Ship' => 'To Receive',
            'To Receive' => 'Completed'
        ];

        $status_from = $_POST['status_from']; // Current status ng orders
        if (isset($_POST['order_ids']) && count($_POST['order_ids']) > 0) {
            foreach ($_POST['order_ids'] as $order_id) {
                $next_status = $status_map[$status_from]; // Naka-map na next status
                // Query para i-update ang status ng order
                $update_sql = "UPDATE orders SET status = '$next_status' WHERE order_id = '$order_id' AND status = '$status_from'";
                $conn->query($update_sql); // Execute ang update query
            }
        }
    }
}

// Function para kuhanin ang mga orders base sa status
function fetchOrdersByStatus($conn, $status) {
    $sql = "SELECT o.order_id, p.product_name, o.order_quantity, o.total_price, o.order_date 
            FROM orders o 
            JOIN products p ON o.product_id = p.product_id
            WHERE o.status = '$status'";
    return $conn->query($sql); // Babalik ang mga resulta mula sa query
}

// Kuhanin ang orders base sa kanilang status
$to_pay_orders = fetchOrdersByStatus($conn, 'To Pay');
$to_ship_orders = fetchOrdersByStatus($conn, 'To Ship');
$to_receive_orders = fetchOrdersByStatus($conn, 'To Receive');
$completed_orders = fetchOrdersByStatus($conn, 'Completed');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manage Orders</title>
    <link rel="stylesheet" href="adminChecks.css"> <!-- External CSS file -->
</head>
<body>
<h1>Admin Panel - Manage Orders</h1>

<div class="order-status-wrapper">
<!-- Section para sa "To Pay" -->
<div class="status-container">
<h2>To Pay</h2>
<form action="adminChecks.php" method="post">
    <input type="hidden" name="status_from" value="To Pay"> <!-- Current status -->
    <table>
        <tr><th>Select</th><th>Order ID</th><th>Product Name</th><th>Quantity</th><th>Total Price</th><th>Order Date</th></tr>
        <?php while ($row = $to_pay_orders->fetch_assoc()): ?> <!-- Loop para ipakita ang orders -->
            <tr>
                <td><input type="checkbox" name="order_ids[]" value="<?= $row['order_id'] ?>"></td>
                <td><?= $row['order_id'] ?></td>
                <td><?= $row['product_name'] ?></td>
                <td><?= $row['order_quantity'] ?></td>
                <td>₱<?= $row['total_price'] ?></td>
                <td><?= $row['order_date'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
    <button type="submit" name="update_status">Move to "To Ship"</button> <!-- Button para i-update ang status -->
</form>
</div>

<!-- To Ship Section -->
<div class="status-container">
<h2>To Ship</h2>
<form action="adminChecks.php" method="post">
    <input type="hidden" name="status_from" value="To Ship">
    <table>
        <tr><th>Select</th><th>Order ID</th><th>Product Name</th><th>Quantity</th><th>Total Price</th><th>Order Date</th></tr>
        <?php while ($row = $to_ship_orders->fetch_assoc()): ?>
            <tr>
                <td><input type="checkbox" name="order_ids[]" value="<?= $row['order_id'] ?>"></td>
                <td><?= $row['order_id'] ?></td>
                <td><?= $row['product_name'] ?></td>
                <td><?= $row['order_quantity'] ?></td>
                <td>₱<?= $row['total_price'] ?></td>
                <td><?= $row['order_date'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
    <button type="submit" name="update_status">Move to "To Receive"</button>
</form>
</div>

<!-- To Receive Section -->
<div class="status-container">
<h2>To Receive</h2>
<form action="adminChecks.php" method="post">
    <input type="hidden" name="status_from" value="To Receive">
    <table>
        <tr><th>Select</th><th>Order ID</th><th>Product Name</th><th>Quantity</th><th>Total Price</th><th>Order Date</th></tr>
        <?php while ($row = $to_receive_orders->fetch_assoc()): ?>
            <tr>
                <td><input type="checkbox" name="order_ids[]" value="<?= $row['order_id'] ?>"></td>
                <td><?= $row['order_id'] ?></td>
                <td><?= $row['product_name'] ?></td>
                <td><?= $row['order_quantity'] ?></td>
                <td>₱<?= $row['total_price'] ?></td>
                <td><?= $row['order_date'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
    <button type="submit" name="update_status">Move to "Completed"</button>
</form>
</div>

<!-- Completed Section -->
 <div class="status-container">
<h2>Completed</h2>
<table>
    <tr><th>Order ID</th><th>Product Name</th><th>Quantity</th><th>Total Price</th><th>Order Date</th></tr>
    <?php while ($row = $completed_orders->fetch_assoc()): ?>
        <tr>
            <td><?= $row['order_id'] ?></td>
            <td><?= $row['product_name'] ?></td>
            <td><?= $row['order_quantity'] ?></td>
            <td>₱<?= $row['total_price'] ?></td>
            <td><?= $row['order_date'] ?></td>
        </tr>
    <?php endwhile; ?>
</table>
</div>
</div>

</body>
</html>

<?php $conn->close(); ?>