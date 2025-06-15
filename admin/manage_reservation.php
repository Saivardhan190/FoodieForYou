<?php
session_start();
// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Include your database connection file
include '../includes/db.php';

// Handle status update
if (isset($_POST['update_status'])) {
    $reservation_id = $_POST['reservation_id'];
    $new_status = $_POST['status'];

    // Validate the input
    if (!is_numeric($reservation_id) || !in_array($new_status, ['pending', 'confirmed', 'cancelled'])) {
        $error = "Invalid input!";
    } else {
        // Prepare and execute the update query
        $stmt = $conn->prepare("UPDATE reservations SET status = ? WHERE reservation_id = ?");
        $stmt->bindParam(1, $new_status, PDO::PARAM_STR);
        $stmt->bindParam(2, $reservation_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $success = "Reservation status updated successfully!";
        } else {
            $error = "Error updating reservation status.";
        }
    }
}

// Fetch all reservations
$query = "SELECT reservation_id, username, reservation_date, party_size, order_type, status, created_at FROM reservations";
$result = $conn->query($query);

// Check for errors in fetching data
if (!$result) {
    $error = "Error fetching reservations: " . $conn->errorInfo()[2];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reservations</title>
    <style>
        /* Add your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
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
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .action-form {
            display: inline-block;
        }
        .success {
            color: green;
            text-align: center;
        }
        .error {
            color: red;
            text-align: center;
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
        <h2>Manage Reservations</h2>
        <?php if (isset($success)): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Reservation Date</th>
                    <th>Party Size</th>
                    <th>Order Type</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->rowCount() > 0): ?>
                    <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['reservation_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['reservation_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['party_size']); ?></td>
                            <td><?php echo htmlspecialchars($row['order_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <!-- Status Update Form -->
                                <form method="POST" class="action-form">
                                    <input type="hidden" name="reservation_id" value="<?php echo $row['reservation_id']; ?>">
                                    <select name="status">
                                        <option value="pending" <?php echo ($row['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo ($row['status'] === 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="cancelled" <?php echo ($row['status'] === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_status">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center;">No reservations found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>