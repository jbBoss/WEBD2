<?php
require('../config/connect.php');
session_start();

$user_id = filter_input(INPUT_GET, 'user_id', FILTER_SANITIZE_NUMBER_INT);
$message = '';

if (!$user_id) {
    echo "Invalid user ID.";
    exit;
}

// Fetch user data
$query = "SELECT * FROM user_table WHERE user_id = :user_id";
$statement = $db->prepare($query);
$statement->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$statement->execute();
$user_data = $statement->fetch(PDO::FETCH_ASSOC);

if (!$user_data) {
    echo "User not found.";
    exit;
}


function valid_password($data_one)
{
    $pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$/";

    if (preg_match($pattern, $data_one)) {
        return ($data_one = password_hash($data_one, PASSWORD_DEFAULT));
    } else {
        return false;
    }
}


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $fullname = filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $new_password = $_POST['password'];

        if (!empty($new_password)) {
            $password = valid_password($new_password);
            if (!$password) {
                $message = "Password must be at least 8 characters, include uppercase, lowercase, and a number.";
            } else {
                $update_query = "UPDATE user_table 
                    SET user_name = :username, 
                        user_fname = :fullname,
                        user_gmail = :email,
                        user_password = :password
                    WHERE user_id = :user_id";

                $update_stmt = $db->prepare($update_query);
                $update_stmt->bindValue(':password', $password);
            }
        } else {
            $update_query = "UPDATE user_table 
                SET user_name = :username, 
                    user_fname = :fullname,
                    user_gmail = :email
                WHERE user_id = :user_id";

            $update_stmt = $db->prepare($update_query);
        }

        // Bind common values
        $update_stmt->bindValue(':username', $username);
        $update_stmt->bindValue(':fullname', $fullname);
        $update_stmt->bindValue(':email', $email);
        $update_stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        if ($update_stmt->execute()) {
            $message = "User updated successfully!";
            $statement->execute();
            $user_data = $statement->fetch(PDO::FETCH_ASSOC);
        } else {
            $message = "Error updating user.";
        }
    }
    }

    if (isset($_POST['delete'])) {
        // Delete from related tables first
        $tables = ['movie_request', 'watched', 'user_table'];
        $success = true;

        foreach ($tables as $table) {
            $delete_query = "DELETE FROM $table WHERE user_id = :user_id";
            $delete_stmt = $db->prepare($delete_query);
            $delete_stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

            if (!$delete_stmt->execute()) {
                $success = false;
                $message = "Error deleting from $table.";
                break;
            }
        }

        if ($success) {
            $message = "User and related data deleted successfully!";
            header("Location: ../Resource/pages/Admin/users.php");
            exit;
        }
    }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
</head>

<body>
    <h2>Edit User</h2>

    <?php if (!empty($message)): ?>
        <p style="color: green;"><?= htmlspecialchars($message) ?></p> <a href="../Resource/pages/Admin/users.php">go back
        </a>
    <?php endif; ?>

    <form action="" method="post">
        <label for="user_id">User id :<?= $user_data['user_id'] ?></label>
        <br>
        <label for="username">User Name</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user_data['user_name']) ?>" required><br>

        <label for="fullname">Full Name</label>
        <input type="text" name="fullname" value="<?= htmlspecialchars($user_data['user_fname']) ?>" required><br>

        <label for="email">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user_data['user_gmail']) ?>" required><br><br>

        <label for="password">Password</label>
        <input name="password"><br>


        <button type="submit" name="update">Update</button>
        <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this user?');"
            style="color: red;">Delete</button>
    </form>
</body>

</html>