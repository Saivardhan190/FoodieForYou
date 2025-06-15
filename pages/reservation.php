<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Include your database connection file
include '../includes/db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['username'];
    $reservation_date = $_POST['reservation_date'];
    $party_size = $_POST['party_size'];
    $order_type = $_POST['order_type'];

    $errors = [];
    if (empty($reservation_date)) {
        $errors[] = "Reservation date is required.";
    }
    if (empty($party_size) || !is_numeric($party_size) || $party_size <= 0) {
        $errors[] = "Party size must be a positive number.";
    }
    if (!in_array($order_type, ['online', 'offline'])) {
        $errors[] = "Invalid order type.";
    }

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("INSERT INTO reservations (username, reservation_date, party_size, order_type, status) VALUES (?, ?, ?, ?, ?)");
            $status = 'pending';
            $stmt->execute([$username, $reservation_date, $party_size, $order_type, $status]);
            $success = "Your reservation has been submitted successfully!";
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Table</title>
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
            background-color: transparent; /* Make it transparent */
            padding: 10px 0px;
            color: white;
            position: absolute;
            width: 100%;
            z-index: 10;
            flex-wrap: wrap;
        }
            
        .logo {
            font-family: 'Times New Roman', Times, serif;
            font-weight: 500;
            padding-left: 30px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 80px;
            padding-right: 20px;
            margin-right: 15px;
        }

        .nav-links a {
            font-family: 'Times New Roman', Times, serif;
            color: white;
            text-decoration: none;
            font-size: 15px;
            font-weight: 800;
            transition: all var(--transition-speed);
            position: relative;
            display: inline-block; /* Add this */
            padding: 4px 0; /* Add padding for better hover area */ 
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--secondary-color);
            transform: scaleX(0); /* Use transform instead of width */
            transition: transform var(--transition-speed);
            transform-origin: bottom right;
        }

        .nav-links a:hover::after {
            transform: scaleX(1); /* Expand the underline */
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
            background: rgba(255, 255, 255, 0.2); /* Transparent white background */
            backdrop-filter: blur(5px); /* Apply background blur */
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
            padding: 200px 20px; /* Increase padding to cover navbar */
            color: white;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../images/reserve 2.jpg');
            background-size: cover;
            background-position: center;
            min-height: 40vh; /* Adjust height to cover navbar as well */
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

        /* Header */
        .header {
            text-align: center;
            margin: 100px auto 20px;
            padding: 20px;
            max-width: 800px;
        }

        .header h1 {
            font-size: 2.5rem;
            color: var(--primary-color);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .heading-line {
            width: 80px;
            height: 3px;
            background: var(--secondary-color);
            margin: 15px auto;
            border-radius: 3px;
        }

        /* Form Container */
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .container label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
            text-align: left;
        }

        .container input,
        .container select {
            width: 95%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .container button {
            background: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 8px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all var(--transition-speed);
            width: 30%;
            margin-top: 10px;
        }

        .container button:hover {
            background: rgba(125, 4, 149, 0.1);
            transform: scale(1.05);
        }

        .success {
            color: var(--success-color);
            font-weight: bold;
        }

        .error {
            color: red;
            font-weight: bold;
            list-style: none;
            padding: 0;
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 15px;
            }
            
            .nav-links {
                gap: 15px;
            }
            
            .nav-links a {
                font-size: 14px;
            }
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

  <div class="welcome-section">
    <h1>Reserve A Table </h1>
    <p>Good food, great company, and a table waiting just for you! Reserve yours today.</p>
    <div class="btn-container">
    </div>
</div>

<!-- Header (Outside Container) -->
<div class="header">
    <h1>Reserve a Table</h1>
    <div class="heading-line"></div>
    <p>"A table reserved just for you, where memories are made."</p>
</div>

<!-- Main Container -->
<div class="container">
    <div class="username-display">
        Logged in as: <?php echo htmlspecialchars($_SESSION['username']); ?>
    </div>

    <?php if (isset($success)): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <ul class="error">
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="reservation_date">Reservation Date and Time:</label>
        <input type="datetime-local" id="reservation_date" name="reservation_date" required>

        <label for="party_size">Party Size:</label>
        <input type="number" id="party_size" name="party_size" min="1" required>

        <label for="order_type">Order Type:</label>
        <select id="order_type" name="order_type" required>
            <option value="online">Online</option>
            <option value="offline">Offline</option>
        </select>

        <button type="submit">Submit Reservation</button>
    </form>
</div>
    <script>
    // JavaScript to toggle profile dropdown
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
