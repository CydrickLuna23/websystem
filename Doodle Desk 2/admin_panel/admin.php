<?php
// **Database connection setup** 
// Kinoconnect ang script sa database gamit ang mysqli.
// Dito naka-store ang credentials ng server at database.
$servername = "sql209.infinityfree.com"; 
$username = "if0_37942168"; 
$password = "i2CzDbucPVZC8r"; 
$dbname = "if0_37942168_doodledesk";

$conn = new mysqli($servername, $username, $password, $dbname); 

// Check kung successful ang connection; kung hindi, terminate script with error message.
if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error); 
}

// **Handle form submission** 
// Sinisigurado na ang request ay POST bago ito i-process.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["submit"])) {
        // **Get form inputs**
        // Kinukuha ang data ng product mula sa form.
        $product_id = isset($_POST["product_id"]) ? $_POST["product_id"] : null; 
        $product_name = $_POST["product_name"]; 
        $product_description = $_POST["product_description"]; // Product description
        $product_price = $_POST["product_price"]; 
        $product_quantity = $_POST["product_quantity"]; 
        $product_picture = "";

        // **File upload handling**
        // Ina-upload ang product picture kung may na-upload.
        if (isset($_FILES["product_picture"]) && $_FILES["product_picture"]["error"] == 0) {
            $target_dir = "../images/"; // Directory ng mga images.
            $target_file = $target_dir . basename($_FILES["product_picture"]["name"]); 
            move_uploaded_file($_FILES["product_picture"]["tmp_name"], $target_file); 
            $product_picture = $target_file; 
        }

        if ($product_id) { 
            // **Update existing product**
            // Kung may product ID, ina-update ang product data.
            if (!empty($product_picture)) { 
                $stmt = $conn->prepare("UPDATE products SET product_name=?, product_description=?, product_picture=?, product_price=?, product_quantity=? WHERE product_id=?"); 
                $stmt->bind_param("sssdii", $product_name, $product_description, $product_picture, $product_price, $product_quantity, $product_id); 
            } else { 
                $stmt = $conn->prepare("UPDATE products SET product_name=?, product_description=?, product_price=?, product_quantity=? WHERE product_id=?"); 
                $stmt->bind_param("ssdii", $product_name, $product_description, $product_price, $product_quantity, $product_id); 
            }
        } else { 
            // **Insert new product**
            // Kapag walang product ID, mag-a-add ng bagong product sa database.
            $stmt = $conn->prepare("INSERT INTO products (product_name, product_description, product_picture, product_price, product_quantity) VALUES (?, ?, ?, ?, ?)"); 
            $stmt->bind_param("sssdi", $product_name, $product_description, $product_picture, $product_price, $product_quantity); 
        }

        // **Execute statement and provide feedback**
        if ($stmt->execute()) { 
            $message = $product_id ? "Product updated successfully!" : "Product added successfully!"; 
        } else { 
            $message = "Error: " . $stmt->error; 
        }
        $stmt->close(); 
    }
} elseif (isset($_GET["delete"])) { 
    // **Handle delete action**
    // Kapag may "delete" sa URL, dini-delete ang product based sa ID.
    $product_id = $_GET["delete"]; 
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id=?"); 
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) { 
        $message = "Product deleted successfully!"; 
    } else { 
        $message = "Error: " . $stmt->error; 
    }
    $stmt->close(); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - DoodleDesk</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin">
        <h1>Admin Panel</h1>
        <!-- Button para mag-update ng orders -->
        <a href="adminChecks.php"><button class="check">Update the Order</button></a>
    </div>

    <!-- Success or error messages -->
    <?php if (isset($message)) echo "<p style='color: green;'>$message</p>"; ?>

    <!-- **Form for adding or updating products** -->
    <form action="admin.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" id="product_id" name="product_id" value="<?php echo isset($_GET['edit']) ? $_GET['edit'] : ''; ?>">

        <label for="product_name">Product Name:</label>
        <input type="text" id="product_name" name="product_name" value="<?php echo isset($_GET['edit']) ? getProductData($conn, $_GET['edit'], 'product_name') : ''; ?>" required><br>

        <label for="product_description">Product Description:</label>
        <input type="text" id="product_description" name="product_description" value="<?php echo isset($_GET['edit']) ? getProductData($conn, $_GET['edit'], 'product_description') : ''; ?>" required><br>

        <label for="product_picture">Product Picture:</label>
        <input type="file" id="product_picture" name="product_picture" accept="image/*"><br>

        <label for="product_price">Product Price:</label>
        <input type="number" id="product_price" name="product_price" step="0.01" value="<?php echo isset($_GET['edit']) ? getProductData($conn, $_GET['edit'], 'product_price') : ''; ?>" required><br>

        <label for="product_quantity">Product Quantity:</label>
        <input type="number" id="product_quantity" name="product_quantity" value="<?php echo isset($_GET['edit']) ? getProductData($conn, $_GET['edit'], 'product_quantity') : ''; ?>" required><br>

        <button type="submit" name="submit"><?php echo isset($_GET['edit']) ? 'Update Product' : 'Add Product'; ?></button>
    </form>

    <h2>Products List</h2>
    <div id="product_list">
        <?php
        // **Display all products**
        $sql = "SELECT * FROM products"; 
        $result = $conn->query($sql);

        if ($result->num_rows > 0) { 
            echo "<table>"; 
            echo "<tr><th>ID</th><th>Name</th><th>Description</th><th>Picture</th><th>Price</th><th>Quantity</th><th>Actions</th></tr>";
            while ($row = $result->fetch_assoc()) { 
                echo "<tr>
                        <td>{$row['product_id']}</td>
                        <td>{$row['product_name']}</td>
                        <td style='width: 150px; word-wrap: break-word;'>{$row['product_description']}</td>
                        <td><img src='{$row['product_picture']}' alt='Product Image' width='50'></td>
                        <td>{$row['product_price']}</td>
                        <td>{$row['product_quantity']}</td>
                        <td>
                            <a href='admin.php?edit={$row['product_id']}'>Edit</a>
                            <a href='admin.php?delete={$row['product_id']}'>Delete</a>
                        </td>
                      </tr>"; 
            }
            echo "</table>"; 
        } else { 
            echo "No products available."; 
        }

        // **Helper function to get product data**
        function getProductData($conn, $product_id, $field) { 
            $value = ""; 
            $stmt = $conn->prepare("SELECT $field FROM products WHERE product_id=?"); 
            $stmt->bind_param("i", $product_id); 
            $stmt->execute(); 
            $stmt->bind_result($value); 
            $stmt->fetch(); 
            $stmt->close(); 
            return $value; 
        }
        $conn->close();
        ?>
    </div>
</body>
</html>
