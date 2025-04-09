<?php
require('connect.php');

require('sessionCheck.php');


$current_user = $_SESSION['user_id'];

// Function to remove movie from watchlist
function remove_from_watchlist($db, $movie_id, $current_user) {
    $query = "DELETE FROM watchlist WHERE movie_id = :movie_id AND user_id = :user_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':movie_id', $movie_id, PDO::PARAM_INT);
    $statement->bindValue(':user_id', $current_user, PDO::PARAM_INT);
    
    if ($statement->execute()) {
        return true;
    } else {
        error_log("Failed to remove movie from watchlist: " . implode(" | ", $statement->errorInfo()));
        return false;
    }
}

// Function to add movie to watched list
function add_to_watched($db, $movie_id, $current_user) {
    $insert_query = "INSERT INTO watched (movie_id, user_id, comment, rating) 
                     VALUES (:movie_id, :user_id, :comment, :rating)";
    $insert_statement = $db->prepare($insert_query);
    $insert_statement->bindValue(':movie_id', $movie_id, PDO::PARAM_INT);
    $insert_statement->bindValue(':user_id', $current_user, PDO::PARAM_INT);
    $insert_statement->bindValue(':comment', "NA", PDO::PARAM_STR);
    $insert_statement->bindValue(':rating', 0, PDO::PARAM_INT);

    if ($insert_statement->execute()) {
        if (remove_from_watchlist($db, $movie_id, $current_user)) {
            header("Location: ../Resource/pages/watched.php");
            exit();
        }
    } else {
        error_log("Failed to add movie to watched list: " . implode(" | ", $insert_statement->errorInfo()));
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    $movie_id = filter_input(INPUT_POST, 'movie_id', FILTER_VALIDATE_INT);
    
    if ($movie_id) {
        if (isset($_POST['watched'])) {
            add_to_watched($db, $movie_id, $current_user);
        } elseif (isset($_POST['remove'])) {
            if (remove_from_watchlist($db, $movie_id, $current_user)) {
                header("Location: ../Resource/pages/watchlist.php");
                exit();
            }
        }
    } else {
        echo "Invalid movie ID.<br>";
    }
}
?>
