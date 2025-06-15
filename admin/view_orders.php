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

// Handle order confirmation
if (isset($_GET['confirm_order'])) {
    $order_id = intval($_GET['confirm_order']);
    if ($order_id > 0) {
        // Update order status in the `orders` table
        $stmt = $conn->prepare("UPDATE orders SET order_status = 'Confirmed' WHERE order_id = :order_id");
        $stmt->execute(['order_id' => $order_id]);
        if ($stmt->rowCount() > 0) {
            $success_message = "Order #$order_id has been confirmed!";
        } else {
            $error_message = "Failed to confirm order #$order_id.";
        }
    }
}

// Handle order cancellation
if (isset($_GET['cancel_order'])) {
    $order_id = intval($_GET['cancel_order']);
    if ($order_id > 0) {
        // Update order status in the `orders` table
        $stmt = $conn->prepare("UPDATE orders SET order_status = 'Cancelled' WHERE order_id = :order_id");
        $stmt->execute(['order_id' => $order_id]);
        if ($stmt->rowCount() > 0) {
            $success_message = "Order #$order_id has been cancelled!";
        } else {
            $error_message = "Failed to cancel order #$order_id.";
        }
    }
}

// Fetch all orders with payment details
try {
    $query = "
        SELECT o.order_id, o.username, o.total_amount, o.payment_method, o.order_status, o.created_at,
               p.payment_id, p.payment_date
        FROM orders o
        LEFT JOIN payments p ON o.order_id = p.order_id
        ORDER BY o.created_at DESC
    ";
    $stmt = $conn->query($query);

    if ($stmt === false) {
        throw new Exception("Failed to execute query: " . implode(" ", $conn->errorInfo()));
    }

    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($orders)) {
        $error_message = "No orders found in the database.";
    }
} catch (PDOException $e) {
    die("Error fetching orders: " . $e->getMessage());
} catch (Exception $e) {
    die($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>
    <style>
        /* Your CSS styles here */
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

        .dashboard-link {
            font-family: 'Times New Roman', Times, serif;
            color: white;
            text-decoration: none;
            font-size: 15px;
            font-weight: 800;
            transition: all var(--transition-speed);
            position: relative;
            display: inline-block;
            padding: 5px 0;
            margin-right: 30px; /* Add some spacing between dashboard and profile */
        }

        .dashboard-link::after {
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

        .dashboard-link:hover::after {
            transform: scaleX(1);
            transform-origin: bottom left;
        }

        /* Adjust the nav-links spacing */
        .nav-links {
            display: flex;
            align-items: center;
            gap: 30px; 
            padding-right: 30px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .status-pending {
            color: #f44336;
            font-weight: bold;
        }

        .status-confirmed {
            color: #4CAF50;
            font-weight: bold;
        }

        .status-cancelled {
            color: #ff9800;
            font-weight: bold;
        }

        .confirm-btn, .cancel-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        .confirm-btn {
            background-color: #4CAF50;
            color: white;
        }

        .confirm-btn:hover {
            background-color: #45a049;
        }

        .cancel-btn {
            background-color: #f44336;
            color: white;
        }

        .cancel-btn:hover {
            background-color: #e53935;
        }

        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
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
        
        <div class="nav-links">
            <!-- Add dashboard link before profile -->
            <a href="dashboard.php" class="dashboard-link">Dashboard</a>
            
            <div class="profile">
                <div class="profile-icon">👤</div>
                <div class="profile-dropdown">
                    <a href="dashboard.php">Dashboard</a>
                    <a href="logout.php" class="logout">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="container">
        <h2>View Orders</h2>

        <!-- Display success/error messages -->
        <?php if (isset($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Order Date</th>
                    <th>Total Amount</th>
                    <th>Payment Method</th>
                    <th>Payment ID</th>
                    <th>Payment Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo $order['order_id']; ?></td>
                            <td><?php echo htmlspecialchars($order['username']); ?></td>
                            <td><?php echo $order['created_at']; ?></td>
                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                            <td><?php echo $order['payment_id']; ?></td>
                            <td><?php echo $order['payment_date']; ?></td>
                            <td>
                                <?php if ($order['order_status'] == 'Pending'): ?>
                                    <span class="status-pending">Pending</span>
                                <?php elseif ($order['order_status'] == 'Confirmed'): ?>
                                    <span class="status-confirmed">Confirmed</span>
                                <?php else: ?>
                                    <span class="status-cancelled">Cancelled</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($order['order_status'] == 'Pending'): ?>
                                    <a href="view_orders.php?confirm_order=<?php echo $order['order_id']; ?>" class="confirm-btn">Confirm</a>
                                    <a href="view_orders.php?cancel_order=<?php echo $order['order_id']; ?>" class="cancel-btn">Cancel</a>
                                <?php else: ?>
                                    <span>No Action</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align: center;">No orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Foodie Admin Dashboard</p>
    </footer>
</body>
</html>