<?php
include_once 'config.php';
include_once 'jwt.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $refresh_token = $_POST['refresh_token'];

    // Check if refresh token exists in the database
    $query = "SELECT * FROM users WHERE refresh_token = :refresh_token";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':refresh_token', $refresh_token);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Generate a new JWT
        $new_jwt = generate_jwt($user['id'], $user['username']);
        
        // Update the JWT in the database
        $update_query = "UPDATE users SET jwt_token = :jwt WHERE id = :id";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bindParam(':jwt', $new_jwt);
        $update_stmt->bindParam(':id', $user['id']);
        $update_stmt->execute();

        echo json_encode(["jwt" => $new_jwt, "message" => "Token refreshed successfully"]);
    } else {
        echo json_encode(["message" => "Invalid refresh token"]);
    }
}
?>
