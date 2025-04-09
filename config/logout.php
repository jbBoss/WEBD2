<?php
session_start(); // Start the session
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session
header("Location: /WD/0_Project_WEBD/login.html"); // Redirect to the homepage or login page
exit();
?>
