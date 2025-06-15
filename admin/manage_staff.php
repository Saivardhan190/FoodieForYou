<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../includes/db.php';

// Add New Staff
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_staff'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO staff (name, email, role) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $role]);
}

// Delete Staff
if (isset($_GET['delete_staff'])) {
    $staff_id = $_GET['delete_staff'];
    $stmt = $conn->prepare("DELETE FROM staff WHERE staff_id = ?");
    $stmt->execute([$staff_id]);
    header("Location: manage_staff.php");
    exit();
}

// Fetch All Staff
$stmt = $conn->query("SELECT * FROM staff");
$staff = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff</title>
    <style>
        /* Inherit styles from admin dashboard */
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { width: 90%; margin: 20px auto; background-color: #fff; padding: 20px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f9f9f9; }
        .action-btns a { padding: 5px 10px; margin: 0 5px; text-decoration: none; border-radius: 4px; }
        .edit-btn { background-color: #4CAF50; color: white; }
        .delete-btn { background-color: #f44336; color: white; }
        .add-staff-form { margin-bottom: 30px; }
        input[type="text"], input[type="email"] { padding: 8px; width: 250px; }
        button { padding: 8px 15px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; }
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
     <!-- Reuse admin navbar -->
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
        <h2>Manage Staff</h2>

        <!-- Add Staff Form -->
        <div class="add-staff-form">
            <form method="POST">
                <input type="text" name="name" placeholder="Staff Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <select name="role" required>
                    <option value="Manager">Manager</option>
                    <option value="Chef">Chef</option>
                    <option value="Waiter">Waiter</option>
                </select>
                <button type="submit" name="add_staff">Add Staff</button>
            </form>
        </div>

        <!-- Staff List Table -->
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($staff as $member): ?>
            <tr>
                <td><?= $member['staff_id'] ?></td>
                <td><?= htmlspecialchars($member['name']) ?></td>
                <td><?= htmlspecialchars($member['email']) ?></td>
                <td><?= htmlspecialchars($member['role']) ?></td>
                <td class="action-btns">
                    <a href="edit_staff.php?id=<?= $member['staff_id'] ?>" class="edit-btn">Edit</a>
                    <a href="manage_staff.php?delete_staff=<?= $member['staff_id'] ?>" class="delete-btn" onclick="return confirm('Delete this staff member?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>