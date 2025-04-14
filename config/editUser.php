<?php
require('../config/connect.php');

session_start();

function valid_user($db, $user, $password)
{
    $query = "SELECT * FROM user_table WHERE user_name = :user";
    $statement = $db->prepare($query);
    $statement->bindParam(':user', $user, PDO::PARAM_STR);
    $statement->execute();

    // Fetch the user data
    $user_data = $statement->fetch(PDO::FETCH_ASSOC);

    if ($user_data) {
        echo $user_data['user_password'].'<br>';
        if (password_verify($password, $user_data['user_password'])) {
            echo 'Valid Cred';
        } else {
            echo 'invalid Cred';
        }
    } else {
        echo 'User_not found';
    }
}
// if (!isset($_POST['username']) || !isset($_POST['password']) || empty(trim($_POST["username"])) || empty(trim($_POST["password"]))) {
//     echo json_encode(["success" => false, "message" => "Missing credentials"]);
//     exit();
// } else {
    $username = trim($_POST['username']);
    // $enteredPassword = trim($_POST['password']);
    $enteredPassword = "Testing@123";
    echo "Entered username " . htmlspecialchars($username) . "<br>";
    echo "Entered Password " . htmlspecialchars($enteredPassword) . "<br>";

    // Call the valid_user function to authenticate
    valid_user($db, $username, $enteredPassword);

// }
?>