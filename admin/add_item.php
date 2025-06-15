<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Include your database connection file
include '../includes/db.php'; // Replace with your actual file name

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];

    // Handle image upload
    $image_url = ''; // Default value if no image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../images/"; // Ensure this directory exists and is writable
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true); // Create the directory if it doesn't exist
        }
        $target_file = $target_dir . basename($_FILES['image']['name']);

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_url = $target_file;
        } else {
            $error = "Error uploading image.";
        }
    }

    // Insert into the `menu` table
    if (!isset($error)) {
        // Update the SQL query to use `item_name` instead of `name`
        $sql = "INSERT INTO menu (item_name, description, price, category, image_url) VALUES (:item_name, :description, :price, :category, :image_url)";
        $stmt = $conn->prepare($sql);

        // Bind parameters using PDO
        $stmt->bindValue(':item_name', $name); // Use `item_name` here
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':price', $price);
        $stmt->bindValue(':category', $category);
        $stmt->bindValue(':image_url', $image_url);

        if ($stmt->execute()) {
            $success = "Item added successfully!";
        } else {
            $error = "Error adding item: " . implode(" ", $stmt->errorInfo());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Food Item</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
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

        .container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 24px;
            text-align: center;
            font-size: 28px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 4px;
            display: block;
        }

        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="file"] {
            padding: 10px 0;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }

        button {
            background-color: #3498db;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 8px;
        }

        button:hover {
            background-color: #2980b9;
        }

        .success {
            color: #27ae60;
            background-color: #d4edda;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 16px;
        }

        .error {
            color: #c0392b;
            background-color: #f8d7da;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 16px;
        }

        @media (max-width: 768px) {
            .container {
                margin: 20px;
                padding: 16px;
            }

            h2 {
                font-size: 24px;
            }
        }

        /* Style for the dashboard link */
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
        <h2>Add Food Item</h2>
        <?php if (isset($success)): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="add_item.php" method="POST" enctype="multipart/form-data">
            <div>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div>
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>
            </div>

            <div>
                <label for="price">Price:</label>
                <input type="number" id="price" name="price" step="0.01" required>
            </div>

            <div>
                <label for="category">Category:</label>
                <input type="text" id="category" name="category" required>
            </div>

            <div>
                <label for="image">Image:</label>
                <input type="file" id="image" name="image">
            </div>

            <button type="submit">Add Item</button>
        </form>
    </div>
</body>
</html>