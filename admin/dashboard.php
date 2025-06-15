<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Include the database connection file
require_once __DIR__ . '/../includes/db.php';

// Debugging: Check if $conn is set
if (!isset($conn)) {
    die('Database connection not established!');
}

// Fetch admin details (example)
$admin_name = "Admin"; // Replace with dynamic data from the database

// Fetch total orders
try {
    $stmt = $conn->query("SELECT COUNT(*) AS total_orders FROM orders");
    $total_orders = $stmt->fetchColumn();
} catch (PDOException $e) {
    die("Error fetching total orders: " . $e->getMessage());
}

// Fetch total revenue (only for confirmed orders)
try {
    $stmt = $conn->query("SELECT COALESCE(SUM(total_amount), 0) AS total_revenue FROM orders WHERE order_status = 'Confirmed'");
    $total_revenue = $stmt->fetchColumn();
} catch (PDOException $e) {
    die("Error fetching total revenue: " . $e->getMessage());
}

// Fetch total customers (distinct usernames)
try {
    $stmt = $conn->query("SELECT COUNT(DISTINCT username) AS total_customers FROM orders");
    $total_customers = $stmt->fetchColumn();
} catch (PDOException $e) {
    die("Error fetching total customers: " . $e->getMessage());
}

// Fetch pending orders
try {
    $stmt = $conn->query("SELECT COUNT(*) AS pending_orders FROM orders WHERE order_status = 'Pending'");
    $pending_orders = $stmt->fetchColumn();
} catch (PDOException $e) {
    die("Error fetching pending orders: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        /* Your existing CSS styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            padding: 10px 20px;
            color: white;
        }

        .navbar .logo {
            display: flex;
            align-items: center;
        }

        .navbar .logo img {
            height: 40px;
            margin-right: 10px;
            border-radius: 5px;
        }

        .navbar .logo span {
            font-size: 25px;
            font-weight: bold;
        }

        .navbar .profile {
            position: relative;
        }

        .navbar .profile-icon {
            cursor: pointer;
            font-size: 24px;
            background-color: #fff;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
            transition: background-color 0.3s ease;
        }

        .navbar .profile-icon:hover {
            background-color: #fff;
        }

        .navbar .profile-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: 50px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 20px;
            min-width: 200px;
            z-index: 1;
            border: 4px solid #ddd;
        }

        .navbar .profile-dropdown a {
            display: block;
            padding: 8px 12px;
            color: #333;
            text-decoration: none;
        }

        .navbar .profile-dropdown a:hover {
            background-color: #001f3f;
        }

        .navbar .profile:hover .profile-dropdown {
            display: block;
        }

        .container {
            width: 90%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .dashboard-card {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        .dashboard-card h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }

        .dashboard-card p {
            font-size: 24px;
            font-weight: bold;
            color: #4CAF50;
            margin: 10px 0;
        }

        nav {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        nav a {
            text-decoration: none;
            padding: 12px 25px;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        nav a:hover {
            background-color: #45a049;
        }

        .logout {
            background-color: #f44336;
        }

        .logout:hover {
            background-color: #e53935;
        }

        footer {
            text-align: center;
            margin-top: 50px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">
            <img src="../images/logo.png" alt="Restaurant Logo">
            <span>Foodie</span>
        </div>
        <div class="profile">
            <div class="profile-icon">👤</div>
            <div class="profile-dropdown">
                <a href="#"><?php echo $admin_name; ?></a><hr>
                <a href="logout.php" class="logout">Logout</a>
            </div>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="container">
        <h2>Admin Dashboard</h2>

        <!-- Key Metrics Cards -->
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3>Total Orders</h3>
                <p><?php echo $total_orders; ?></p>
            </div>
            <div class="dashboard-card">
                <h3>Total Revenue</h3>
                <p>₹<?php echo number_format($total_revenue, 2); ?></p>
            </div>
            <div class="dashboard-card">
                <h3>Active Customers</h3>
                <p><?php echo $total_customers; ?></p>
            </div>
            <div class="dashboard-card">
                <h3>Pending Orders</h3>
                <p><?php echo $pending_orders; ?></p>
            </div>
        </div>

        <!-- Quick Links -->
        <nav>
            <a href="add_item.php">Add Food Item</a>
            <a href="manage_item.php">Manage Menu</a>
            <a href="view_orders.php">View Orders</a>
            <a href="manage_reservation.php">Manage Reservations</a>
            <a href="manage_staff.php">Manage Staff</a>
            <a href="reports.php">View Reports</a>
        </nav>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Foodie Admin Dashboard</p>
    </footer>

    <script>
        // JavaScript to toggle profile dropdown
        document.querySelector('.profile-icon').addEventListener('click', function() {
            const dropdown = document.querySelector('.profile-dropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        });
    </script>
</body>
</html>