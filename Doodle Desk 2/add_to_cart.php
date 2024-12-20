<?php

// so bale, ito ang javascript para masagawa ang pag add to cart sa home.php
// Simulan ang session para ma-access at magamit ang session variables.
session_start();

// Mga detalye ng database connection.
$servername = "sql209.infinityfree.com";
$username = "if0_37942168";
$password = "i2CzDbucPVZC8r";
$dbname = "if0_37942168_doodledesk";

// Gumagawa ng koneksyon sa MySQL database gamit ang mysqli.
$conn = new mysqli($servername, $username, $password, $dbname);

// Sine-check kung may error sa koneksyon. Kung meron, ititigil ang script at ipapakita ang error.
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Kunin ang product ID at quantity mula sa POST data na ipinasa mula sa form.
// `intval` ang ginamit para matiyak na ang input ay integer.
$product_id = intval($_POST['product_id']);
$quantity = intval($_POST['quantity']);

// Kumuha ng detalye ng produkto mula sa database gamit ang product ID.
$sql = "SELECT * FROM products WHERE product_id = $product_id";
$result = $conn->query($sql);

// Sine-check kung may nahanap na produkto sa database.
if ($result->num_rows > 0) {
    // Kunin ang product details bilang associative array.
    $product = $result->fetch_assoc();
    // I-compute ang total price ng produkto batay sa quantity.
    $total_price = $product['product_price'] * $quantity;

    // Sine-check kung mayroon nang session variable na `cart`.
    if (!isset($_SESSION['cart'])) {
        // Kung wala pa, gumawa ng bagong `cart` array sa session.
        $_SESSION['cart'] = [];
    }
    
    // Idagdag ang produkto sa `cart` session array.
    $_SESSION['cart'][] = [
        'product_id' => $product_id,                // ID ng produkto.
        'product_name' => $product['product_name'], // Pangalan ng produkto.
        'quantity' => $quantity,                   // Quantity na binili.
        'price_per_unit' => $product['product_price'], // Presyo kada unit.
        'total_price' => $total_price,             // Kabuuang presyo ng produkto.
    ];
}

// I-redirect pabalik sa `home.php` pagkatapos ma-update ang cart.
// `header` function ang ginagamit para sa redirection.
header("Location: home.php");
exit; // Itigil ang script execution pagkatapos ng redirection.
?>
