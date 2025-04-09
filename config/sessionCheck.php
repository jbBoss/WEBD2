<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /WD/0_Project_WEBD/login.html");
    exit();
// } elseif ( isset($_SESSION['user_id']) && $_SESSION['user_id'] === 12) {
//     header("Location: /WD/0_Project_WEBD/Resource\pages\Admin\admin.htmll");
//     echo"admin";
//     exit();
} else {
    // User is logged in and not an admin – proceed normally
}
?>
