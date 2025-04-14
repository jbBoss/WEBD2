<?php
require('../config/connect.php');

session_start();

function valid_user($db, $user, $password) {
    $query = "SELECT * FROM user_table WHERE user_name = :user";
    $statement = $db->prepare($query);
    $statement->bindParam(':user', $user, PDO::PARAM_STR);
    $statement->execute();
    
    // Fetch the user data
    $user_data = $statement->fetch(PDO::FETCH_ASSOC);
    
    if ($user_data) {
        // Verify the password against the hash
        if (password_verify($password, $user_data['user_password'])) {
            return $user_data; // Return user details if the password is correct
        } else {
            return false; // Password doesn't match
        }
    } else {
        return false; // User not found
    }
}

if (!isset($_POST['username']) || !isset($_POST['password']) || empty(trim($_POST["username"])) || empty(trim($_POST["password"]))) {
    echo json_encode(["success" => false, "message" => "Missing credentials"]);
    exit();
} else {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $user_data = valid_user($db, $username, $password);

    
}
?>
