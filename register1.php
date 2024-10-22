<?php


include_once 'config.php';
include_once 'jwt.php'; // Make sure this file has your JWT generation function

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['contact_number']) && isset($data['username']) && isset($data['password'])) {
        $contact_number = $data['contact_number'];
        $username = $data['username'];
        $password = password_hash($data['password'], PASSWORD_DEFAULT);

        // Check if the user already exists
        $check_query = "SELECT * FROM users WHERE username = :username";
        $stmt = $conn->prepare($check_query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode(["message" => "User already exists."]);
            exit();
        }

        // Insert new user
        $query = "INSERT INTO users (contact_number, username, password) VALUES (:contact_number, :username, :password)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':contact_number', $contact_number);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        
        if ($stmt->execute()) {
            $user_id = $conn->lastInsertId(); // Get the ID of the newly created user
            $jwt = generate_jwt($user_id, $username); // Generate JWT

            // Store the JWT in the database
            $update_query = "UPDATE users SET jwt_token = :jwt WHERE id = :id";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bindParam(':jwt', $jwt);
            $update_stmt->bindParam(':id', $user_id);
            $update_stmt->execute();

            echo json_encode([
                "message" => "User registered successfully.",
                "jwt" => $jwt // Optionally return JWT
            ]);
        } else {
            echo json_encode(["message" => "User registration failed."]);
        }
    } else {
        echo json_encode(["message" => "Missing required fields."]);
    }
}
?>
