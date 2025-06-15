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

// Fetch Orders for the User
$orders = [];
$query = "
    SELECT o.order_id, o.total_amount, o.payment_method, o.order_status, o.created_at, o.upi_id, o.card_number
    FROM orders o
    WHERE o.user_id = :user_id
    ORDER BY o.created_at DESC
";
$stmt = $conn->prepare($query);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Order Items for Each Order
foreach ($orders as &$order) {
    $order_items_query = "
        SELECT oi.item_id, oi.quantity, oi.price, m.item_name, m.image_url
        FROM order_items oi
        JOIN menu m ON oi.item_id = m.item_id
        WHERE oi.order_id = :order_id
    ";
    $stmt = $conn->prepare($order_items_query);
    $stmt->bindValue(':order_id', $order['order_id'], PDO::PARAM_INT);
    $stmt->execute();
    $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>
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
            min-height: 40vh; 
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
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .order {
            border-bottom: 1px solid #ddd;
            padding: 20px 0;
        }
        .order:last-child {
            border-bottom: none;
        }
        .order p {
            margin: 5px 0;
        }
        .order-items {
            margin-top: 10px;
            padding-left: 20px;
        }
        .order-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .order-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            margin-right: 10px;
            border-radius: 4px;
        }
        .order-item div {
            flex-grow: 1;
        }

        #yourOrders {
            text-align: center;
        }

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
        <h1> Your Orders</h1>
        <p>check your orders</p>
    </div>
    <div class="container">
        <h1 id = "yourOrders">Your Orders</h1>

        <?php if (empty($orders)): ?>
            <p>You have no orders yet.</p>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order">
                    <p><strong>Order ID:</strong> <?= htmlspecialchars($order['order_id']) ?></p>
                    <p><strong>Total Amount:</strong> ₹<?= number_format($order['total_amount'], 2) ?></p>
                    <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($order['order_status']) ?></p>
                    <p><strong>Order Date:</strong> <?= htmlspecialchars($order['created_at']) ?></p>

                    <div class="order-items">
                        <h4>Items:</h4>
                        <?php foreach ($order['items'] as $item): ?>
                            <div class="order-item">
                                <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['item_name']) ?>">
                                <div>
                                    <p><strong><?= htmlspecialchars($item['item_name']) ?></strong></p>
                                    <p>Quantity: <?= htmlspecialchars($item['quantity']) ?></p>
                                    <p>Price: ₹<?= number_format($item['price'], 2) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

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