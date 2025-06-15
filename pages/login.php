<?php
session_start();

// Database connection
$host = 'localhost'; // or your host
$dbname = 'restaurant';
$user = 'root';
$pass = '';
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Retrieve user from the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Validate credentials
    if ($user && password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect to the intended page or home page
        $redirect_url = isset($_GET['redirect']) ? urldecode($_GET['redirect']) : '../index.php';
        header("Location: $redirect_url");
        exit();
    } else {
        // Set error message
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
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
        .login-wrapper {
            display: flex;
            background-color: rgba(255, 255, 255, 0.9); /* Slightly transparent white */
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3); /* Increased shadow */
            overflow: hidden;
            width: 800px; /* Adjust width as needed */
            position: relative;
            z-index: 1;
        }
        .image-container {
            flex: 1;
            background-image: url('../images/logo.png'); /* Replace with your image path */
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
        .login-container {
            flex: 1;
            padding: 40px;
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 20px;
            color: #444; /* Darker text color */
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
            color: #001f3f; /* Dark blue color for icons */
        }
        .login-container input {
            width: calc(100% - 40px); /* Adjust width to accommodate the icon */
            padding: 10px 10px 10px 40px; /* Add padding for the icon */
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #001f3f; /* Green button color */
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .login-container button:hover {
            background-color: #003366; /* Darker green on hover */
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .create-account {
            margin-top: 20px;
            font-size: 14px;
            color: #555; /* Darker text color */
        }
        .create-account a {
            color: #001f3f; /* Green link color */
            text-decoration: none;
        }
        .create-account a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <!-- Image Container -->
        <div class="image-container">
            <!-- Optional: Add text or logo here -->
        </div>
        <!-- Login Container -->
        <div class="login-container">
            <h2>LOGIN</h2>
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            <form action="login.php?redirect=<?php echo urlencode($_GET['redirect'] ?? '../index.php'); ?>" method="POST">
                <!-- Username Input with Profile Icon -->
                <div class="input-container">
                    <i class="fas fa-user"></i> <!-- Profile Icon -->
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <!-- Password Input with Lock Icon -->
                <div class="input-container">
                    <i class="fas fa-lock"></i> <!-- Lock Icon -->
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <br><br>
                <button type="submit">Login</button>
            </form><br>
            <hr>
            <div class="create-account">
                Don't have an account?<a href="signup.php"><br>Create New Account</a><br>
            </div>
        </div>
    </div>
</body>
</html>