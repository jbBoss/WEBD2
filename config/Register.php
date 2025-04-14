<?php
require('connect.php');
function valid_user_fullname($f_name, $l_name) {
    $firstName = filter_var($f_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $lastName = filter_var($l_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
    if (preg_match("/^[a-zA-Z]+$/", $firstName) && preg_match("/^[a-zA-Z]+$/", $lastName)) {
        // Capitalize first letter of each name
        $firstName = ucfirst(strtolower($firstName));
        $lastName = ucfirst(strtolower($lastName));
        
        return $firstName . " " . $lastName;
    } else {
        return false;
    }
}

$profileImage= "default.jpeg";
function valid_user_email($data) {
    $email = filter_var($data, FILTER_SANITIZE_EMAIL);
    $email =strtolower($email);
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : false;
}

function valid_user_name($data) {
    $userName = filter_var($data, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    return preg_match("/^[a-zA-Z0-9_]+$/", $userName) ? $userName : false;
}

function valid_password($data_one, $data_two) {
    $pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$/";

    if (preg_match($pattern, $data_one) && preg_match($pattern, $data_two)) {
        return ($data_one === $data_two) ? $data_one : false;
    } else {
        return false;
    }
}

// Initialize error messages
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate full name
    $fullName = valid_user_fullname($_POST['firstName'], $_POST['lastName']);
    if ($fullName === false) {
        $errors[] = "Enter a valid First Name and Last Name.";
    } 
    // Validate email
    $email = valid_user_email($_POST['email']);
    if ($email === false) {
        $errors[] = "Enter a valid email address.";
    } 
    // Validate username
    $userName = valid_user_name($_POST['userName']);
    if ($userName === false) {
        $errors[] = "Enter a valid Username (letters, numbers, and underscores only).";
    }
    // Validate password
    $password = valid_password($_POST['password'], $_POST['confirmPassword']);
    if ($password === false) {
        $errors[] = "Password must be at least 8 characters, include one uppercase letter, one lowercase letter, and one number.";
    }
    // If errors exist, concatenate them into a message
    if (!empty($errors)) {
        $message = implode("<br>", $errors);
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $message = "Registration Successful!";
    }
} else {
    $message = "Error in processing form. Please try again.";
}
if (empty($errors)){
    $query ="INSERT INTO user_table (user_fname, user_name, user_gmail, user_password, user_image) VALUES (:user_fname, :user_name, :user_gmail, :user_password, :user_image )";
    $statement = $db->prepare($query);
    $statement->bindValue(':user_fname', $fullName);
    $statement->bindValue(':user_name', $userName);
    $statement->bindValue(':user_gmail', $email);
    $statement->bindValue(':user_password', $hashedPassword);
    $statement->bindValue(':user_image', $profileImage);
    if($statement->execute()){
        $message = "Registration Successful!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>    
    <h1><?= $message ?></h1>

    <?php if (empty($errors)): ?>
        <p><strong>Full Name:</strong> <?= htmlspecialchars($fullName) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
        <p><strong>Username:</strong> <?= htmlspecialchars($userName) ?></p>
        <p><strong>Password:</strong> **********</p>
        <a href="../login.html">go back to login</a>
    <?php endif; ?>
</body>
</html>
