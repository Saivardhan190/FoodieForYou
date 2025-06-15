<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
// Include your database connection file
include '../includes/db.php'; // Replace with your actual file name

// Handle item deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    // Validate the ID (ensure it's a number)
    if (!is_numeric($delete_id)) {
        $error = "Invalid item ID!";
    } else {
        // Prepare and execute the delete query
        $stmt = $conn->prepare("DELETE FROM menu WHERE item_id = ?");
        $stmt->bindParam(1, $delete_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $success = "Item deleted successfully!";
        } else {
            $error = "Error deleting item.";
        }
    }
}

// Fetch all menu items
$query = "SELECT item_id, item_name, description, price, category, image_url FROM menu"; // Use correct column names
$result = $conn->query($query);

// Check for errors in fetching data
if (!$result) {
    $error = "Error fetching menu items: " . $conn->errorInfo()[2];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Menu</title>
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
        img {
            max-width: 50px;
            border-radius: 5px;
        }
        .action-link {
            color: #fff;
            background-color: #f44336;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .action-link:hover {
            background-color: #e53935;
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
        <h2>Manage Menu</h2>
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
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->rowCount() > 0): ?>
                    <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['item_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td>$<?php echo htmlspecialchars($row['price']); ?></td>
                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                            <td>
                                <?php if (isset($row['image_url']) && !empty($row['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['item_name']); ?>">
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="manage_item.php?delete_id=<?php echo $row['item_id']; ?>" class="action-link" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">No menu items found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>