<?php
// Include the database connection file
include('../includes/db.php');

// Start the session
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $phone = $_POST['phone'];
    $address = $_POST['address']; // New address field
    $role = 'user'; // Default role for users

    // Check if the email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Email already exists
        echo "<script>alert('Email is already registered!');</script>";
    } else {
        // Insert new user into the database
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, phone, address, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$username, $email, $password, $phone, $address, $role]);

        // Log the user in after successful registration
        $_SESSION['user_id'] = $conn->lastInsertId();
        header("Location: ../index.php"); // Redirect to the homepage
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-image: url('../images/logo.png'); /* Replace with your image path */
            background-size: cover;
            background-position: center;
            position: relative;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: inherit;
            filter: blur(10px);
            z-index: -1;
        }
        .signup-wrapper {
            display: flex;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            width: 800px;
            position: relative;
            z-index: 1;
        }
        .image-container {
            flex: 1;
            background-image: url('../images/logo.png');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #fff;
            text-align: center;
            padding: 20px;
            position: relative;
        }
        .signup-container {
            flex: 1;
            padding: 40px;
            text-align: center;
        }
        .signup-container h2 {
            margin-bottom: 20px;
            color: #444;
        }
        .input-container {
            position: relative;
            margin: 10px 0;
        }
        .input-container i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #001f3f;
        }
        .signup-container input {
            width: calc(100% - 40px);
            padding: 10px 10px 10px 40px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        .signup-container button {
            width: 100%;
            padding: 10px;
            background-color: #001f3f;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .signup-container button:hover {
            background-color: #003366;
        }
        .already-have-account {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }
        .already-have-account a {
            color: #001f3f;
            text-decoration: none;
        }
        .already-have-account a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="signup-wrapper">
        <div class="image-container"></div>
        <div class="signup-container">
            <h2>Sign Up</h2>
            <form action="signup.php" method="POST">
                <div class="input-container">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-container">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input-container">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="input-container">
                    <i class="fas fa-phone"></i>
                    <input type="tel" name="phone" placeholder="Phone Number" required>
                </div>
                <div class="input-container">
                    <i class="fas fa-map-marker-alt"></i> <!-- Icon for address -->
                    <input type="text" name="address" placeholder="Address" required> <!-- Address field -->
                </div>
                <button type="submit" name="register">Sign Up</button>
            </form>
            <div class="already-have-account">
                Already have an account? <a href="login.php">Log in</a>
            </div>
        </div>
    </div>
</body>
</html>