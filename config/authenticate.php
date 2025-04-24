<?php
require('connect.php');

session_start();
/*******w******** 
    
    Name: 
    Date: 
    Description:

****************/

function valid_user($db, $user, $password) {
    $query = "SELECT * FROM user_table WHERE user_name = :user";
    $statement = $db->prepare($query);
    $statement->bindParam(':user', $user, PDO::PARAM_STR);
    $statement->execute();
    
    // Fetch the user data
    $user_data = $statement->fetch(PDO::FETCH_ASSOC);
    
    if ($user_data && password_verify($password, $user_data['user_password'])) {
        return $user_data; // Return user details if the password is correct
    } else {
        return false; // User not found or password incorrect
    }
}


if (!isset($_POST['username']) || !isset($_POST['password']) || empty(trim($_POST["username"])) || empty(trim($_POST["password"]))) {
    $message = "Missing credentials";
    // echo json_encode(["success" => false, "message" => "Missing credentials"]);
} else {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    $user_data = valid_user($db, $username, $password);

    if ($user_data) {
        $_SESSION['user'] = $user_data['user_name']; // Store session
        $_SESSION['user_fname'] = $user_data['user_fname']; // Optionally store first name
        $_SESSION['user_id'] = $user_data['user_id']; // Optionally store id
        if ($user_data['user_id'] === 12){
            header("Location: ../Resource/pages/Admin/admin.html"); // Redirect to home page
        exit();
        }
        // echo json_encode(["success" => true, "message" => "Login successful"]);
        header("Location: ../Resource/pages/watchedMovie.php"); // Redirect to home page
        exit();
    } else {
        "Invalid username or password";

        $message ="Invalid username or password";
        // echo json_encode(["success" => false, "message"  => "Invalid username or password"]);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifyimg Credentials</title>
    <style>
        body {
            background-color: #353535;
            color: #ffffff;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }

        h1 {
            font-size: 3em;
            color: #e50914;
            margin-bottom: 0.5em;
        }

        p {
            font-size: 1.2em;
        }

        a {
            color: #e50914;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>    
    
    <h1><?= $message ?></h1>
    <p>Go back to <a href="../login.html">Log in</a></p>
    
    
</body>
</html>