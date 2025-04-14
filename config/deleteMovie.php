<?php
require_once('connect.php'); // Adjust if needed

$movie_id = filter_input(INPUT_GET, 'movie_id', FILTER_VALIDATE_INT);

if ($movie_id === false || $movie_id === null) {
    $message = "Invalid movie ID.";
    $deleted = false;
} else {
    // Deleting from the movie_table
    $query = "DELETE FROM movie_table WHERE movie_id = :movie_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':movie_id', $movie_id, PDO::PARAM_INT);
    $statement->execute(); 
    $movieDeleted = $statement->rowCount() > 0;

    // Deleting from the watched table
    $query = "DELETE FROM watched WHERE movie_id = :movie_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':movie_id', $movie_id, PDO::PARAM_INT);
    $statement->execute(); 
    $watchedDeleted = $statement->rowCount() > 0;

    // Determine message based on the results
    if ($movieDeleted && $watchedDeleted) {
        $message = "Movie and related Comments entries deleted successfully!";
    } elseif ($movieDeleted) {
        $message = "Movie deleted successfully, but no Comments entries found.";
    } elseif ($watchedDeleted) {
        $message = "Comments entries deleted, but movie not found.";
    } else {
        $message = "Movie and related entries not found or already deleted.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Movie Deletion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: white;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            text-align: center;
        }

        h1 {
            color: <?= $movieDeleted || $watchedDeleted ? '#28a745' : '#dc3545' ?>;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            text-decoration: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            transition: background-color 0.2s ease-in-out;
        }

        a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?= $message ?></h1>
        <a href="../Resource/pages/Admin/movies.php">Go Back</a>
    </div>
</body>
</html>
