<?php
header('Content-Type: application/json'); // Ensure the response is JSON

require '../restaurant/includes/db.php';

try {
    // Get user input
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        throw new Exception("Invalid input");
    }

    $message = strtolower($input['message']);
    $user_id = $input['user_id'] ?? null; // Optional: Add user authentication

    // Handle user queries
    if (strpos($message, 'hii') !== false || strpos($message, 'hello') !== false) {
        $response = ["response" => "Hello! How can I assist you today?"];
    } elseif (strpos($message, 'bye') !== false) {
        $response = ["response" => "Goodbye! Have a great day!"];
    } elseif (strpos($message, 'special dishes') !== false || strpos($message, 'specials') !== false) {
        // Fetch special dishes from the menu table
        $stmt = $conn->query("SELECT item_name, price FROM menu WHERE special = 1");
        $specials = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($specials) {
            $responseText = "Today's special dishes:\n";
            foreach ($specials as $item) {
                $responseText .= "- " . $item['item_name'] . " ($" . $item['price'] . ")\n";
            }
            $response = ["response" => $responseText];
        } else {
            $response = ["response" => "No special dishes available today."];
        }
    } elseif (strpos($message, 'feedback') !== false) {
        // Handle feedback submission
        $feedback = $input['feedback'] ?? null;
        if ($feedback) {
            // Insert feedback into the feedback table
            $stmt = $conn->prepare("INSERT INTO feedback (user_id, message) VALUES (:user_id, :message)");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':message', $feedback);
            $stmt->execute();

            $response = ["response" => "Thank you for your feedback!"];
        } else {
            $response = ["response" => "Please provide your feedback."];
        }
    } elseif (strpos($message, 'order status') !== false) {
        // Fetch order status logic
        $response = ["response" => "Order status logic here"];
    } elseif (strpos($message, 'reservation') !== false) {
        // Handle reservation logic
        $response = ["response" => "Reservation logic here"];
    } else {
        $response = ["response" => "How can I assist you today?"];
    }
} catch (Exception $e) {
    // Handle errors and return them as JSON
    $response = ["error" => "An error occurred: " . $e->getMessage()];
}

// Send JSON response
echo json_encode($response);
?>