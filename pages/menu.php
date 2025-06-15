<?php
session_start();
// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
// Include your database connection file
include '../includes/db.php';
// Check if the connection was successful
if (!isset($conn)) {
    die("Database connection failed.");
}
try {
    // Fetch all menu items using PDO
    $query = "SELECT * FROM menu ORDER BY category, item_name";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    // Group items by category
    $menuItems = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $category = $row['category'];
        if (!isset($menuItems[$category])) {
            $menuItems[$category] = [];
        }
        $menuItems[$category][] = $row;
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Modern Menu Styles */
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

        /* Welcome Section */
        .welcome-section {
            text-align: center;
            padding: 200px 20px;
            color: white;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../images/signature.jpg');
            background-size: cover;
            background-position: center;
            min-height: 40vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .welcome-section h1 {
            font-size: 70px;
            margin: 0;
        }

        .welcome-section p {
            font-size: 24px;
            margin: 10px 0;
        }

        .btn-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .welcome-section .btn {
            background: transparent;
            border: 2px solid white;
            padding: 12px 30px;
            font-size: 18px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .welcome-section .btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        /* Menu Section Styles */
        .menu-section {
            text-align: center;
            margin: 50px 0;
            padding: 0 20px;
        }

        .menu-section h2 {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .menu-section p {
            color: var(--text-secondary);
            font-size: 1rem;
            margin-bottom: 20px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Menu Container Styles */
        .menu-container {
            max-width: 1200px;
            width: 90%;
            margin: 20px auto;
            padding: 20px;
        }

        /* Category Heading Styles */
        .category-heading {
            font-size: 1.5rem;
            color: var(--primary-color);
            margin: 20px 0 10px;
            grid-column: 1 / -1;
        }

        /* Menu Grid Styles */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* 4 items per row */
            gap: 30px; /* Adjust the gap between items */
            width: 100%; /* Full width */
            margin-bottom: 40px;
        }

        /* Menu Item Styles */
        .menu-item {
            background: var(--card-background);
            border-radius: 10px;
            padding: 10px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all var(--transition-speed);
            position: relative;
            overflow: hidden;
            width: 100%;
        }

        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .menu-item img {
            width: 90%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: transform var(--transition-speed);
        }

        .menu-item:hover img {
            transform: scale(1.05);
        }

        .menu-item h3 {
            margin: 10px 0;
            font-size: 18px;
            color: var(--text-primary);
            font-weight: 600;
        }

        .menu-item .description {
            margin: 8px 0;
            color: var(--text-secondary);
            line-height: 1.4;
            font-size: 13px;
            height: 40px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .menu-item .price {
            font-size: 20px;
            font-weight: 700;
            color: var(--success-color);
            margin: 10px 0;
        }

        .menu-item .category {
            display: inline-block;
            padding: 5px 10px;
            background-color: rgba(125, 4, 149, 0.1);
            color: var(--primary-color);
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
            margin-top: 8px;
        }

        .category-heading {
            font-size: 2.5rem;
            text-align: center;
            color: var(--primary-color);
            margin: 20px 0 10px;
            grid-column: 1 / -1;
            border-bottom: 6px solid var(--secondary-color); /* Underline with color */
            padding-bottom: 20px; /* Space between text and underline */
        }

        .add-to-cart-btn {
            background: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 8px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all var(--transition-speed);
            width: 80%;
            margin-top: 10px;
        }

        .add-to-cart-btn:hover {
            background: rgba(125, 4, 149, 0.1);
            transform: scale(1.05);
        }

        /* Footer Styles */
        .footer {
            background: #333;
            color: white;
            padding: 40px 20px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .footer div {
            flex: 1;
            min-width: 200px;
        }

        .footer h4 {
            font-size: 1.2rem;
            margin-bottom: 15px;
        }

        .footer p {
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .footer .subscribe input {
            padding: 10px;
            border: none;
            border-radius: 5px;
            width: 100%;
            margin-bottom: 10px;
        }

        .footer .subscribe i {
            font-size: 1.5rem;
            cursor: pointer;
        }

        .footer .copyright {
            text-align: center;
            width: 100%;
            margin-top: 20px;
            font-size: 0.9rem;
        }

        .footer .copyright i {
            margin: 0 10px;
            cursor: pointer;
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

<!-- Welcome Section -->
<div class="welcome-section">
    <h1>Our Menu</h1>
    <p>Discover our delicious range of dishes crafted with love and passion.</p>
    <div class="btn-container">
    </div>
</div>

<!-- Menu Section -->
<div class="menu-section">
    <p>At FOODIE , we believe that food is more than just sustenance—it’s an experience. Our carefully crafted menu reflects our passion for delivering exceptional flavors, fresh ingredients, and unforgettable dining moments.</p>
</div>

<!-- Menu Container -->
<div class="menu-container">
    <?php foreach ($menuItems as $category => $items): ?>
        <!-- Category Heading -->
        <h2 class="category-heading"><?php echo htmlspecialchars(ucfirst($category)); ?></h2>
        
        <!-- Grid for Menu Items -->
        <div class="menu-grid">
            <?php foreach ($items as $item): ?>
                <div class="menu-item">
                    <?php if (!empty($item['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                            alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($item['item_name']); ?></h3>
                    <p class="description"><?php echo htmlspecialchars($item['description']); ?></p>
                    <p class="price">₹<?php echo number_format($item['price'], 2); ?></p>
                    <span class="category"><?php echo htmlspecialchars($item['category']); ?></span><br><br>
                    
                    <!-- Modified Add to Cart Form -->
                    <form method="POST" class="add-to-cart-form">
                        <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="button" class="add-to-cart-btn" onclick="addToCart(this)">Add to Cart</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>

<!-- Footer -->
<footer class="footer">
    <div>
        <img class="diviLogo" src="images/logo.png" alt="Foodie Logo">
        <p class="tagline">
            Just like a perfectly baked pastry, we created Foodie with special love and it shows.
        </p>
    </div>
    <div>
        <h4>Open Hours</h4>
        <p>Mon-Fri: 9 AM - 6 PM</p>
        <p>Saturday: 9 AM - 4 PM</p>
        <p>Sunday: Closed</p>
    </div>
    <div>
        <h4>Contact Us</h4>
        <p>176 W street name, New York, NY 10014</p>
        <p>Email: info@foodie.com</p>
        <p>Telephone: +1(800)123-4566</p>
    </div>
    <div class="subscribe">
        <h4>Subscribe Newsletter</h4>
        <input type="email" name="email" id="email" placeholder="Email Address">
        <i class="fas fa-paper-plane fa-2x"></i>
    </div>
    <div class="copyright">
        <span>2023&#169; Foodie &#124; Design by Your Name</span>
        <i class="fab fa-facebook-f fa-2x"></i>
        <i class="fab fa-twitter fa-2x"></i>
        <i class="linkedIn fab fa-linkedin-in"></i>
        <i class="fab fa-instagram fa-2x"></i>
    </div>
</footer>

<script>
    // JavaScript to toggle profile dropdown
    document.addEventListener("DOMContentLoaded", function () {
        const profileLink = document.querySelector('.profile-dropdown a');
        if (profileLink) {
            profileLink.addEventListener('click', function(e) {
                e.preventDefault();
                const dropdown = document.querySelector('.dropdown-content');
                dropdown.classList.toggle('show');
            });

            document.addEventListener('click', function(e) {
                const dropdown = document.querySelector('.dropdown-content');
                if (dropdown && !e.target.closest('.profile-dropdown')) {
                    dropdown.classList.remove('show');
                }
            });
        }
    });

    function addToCart(button) {
        // Get the form data
        const form = button.closest('.add-to-cart-form');
        const formData = new FormData(form);

        // Send an AJAX request to cart.php
        fetch('cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok.');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Item added to cart!');
            } else {
                alert('Failed to add item to cart: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding the item to the cart.');
        });
    }
</script>
</body>
</html>