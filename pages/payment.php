<?php
session_start();

// Include your database connection file
require_once '../includes/db.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Retrieve user_id from session
$user_id = $_SESSION['user_id'];

// Fetch Cart Items
$cart_items = [];
$total = 0;

$query = "
    SELECT c.cart_id, c.item_id, c.quantity, m.item_name, m.price, m.image_url
    FROM cart c
    JOIN menu m ON c.item_id = m.item_id
    WHERE c.user_id = :user_id
";
$stmt = $conn->prepare($query);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $row) {
    $cart_items[] = $row;
    $total += $row['price'] * $row['quantity'];
}

// Handle Payment Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'];
    $upi_id = $_POST['upi_id'] ?? null;
    $card_number = $_POST['card_number'] ?? null;

    // Validate payment method
    if (!in_array($payment_method, ['upi', 'card', 'cod'])) {
        die("Invalid payment method.");
    }

    // Start a transaction
    $conn->beginTransaction();

    try {
        // Insert into orders table
        $order_query = "
            INSERT INTO orders (user_id, username, total_amount, payment_method, upi_id, card_number, order_status, created_at)
            VALUES (:user_id, :username, :total_amount, :payment_method, :upi_id, :card_number, 'Pending', NOW())
        ";
        $stmt = $conn->prepare($order_query);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':username', $_SESSION['username'], PDO::PARAM_STR);
        $stmt->bindValue(':total_amount', $total, PDO::PARAM_STR);
        $stmt->bindValue(':payment_method', $payment_method, PDO::PARAM_STR);
        $stmt->bindValue(':upi_id', $upi_id, PDO::PARAM_STR);
        $stmt->bindValue(':card_number', $card_number, PDO::PARAM_STR);
        $stmt->execute();
        $order_id = $conn->lastInsertId();

        // Insert into order_items table
        foreach ($cart_items as $item) {
            $order_item_query = "
                INSERT INTO order_items (order_id, item_id, quantity, price)
                VALUES (:order_id, :item_id, :quantity, :price)
            ";
            $stmt = $conn->prepare($order_item_query);
            $stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
            $stmt->bindValue(':item_id', $item['item_id'], PDO::PARAM_INT);
            $stmt->bindValue(':quantity', $item['quantity'], PDO::PARAM_INT);
            $stmt->bindValue(':price', $item['price'], PDO::PARAM_STR);
            $stmt->execute();
        }

        // Insert into payments table
        $payment_query = "
            INSERT INTO payments (user_id, total_amount, payment_method, upi_id, card_number, payment_date, order_id)
            VALUES (:user_id, :total_amount, :payment_method, :upi_id, :card_number, NOW(), :order_id)
        ";
        $stmt = $conn->prepare($payment_query);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':total_amount', $total, PDO::PARAM_STR);
        $stmt->bindValue(':payment_method', $payment_method, PDO::PARAM_STR);
        $stmt->bindValue(':upi_id', $upi_id, PDO::PARAM_STR);
        $stmt->bindValue(':card_number', $card_number, PDO::PARAM_STR);
        $stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->execute();

        // Clear the cart
        $clear_cart_query = "DELETE FROM cart WHERE user_id = :user_id";
        $stmt = $conn->prepare($clear_cart_query);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Commit the transaction
        $conn->commit();

        // Display success message and redirect after 3 seconds
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Payment Success</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f9f9f9;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                }
                .success-message {
                    background-color: #d4edda;
                    color: #155724;
                    padding: 20px;
                    border-radius: 8px;
                    text-align: center;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }
            </style>
            <script>
                setTimeout(function() {
                    window.location.href = 'orders.php';
                }, 3000); // Redirect after 3 seconds
            </script>
        </head>
        <body>
            <div class='success-message'>
                <h2>Payment Successful!</h2>
                <p>Your order has been placed. Redirecting to your orders page...</p>
            </div>
        </body>
        </html>";
        exit();
    } catch (PDOException $e) {
        // Rollback the transaction on error
        $conn->rollBack();
        die("Payment failed: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Add your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .payment-method {
            margin-bottom: 20px;
        }
        .payment-method label {
            display: block;
            margin-bottom: 10px;
        }
        .payment-method input[type="radio"] {
            margin-right: 10px;
        }
        .payment-details {
            display: none;
            margin-top: 20px;
        }
        .payment-details input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .proceed-to-pay {
            display: block;
            width: 100%;
            padding: 15px;
            background-color: rgb(40, 43, 243);
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }
        .proceed-to-pay:hover {
            background-color: rgb(7, 16, 187);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Payment</h1>
        <form action="payment.php" method="POST">
            <div class="payment-method">
                <label>
                    <input type="radio" name="payment_method" value="upi" required> UPI
                </label>
                <label>
                    <input type="radio" name="payment_method" value="card" required> Credit/Debit Card
                </label>
                <label>
                    <input type="radio" name="payment_method" value="cod" required> Cash on Delivery (COD)
                </label>
            </div>

            <div id="upi-details" class="payment-details">
                <input type="text" name="upi_id" placeholder="Enter UPI ID">
            </div>

            <div id="card-details" class="payment-details">
                <input type="text" name="card_number" placeholder="Enter Card Number">
            </div>

            <button type="submit" class="proceed-to-pay">Proceed to Pay</button>
        </form>
    </div>

    <script>
        document.querySelectorAll('input[name="payment_method"]').forEach(input => {
            input.addEventListener('change', function() {
                document.getElementById('upi-details').style.display = this.value === 'upi' ? 'block' : 'none';
                document.getElementById('card-details').style.display = this.value === 'card' ? 'block' : 'none';
            });
        });
    </script>
</body>
</html>