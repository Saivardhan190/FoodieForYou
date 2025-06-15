<?php
session_start();

// Include your database connection file
require_once '../includes/db.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Retrieve user_id from session
$user_id = $_SESSION['user_id'];

// Handle Add to Cart Action (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add Item to Cart
    if (isset($_POST['item_id'])) {
        $item_id = intval($_POST['item_id']);
        $quantity = intval($_POST['quantity']);

        // Validate input
        if ($item_id > 0 && $quantity > 0) {
            try {
                // Check if the item already exists in the cart
                $check_query = "SELECT * FROM cart WHERE user_id = :user_id AND item_id = :item_id";
                $stmt = $conn->prepare($check_query);
                $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindValue(':item_id', $item_id, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result) {
                    // Update quantity if item already exists
                    $new_quantity = $result['quantity'] + $quantity;
                    $update_query = "UPDATE cart SET quantity = :quantity WHERE cart_id = :cart_id";
                    $stmt = $conn->prepare($update_query);
                    $stmt->bindValue(':quantity', $new_quantity, PDO::PARAM_INT);
                    $stmt->bindValue(':cart_id', $result['cart_id'], PDO::PARAM_INT);
                    $stmt->execute();
                } else {
                    // Insert new item into the cart
                    $insert_query = "INSERT INTO cart (user_id, item_id, quantity) VALUES (:user_id, :item_id, :quantity)";
                    $stmt = $conn->prepare($insert_query);
                    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                    $stmt->bindValue(':item_id', $item_id, PDO::PARAM_INT);
                    $stmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
                    $stmt->execute();
                }

                // Return success response
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit;
            } catch (PDOException $e) {
                // Return error response
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
                exit;
            }
        } else {
            // Invalid input
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid input.']);
            exit;
        }
    }

    // Remove Item from Cart
    if (isset($_POST['remove_item_id'])) {
        $cart_id = intval($_POST['remove_item_id']);

        try {
            $delete_query = "DELETE FROM cart WHERE cart_id = :cart_id AND user_id = :user_id";
            $stmt = $conn->prepare($delete_query);
            $stmt->bindValue(':cart_id', $cart_id, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            // Return success response
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        } catch (PDOException $e) {
            // Return error response
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            exit;
        }
    }

    // Update Quantity
    if (isset($_POST['update_cart_id']) && isset($_POST['new_quantity'])) {
        $cart_id = intval($_POST['update_cart_id']);
        $new_quantity = intval($_POST['new_quantity']);

        if ($new_quantity > 0) {
            try {
                $update_query = "UPDATE cart SET quantity = :quantity WHERE cart_id = :cart_id AND user_id = :user_id";
                $stmt = $conn->prepare($update_query);
                $stmt->bindValue(':quantity', $new_quantity, PDO::PARAM_INT);
                $stmt->bindValue(':cart_id', $cart_id, PDO::PARAM_INT);
                $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();

                // Return success response
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit;
            } catch (PDOException $e) {
                // Return error response
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
                exit;
            }
        } else {
            // If quantity is <= 0, delete the item
            try {
                $delete_query = "DELETE FROM cart WHERE cart_id = :cart_id AND user_id = :user_id";
                $stmt = $conn->prepare($delete_query);
                $stmt->bindValue(':cart_id', $cart_id, PDO::PARAM_INT);
                $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();

                // Return success response
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit;
            } catch (PDOException $e) {
                // Return error response
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
                exit;
            }
        }
    }
}

// Fetch Cart Items
$cart_items = [];
$total = 0;

$query = "
    SELECT c.cart_id, c.item_id, c.quantity, m.item_name, m.price, m.image_url
    FROM cart c
    JOIN menu m ON c.item_id = m.item_id
    WHERE c.user_id = :user_id
";
$stmt = $conn->prepare($query);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $row) {
    $cart_items[] = $row;
    $total += $row['price'] * $row['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        :root {
            --primary-color: #7d0495;
            --secondary-color: #ff69b4;
            --background-color: #f8f9fa;
            --card-background: #ffffff;
            --text-primary: #2c3e50;
            --text-secondary: #666666;
            --success-color: #27ae60;
            --transition-speed: 0.3s;
        }

        /* Navbar Styles */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: transparent;
            padding: 10px 20px;
            color: white;
            position: absolute;
            width: 100%;
            z-index: 10;
            flex-wrap: wrap;
        }

        .logo {
            font-family: 'Times New Roman', Times, serif;
            font-weight: 500;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 90px;
            padding-right: 60px;
            margin-right: 0px;
        }

        .nav-links a {
            font-family: 'Times New Roman', Times, serif;
            color: white;
            text-decoration: none;
            font-size: 15px;
            font-weight: 800;
            transition: all var(--transition-speed);
            position: relative;
            display: inline-block;
            padding: 5px 0;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--secondary-color);
            transform: scaleX(0);
            transition: transform var(--transition-speed);
            transform-origin: bottom right;
        }

        .nav-links a:hover::after {
            transform: scaleX(1);
            transform-origin: bottom left;
        }

        .cart-icon {
            position: relative;
            font-size: 20px;
            cursor: pointer;
        }

        .profile-dropdown {
            position: relative;
            text-align: center;
            padding-right: 40px;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 23px;
            top: 30px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 0px;
            min-width: 190px;
            z-index: 1;
            text-align: center;
            padding-bottom: 10px;
        }

        .dropdown-content a {
            display: block;
            padding: 8px 0px;
            color: #fff;
            text-decoration: none;
            font-weight: bolder;
            text-align: center;
            width: 100%;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .dropdown-content.show {
            display: block;
        }

        @keyframes dropdown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 15px;
            }

            .search-bar input {
                width: 200px;
            }

            .search-bar input:focus {
                width: 250px;
            }

            .nav-links {
                gap: 15px;
            }

            .nav-links a {
                font-size: 14px;
            }
        }

        @media (max-width: 576px) {
            .search-bar {
                display: none;
            }
        }

        /* Cart Heading Styles */
        .welcome-section {
            text-align: center;
            padding: 150px 10px; /* Increase padding to cover navbar */
            color: white;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../images/welcome.jpg');
            background-size: cover;
            background-position: center;
            min-height: 60vh; /* Adjust this value as needed */
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative; /* Add relative positioning */
        }

        /* Pseudo-element to extend the background */
        .welcome-section::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100px; /* Adjust this value to control the extension */
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../images/welcome.jpg');
            background-size: cover;
            background-position: center;
            z-index: -1; /* Ensure it stays behind the content */
        }

        /* Cart Container Styles */
        .container {
            max-width: 800px; /* Adjusted width */
            width: 90%; /* Ensures responsiveness */
            min-height: 400px; /* Adjusted height */
            margin: -100px auto 40px; /* Move the container up */
            padding: 60px;
            background-color: var(--card-background); /* Solid background */
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative; /* Add relative positioning */
            z-index: 1; /* Ensure it appears above the welcome section */
        }

        .container:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
        }

        .cart-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .cart-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin-right: 15px;
            border-radius: 8px;
        }

        .cart-item div {
            flex-grow: 1;
        }

        .cart-item p {
            margin: 5px 0;
        }

        .cart-total {
            text-align: right;
            font-size: 1.2em;
            font-weight: bold;
            margin-top: 20px;
        }

        .proceed-to-pay {
            display: block;
            width: 100%;
            padding: 15px;
            background-color: rgb(40, 43, 243);
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }

        .proceed-to-pay:hover {
            background-color: rgb(7, 16, 187);
        }

        .empty-cart {
            text-align: center;
            font-size: 1.2em;
            color: #888;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
        }

        .quantity-controls button {
            background-color: transparent; /* Remove background */
            color: var(--primary-color); /* Use primary color for text */
            border: 1px solid var(--primary-color); /* Add border */
            padding: 5px 10px;
            cursor: pointer;
            font-size: 1em;
            margin: 0 5px;
            border-radius: 5px;
            transition: all var(--transition-speed);
        }

        .quantity-controls button:hover {
            background-color: var(--primary-color); /* Add background on hover */
            color: white; /* Change text color on hover */
        }

        /* Keep background for other buttons */
        .remove-item {
            background-color: rgb(40, 43, 243);
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            font-size: 1em;
            margin-left: 10px;
            border-radius: 5px;
            transition: background-color var(--transition-speed);
        }

        .remove-item:hover {
            background-color: rgb(0, 3, 159);
        }

        /* Contact Us Section */
        .contact-us {
            width: 100%;
            padding: 60px 20px;
            background-color: black;
            color: white;
            text-align: center;
            margin: 0px 0;
        }

        .contact-us h2 {
            font-size: 2.5rem;
            margin-bottom: 30px;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .contact-details {
            max-width: 600px;
            margin: 0 auto 0px;
        }

        .contact-details p {
            font-size: 1.1rem;
            margin: 15px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .contact-details i {
            font-size: 1.3rem;
            color: white;
        }

        .social-media {
            display: flex;
            justify-content: center;
            gap: 25px;
            margin-top: 20px;
        }

        .social-media a {
            color: white;
            font-size: 1.8rem;
            transition: all var(--transition-speed);
        }

        .social-media a:hover {
            color: rgba(255, 255, 255, 0.8);
            transform: translateY(-3px);
        }

        @media (max-width: 768px) {
            .contact-us h2 {
                font-size: 2rem;
            }
            
            .contact-details p {
                font-size: 1rem;
                flex-direction: column;
            }
        }

        /* Footer Styles */
        footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 0px;
        }

        footer p {
            margin: 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <p>Foodie</p>
        </div>
        <div class="nav-links">
            <a href="../index.php">Home</a>
            <a href="../pages/menu.php">Menu</a>
            <a href="../pages/reservation.php">Reservation</a>
            <a href="../pages/about.php">About</a>
            <a href="../pages/cart.php" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
            </a>
            <?php if (isset($_SESSION['username'])): ?>
                <div class="profile-dropdown">
                    <a href="#">
                        <i class="fas fa-user"></i>
                    </a>
                    <div class="dropdown-content">
                        <p><?php echo htmlspecialchars($_SESSION['username']); ?></p><hr>
                        <a href="../pages/cart.php">Cart</a>
                        <a href="../pages/orders.php">Orders</a>
                        <a href="../pages/logout.php">Log Out</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="login-link">
                    <a href=".../pages/login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">Login</a>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Cart Heading Section -->
    <div class="welcome-section">
        <h1><i class="fas fa-shopping-cart"></i> Your Cart</h1>
        <p>Review and manage your items</p>
    </div>
<br><br>
    <div class="container">
        <?php if (empty($cart_items)): ?>
            <p class="empty-cart">Your cart is empty.</p>
        <?php else: ?>
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item" data-cart-id="<?= $item['cart_id'] ?>">
                    <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['item_name']) ?>">
                    <div>
                        <p><strong><?= htmlspecialchars($item['item_name']) ?></strong></p>
                        <p>₹<?= number_format($item['price'], 2) ?></p>
                        <div class="quantity-controls">
                            <button class="decrease-quantity" onclick="updateQuantity(<?= $item['cart_id'] ?>, -1)">-</button>
                            <span class="quantity"><?= $item['quantity'] ?></span>
                            <button class="increase-quantity" onclick="updateQuantity(<?= $item['cart_id'] ?>, 1)">+</button>
                            <button class="remove-item" onclick="removeItem(<?= $item['cart_id'] ?>)">Remove</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="cart-total">
                Total: ₹<?= number_format($total, 2) ?>
            </div>

            <a href="payment.php" class="proceed-to-pay">Proceed to Pay</a>
        <?php endif; ?>
    </div>

    <!-- Contact Us Section -->
<section class="contact-us">
    <h2>Contact Us</h2>
    <div class="contact-details">
        <p><i class="fas fa-phone"></i> Phone: +1 (123) 456-7890</p>
        <p><i class="fas fa-envelope"></i> Email: support@example.com</p>
        <p><i class="fas fa-map-marker-alt"></i> Address: 123 Food Street, City, Country</p>
    </div>
    <div class="social-media">
        <a href="https://facebook.com" target="_blank"><i class="fab fa-facebook-f"></i></a>
        <a href="https://twitter.com" target="_blank"><i class="fab fa-twitter"></i></a>
        <a href="https://instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
        <a href="https://linkedin.com" target="_blank"><i class="fab fa-linkedin-in"></i></a>
    </div>
</section>

<!-- Footer -->
<footer>
    <p>&copy; 2023 Your Restaurant Name. All rights reserved.</p>
</footer>

    <script>
        function updateQuantity(cartId, change) {
            const cartItem = document.querySelector(`.cart-item[data-cart-id="${cartId}"]`);
            const quantityElement = cartItem.querySelector('.quantity');
            let currentQuantity = parseInt(quantityElement.textContent);

            // Calculate new quantity
            let newQuantity = currentQuantity + change;
            if (newQuantity <= 0) {
                newQuantity = 1; // Prevent quantity from going below 1
            }

            // Send AJAX request to update quantity
            fetch('cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `update_cart_id=${cartId}&new_quantity=${newQuantity}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the displayed quantity
                    quantityElement.textContent = newQuantity;
                    location.reload(); // Reload the page to reflect total changes
                } else {
                    alert('Failed to update quantity.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the quantity.');
            });
        }

        function removeItem(cartId) {
            if (confirm('Are you sure you want to remove this item?')) {
                // Send AJAX request to remove item
                fetch('cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `remove_item_id=${cartId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the cart item from the DOM
                        const cartItem = document.querySelector(`.cart-item[data-cart-id="${cartId}"]`);
                        cartItem.remove();
                        location.reload(); // Reload the page to reflect total changes
                    } else {
                        alert('Failed to remove item.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while removing the item.');
                });
            }
        }
        document.querySelector('.profile-dropdown a').addEventListener('click', function(e) {
            e.preventDefault();
            const dropdown = document.querySelector('.dropdown-content');
            dropdown.classList.toggle('show');
        });

    // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdown = document.querySelector('.dropdown-content');
            if (!e.target.closest('.profile-dropdown')) {
                dropdown.classList.remove('show');
            }
        });
    </script>
</body>
</html>