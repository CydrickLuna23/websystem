<?php
session_start(); // Simulan ang session para sa pag-manage ng session data tulad ng cart at user information.

// Database connection
$servername = "sql209.infinityfree.com";
$username = "if0_37942168";
$password = "i2CzDbucPVZC8r";
$dbname = "if0_37942168_doodledesk";

$conn = new mysqli($servername, $username, $password, $dbname); // Gumagawa ng bagong koneksyon sa database gamit ang MySQLi.

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // I-display ang error message kung hindi matagumpay ang koneksyon.
}

// Place order functionality
if (isset($_POST['place_order']) && isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) { 
    // Sinisigurado na may "place_order" na POST request at may laman ang cart sa session.

    // Only process the checked items
    if (isset($_POST['order_ids']) && count($_POST['order_ids']) > 0) {
        // I-loop ang bawat checked na order ID mula sa form.
        foreach ($_POST['order_ids'] as $order_id) {
            $item = $_SESSION['cart'][$order_id]; // Kunin ang item mula sa session cart gamit ang order_id.
            $user_id = 1; // Dummy value para sa user ID. Palitan ito ng tamang user ID sa production.
            $product_id = $item['product_id']; // Kunin ang product ID mula sa cart item.
            $order_quantity = $item['quantity']; // Kunin ang quantity na inorder.
            $total_price = $item['total_price']; // Kunin ang kabuuang presyo.

            // Insert the order into the orders table with status 'To Pay'
            $sql = "INSERT INTO orders (user_id, product_id, order_quantity, total_price, status) 
                    VALUES ('$user_id', '$product_id', '$order_quantity', '$total_price', 'To Pay')";

            if ($conn->query($sql) === TRUE) { // Kapag matagumpay ang pag-insert sa database.
                // Update product quantity in the products table after order is placed
                $update_sql = "UPDATE products 
                               SET product_quantity = product_quantity - '$order_quantity' 
                               WHERE product_id = '$product_id'";

                if ($conn->query($update_sql) === TRUE) {
                    echo "Order placed successfully and product quantity updated."; // Success message.
                } else {
                    echo "Error updating product quantity: " . $conn->error; // Error sa pag-update ng product quantity.
                }
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error; // Error sa pag-insert ng order.
            }
        }
    }

    // Remove only the checked items from the cart
    if (isset($_POST['order_ids'])) {
        foreach ($_POST['order_ids'] as $order_id) {
            unset($_SESSION['cart'][$order_id]); // Alisin ang item sa cart session gamit ang order ID.
        }
    }
}

// Fetch orders by status
$sql_to_pay = "SELECT o.order_id, p.product_name, o.order_quantity, o.total_price, o.order_date 
               FROM orders o 
               JOIN products p ON o.product_id = p.product_id
               WHERE o.status = 'To Pay'"; // Query para sa mga orders na nasa 'To Pay' status.

$sql_to_ship = "SELECT o.order_id, p.product_name, o.order_quantity, o.total_price, o.order_date 
                FROM orders o 
                JOIN products p ON o.product_id = p.product_id
                WHERE o.status = 'To Ship'"; // Query para sa mga orders na nasa 'To Ship' status.

$sql_to_receive = "SELECT o.order_id, p.product_name, o.order_quantity, o.total_price, o.order_date 
                   FROM orders o 
                   JOIN products p ON o.product_id = p.product_id
                   WHERE o.status = 'To Receive'"; // Query para sa mga orders na nasa 'To Receive' status.

$sql_completed = "SELECT o.order_id, p.product_name, o.order_quantity, o.total_price, o.order_date 
                  FROM orders o 
                  JOIN products p ON o.product_id = p.product_id
                  WHERE o.status = 'Completed'"; // Query para sa mga orders na 'Completed'.

$result_to_pay = $conn->query($sql_to_pay); // Kunin ang resulta ng 'To Pay' query.
$result_to_ship = $conn->query($sql_to_ship); // Kunin ang resulta ng 'To Ship' query.
$result_to_receive = $conn->query($sql_to_receive); // Kunin ang resulta ng 'To Receive' query.
$result_completed = $conn->query($sql_completed); // Kunin ang resulta ng 'Completed' query.
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="cart.css">
</head>
<body>
<h1>Your Orders</h1>

<!-- Cart Section -->
<div class="cart-container">
    <h2>Your Cart</h2>
    <form action="cart.php" method="post"> <!-- Form para sa pag-submit ng orders -->
        <?php
        // I-check kung ang cart ay may laman.
        if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
            echo '<table>';
            echo '<tr><th>Select</th><th>Product Name</th><th>Quantity</th><th>Total Price</th><th>Remove</th></tr>';
            
            // I-loop ang bawat item sa cart.
            foreach ($_SESSION['cart'] as $index => $item) {
                echo '<tr>
                        <td><input type="checkbox" name="order_ids[]" value="' . $index . '"></td> <!-- Checkbox para sa pagpili ng items na i-order -->
                        <td>' . $item['product_name'] . '</td> <!-- Ipakita ang pangalan ng produkto -->
                        <td>' . $item['quantity'] . '</td> <!-- Ipakita ang dami ng produkto -->
                        <td>₱' . $item['total_price'] . '</td> <!-- Ipakita ang kabuuang presyo ng produkto -->
                        <td><a href="remove_from_cart.php?index=' . $index . '">Remove</a></td> <!-- Link para alisin ang item sa cart -->
                      </tr>';
            }
            echo '</table>';
            echo '<button type="submit" name="place_order">Place Order</button>'; // Button para sa pag-place ng order.
        } else {
            echo "<p>Your cart is empty.</p>"; // Kapag walang laman ang cart.
        }
        ?>
    </form>
</div>

<!-- Order Status Sections -->
<div class="order-status-wrapper">
    <!-- To Pay Section -->
    <div class="status-container" id="to-pay">
        <h2>To Pay</h2>
        <?php
        // I-check kung may resulta ang query para sa 'To Pay' status.
        if ($result_to_pay->num_rows > 0) {
            echo '<table>';
            echo '<tr><th>Order ID</th><th>Product Name</th><th>Quantity</th><th>Total Price</th><th>Order Date</th></tr>';
            
            // I-loop ang bawat order na may status na 'To Pay'.
            while ($row = $result_to_pay->fetch_assoc()) {
                echo '<tr>
                        <td>' . $row['order_id'] . '</td> <!-- Ipakita ang Order ID -->
                        <td>' . $row['product_name'] . '</td> <!-- Ipakita ang Product Name -->
                        <td>' . $row['order_quantity'] . '</td> <!-- Ipakita ang dami ng order -->
                        <td>₱' . $row['total_price'] . '</td> <!-- Ipakita ang kabuuang presyo -->
                        <td>' . $row['order_date'] . '</td> <!-- Ipakita ang petsa ng order -->
                      </tr>';
            }
            echo '</table>';
        } else {
            echo "<p>No orders to pay.</p>"; // Kapag walang orders na may 'To Pay' status.
        }
        ?>
    </div>

    <!-- To Ship Section -->
    <div class="status-container" id="to-ship">
        <h2>To Ship</h2>
        <?php
        // I-check kung may resulta ang query para sa 'To Ship' status.
        if ($result_to_ship->num_rows > 0) {
            echo '<table>';
            echo '<tr><th>Order ID</th><th>Product Name</th><th>Quantity</th><th>Total Price</th><th>Order Date</th></tr>';
            
            // I-loop ang bawat order na may status na 'To Ship'.
            while ($row = $result_to_ship->fetch_assoc()) {
                echo '<tr>
                        <td>' . $row['order_id'] . '</td> <!-- Ipakita ang Order ID -->
                        <td>' . $row['product_name'] . '</td> <!-- Ipakita ang Product Name -->
                        <td>' . $row['order_quantity'] . '</td> <!-- Ipakita ang dami ng order -->
                        <td>₱' . $row['total_price'] . '</td> <!-- Ipakita ang kabuuang presyo -->
                        <td>' . $row['order_date'] . '</td> <!-- Ipakita ang petsa ng order -->
                      </tr>';
            }
            echo '</table>';
        } else {
            echo "<p>No orders to ship.</p>"; // Kapag walang orders na may 'To Ship' status.
        }
        ?>
    </div>

    <!-- To Receive Section -->
    <div class="status-container" id="to-receive">
        <h2>To Receive</h2>
        <?php
        // I-check kung may resulta ang query para sa 'To Receive' status.
        if ($result_to_receive->num_rows > 0) {
            echo '<table>';
            echo '<tr><th>Order ID</th><th>Product Name</th><th>Quantity</th><th>Total Price</th><th>Order Date</th></tr>';
            
            // I-loop ang bawat order na may status na 'To Receive'.
            while ($row = $result_to_receive->fetch_assoc()) {
                echo '<tr>
                        <td>' . $row['order_id'] . '</td> <!-- Ipakita ang Order ID -->
                        <td>' . $row['product_name'] . '</td> <!-- Ipakita ang Product Name -->
                        <td>' . $row['order_quantity'] . '</td> <!-- Ipakita ang dami ng order -->
                        <td>₱' . $row['total_price'] . '</td> <!-- Ipakita ang kabuuang presyo -->
                        <td>' . $row['order_date'] . '</td> <!-- Ipakita ang petsa ng order -->
                      </tr>';
            }
            echo '</table>';
        } else {
            echo "<p>No orders to receive.</p>"; // Kapag walang orders na may 'To Receive' status.
        }
        ?>
    </div>

    <!-- Completed Section -->
    <div class="status-container" id="completed">
        <h2>Completed</h2>
        <?php
        // I-check kung may resulta ang query para sa 'Completed' status.
        if ($result_completed->num_rows > 0) {
            echo '<table>';
            echo '<tr><th>Order ID</th><th>Product Name</th><th>Quantity</th><th>Total Price</th><th>Order Date</th></tr>';
            
            // I-loop ang bawat order na may status na 'Completed'.
            while ($row = $result_completed->fetch_assoc()) {
                echo '<tr>
                        <td>' . $row['order_id'] . '</td> <!-- Ipakita ang Order ID -->
                        <td>' . $row['product_name'] . '</td> <!-- Ipakita ang Product Name -->
                        <td>' . $row['order_quantity'] . '</td> <!-- Ipakita ang dami ng order -->
                        <td>₱' . $row['total_price'] . '</td> <!-- Ipakita ang kabuuang presyo -->
                        <td>' . $row['order_date'] . '</td> <!-- Ipakita ang petsa ng order -->
                      </tr>';
            }
            echo '</table>';
        } else {
            echo "<p>No completed orders.</p>"; // Kapag walang orders na 'Completed'.
        }
        ?>
    </div>
</div>

<a href="home.php"><button>Continue Shopping</button></a> <!-- Button para bumalik sa shopping page. -->

</body>
</html>

<?php
$conn->close(); // Isinasara ang koneksyon sa database.
?>
