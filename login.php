<?php

include_once 'config.php';
include_once 'jwt.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the raw POST data
    $data = json_decode(file_get_contents("php://input"), true);
    error_log("Received data: " . print_r($data, true)); // Log received data

    // Check if the required fields are present
    if (!empty($data) && isset($data['username']) && isset($data['password'])) {
        $username = $data['username'];
        $password = $data['password'];

        // Retrieve user
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        error_log("Fetched user: " . print_r($user, true)); // Log the fetched user

        if ($user && password_verify($password, $user['password'])) {
            // Handle successful login
        } else {
            error_log("Login failed for username: $username"); // Log login failure
            echo json_encode(["message" => "Invalid username or password."]);
        }
    } else {
        echo json_encode(["message" => "Missing username or password."]);
    }
}

?>
