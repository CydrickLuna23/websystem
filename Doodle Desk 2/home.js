document.addEventListener("DOMContentLoaded", () => {
    const productView = document.getElementById("product-view");
    const viewImg = document.getElementById("view-img");
    const viewName = document.getElementById("view-name");
    const viewPrice = document.getElementById("view-price");
    const viewDescription = document.getElementById("view-description");
    const viewQuantity = document.getElementById("view-quantity");
    const closeView = document.getElementById("close-view");

    const productCards = document.querySelectorAll(".product-card");

    productCards.forEach(card => {
        card.addEventListener("click", () => {
            // Get product details from the card
            const productId = card.getAttribute("data-id");
            const img = card.querySelector("img").src;
            const name = card.querySelector("h2").innerText;
            const price = card.querySelector("p").innerText;
            const description = card.getAttribute("data-description");
            const quantity = card.getAttribute("data-quantity"); // Get the quantity

            // Update the view with product details
            viewImg.src = img;
            viewName.innerText = name;
            viewPrice.innerText = price;
            viewDescription.innerText = description;
            viewQuantity.innerText = "Available Quantity: " + quantity; // Display quantity

            // Set the product_id in the hidden input field
            document.getElementById("view-product-id").value = productId;

            // Show the product view modal
            productView.style.display = "flex";
        });
    });

    closeView.addEventListener("click", () => {
        // Hide the product view modal
        productView.style.display = "none";
    });

    window.addEventListener("click", (event) => {
        if (event.target === productView) {
            // Close the modal if the user clicks outside the content
            productView.style.display = "none";
        }
    });
});
