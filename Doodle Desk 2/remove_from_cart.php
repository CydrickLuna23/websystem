

<?php

//so bale, parang javascript ito kung paano mag remove ng added orders sa cart


session_start(); 
// Sinisimulan ang session para ma-access ang session variables tulad ng 'cart'.

// Check if the 'index' parameter is provided in the URL
if (isset($_GET['index'])) { 
    // Sinusuri kung ang 'index' parameter ay ipinasa sa URL (e.g., cart.php?index=1).
    $index = $_GET['index']; 
    // Kinukuha ang value ng 'index' mula sa URL.

    // Check if the cart session exists and if the index is valid
    if (isset($_SESSION['cart']) && isset($_SESSION['cart'][$index])) { 
        // Sinusuri kung may laman ang 'cart' session at kung valid ang index na tinutukoy.
        
        // Remove the item from the cart using the provided index
        unset($_SESSION['cart'][$index]); 
        // Inaalis ang item sa cart gamit ang `unset()` function para burahin ang item sa tinukoy na index.

        // Redirect back to the cart page after removal
        header("Location: cart.php"); 
        // Ire-redirect ang user pabalik sa 'cart.php' pagkatapos alisin ang item.
        exit(); 
        // Pinipigilan ang anumang karagdagang code na tumakbo pagkatapos ng redirect.
    } else {
        // Redirect to cart page if index is invalid or cart is empty
        header("Location: cart.php"); 
        // Kapag invalid ang index o walang laman ang cart, ire-redirect pa rin sa 'cart.php'.
        exit(); 
        // Pinipigilan ang karagdagang execution ng code.
    }
} else {
    // If no index is provided, redirect to the cart page
    header("Location: cart.php"); 
    // Kung walang 'index' parameter na ipinasa, ire-redirect sa 'cart.php'.
    exit(); 
    // Pinipigilan ang anumang karagdagang code execution.
}
?>
