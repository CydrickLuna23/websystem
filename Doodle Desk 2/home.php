<?php
// Database connection
$servername = "sql209.infinityfree.com"; // Pangalan ng server kung saan naka-host ang database
$username = "if0_37942168"; // Username na ginagamit para sa database connection
$password = "i2CzDbucPVZC8r"; // Password para sa database connection
$dbname = "if0_37942168_doodledesk"; // Pangalan ng database

$conn = new mysqli($servername, $username, $password, $dbname); // Gumagawa ng bagong koneksyon sa database gamit ang MySQLi

// Check connection
if ($conn->connect_error) { // Sinusuri kung may error sa koneksyon
    die("Connection failed: " . $conn->connect_error); // Kung may error, ihinto ang script at ipakita ang error message
}

// Fetch products from the database
$sql = "SELECT * FROM products"; // Query para kunin lahat ng products mula sa table na "products"
$result = $conn->query($sql); // I-eexecute ang query at itatabi ang resulta sa $result

// Initialize search query if submitted
$search_query = ''; // I-initialize ang search query bilang empty string
if (isset($_GET['search'])) { // Tinitingnan kung may search parameter na naipasa mula sa URL
    $search_query = $_GET['search']; // Kung meron, itatabi ito sa $search_query
}

// Fetch products based on search query
if ($search_query) { // Kung may laman ang $search_query
    $sql = "SELECT * FROM products WHERE product_name LIKE '%$search_query%'"; 
    // Query na naghahanap ng products na may pangalan na tumutugma sa search term
} else {
    $sql = "SELECT * FROM products"; // Kung walang search term, kunin lahat ng products
}

$result = $conn->query($sql); // I-execute ang query at itabi ang resulta sa $result
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="intro.css">
</head>
<body>

    <header>
    <div class="search-container">
            <form action="home.php" method="GET">
                <input type="text" name="search" id="search-input" placeholder="Search products..." value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" class="searchbtn">Search</button>
            </form>
        </div>

        <img src="images/doodle.png" class="logo">

        <div class="others">
        <a href="cart.php"><button>Cart</button></a>
        
        <a href="signup_login/profile.php"> <button>Profile</button></a>

        <a href="about.php"><button>About</button></a>
        </div>

    </header>
    
   
    <div class="container">
    <div class="product-grid">
        <?php
        if ($result->num_rows > 0) { // Kung may resulta mula sa query
            while ($row = $result->fetch_assoc()) {
                // I-loop ang bawat row ng resulta
                $image_path = 'images/' . $row["product_picture"];
                // Kunin ang image path mula sa database
                echo '<div class="product-card" data-id="' . $row["product_id"] . '" data-description="' . htmlspecialchars($row["product_description"], ENT_QUOTES) . '" data-quantity="' . $row["product_quantity"] . '">
                        <img src="' . $image_path . '" alt="' . $row["product_name"] . '" class="product-img">
                        <h2>' . $row["product_name"] . '</h2>
                        <p>₱' . $row["product_price"] . '</p>
                      </div>';
            }
        } else {
            echo "<p>No products available.</p>"; // Kung walang resulta, magpakita ng mensahe
        }
        ?>
    </div>
</div>


    </div>

    <!-- Expanded Product View -->
    <div id="product-view" class="product-view">
        <div class="view-content">
            <span id="close-view" class="close-button">&times;</span>
            <img id="view-img" src="" alt="Product Image">
            <h2 id="view-name"></h2>
            <p id="view-price"></p>
            <p id="view-description"></p>
            <p id="view-quantity"></p>
            <form action="add_to_cart.php" method="POST">
                <input type="hidden" name="product_id" id="view-product-id" value="">
                <input type="number" name="quantity" min="1" value="1" class="quantity-input">
                <button type="submit" class="add-to-cart-button">Add to Cart</button>
            </form>
        </div>
    </div>

    <?php $conn->close(); ?>
    
</body>
<script src="home.js"></script>
</head>
</html>