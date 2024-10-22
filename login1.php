<?php
include_once 'config.php';
include_once 'jwt.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['username']) && isset($data['password'])) {
        $username = $data['username'];
        $password = $data['password'];

        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $jwt = generate_jwt($user['id'], $user['username']);
            echo json_encode(["message" => "Login successful", "jwt" => $jwt]);
        } else {
            echo json_encode(["message" => "Invalid username or password."]);
        }
    } else {
        echo json_encode(["message" => "Missing username or password."]);
    }
}
?>
