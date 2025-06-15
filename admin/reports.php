<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../includes/db.php';

// Date Filter
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

// Sales Report
$sales_stmt = $conn->prepare("
    SELECT DATE(created_at) AS date, SUM(total_amount) AS total_sales, COUNT(*) AS orders 
    FROM orders 
    WHERE created_at BETWEEN ? AND ? 
    GROUP BY DATE(created_at)
");
$sales_stmt->execute([$start_date, $end_date]);
$sales_data = $sales_stmt->fetchAll(PDO::FETCH_ASSOC);

// Popular Items
$items_stmt = $conn->query("
    SELECT m.item_name, SUM(oi.quantity) AS total_quantity 
    FROM order_items oi 
    JOIN menu m ON oi.item_id = m.item_id 
    GROUP BY m.item_name 
    ORDER BY total_quantity DESC 
    LIMIT 10
");
$popular_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Reports</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .container { width: 90%; margin: 20px auto; }
        .filter-form { margin-bottom: 20px; }
        .report-section { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        canvas { max-width: 800px; margin: 0 auto; }
        table { width: 100%; margin-top: 20px; }
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
    </style>
</head>
<body>
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


    <div class="container">
        <h2>Sales Reports</h2>

        <!-- Date Filter -->
        <div class="filter-form">
            <form method="GET">
                <input type="date" name="start_date" value="<?= $start_date ?>">
                <input type="date" name="end_date" value="<?= $end_date ?>">
                <button type="submit">Filter</button>
            </form>
        </div>

        <!-- Sales Chart -->
        <div class="report-section">
            <h3>Daily Sales (<?= $start_date ?> to <?= $end_date ?>)</h3>
            <canvas id="salesChart"></canvas>
        </div>

        <!-- Popular Items -->
        <div class="report-section">
            <h3>Most Popular Items</h3>
            <table>
                <tr>
                    <th>Item</th>
                    <th>Quantity Sold</th>
                </tr>
                <?php foreach ($popular_items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['item_name']) ?></td>
                    <td><?= $item['total_quantity'] ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <script>
        // Sales Chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column($sales_data, 'date')) ?>,
                datasets: [{
                    label: 'Daily Sales',
                    data: <?= json_encode(array_column($sales_data, 'total_sales')) ?>,
                    borderColor: '#4CAF50',
                    tension: 0.1
                }]
            },
            options: { responsive: true }
        });
    </script>
</body>
</html>