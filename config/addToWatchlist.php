<?php
require('connect.php');
require('sessionCheck.php');

    $current_user = $_SESSION['user_id'];
    // echo $current_user."<br>";
$isWatchlistUpdated = '';

$user_query = "SELECT * FROM user_table WHERE user_id = :user_id";
$statement = $db->prepare($user_query);
$statement->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);
$statement->execute();

// Fetch the user data

          
function check_in_movie_table($movie, $db, $movie_name, $genre, $imdb_rating, $director, $language, $poster, $year, $description, $imdb_id,$current_user){
    if ($movie) { // if exist
        // echo  "movie_name exists in DB."."<br>";
        // echo  "trying to add to Adding to watchlist"."<br>";
        check_in_watchlist($db, $movie['movie_id'], $current_user);
    }
    else {
        add_to_movie_table($db, $movie_name, $genre, $imdb_rating, $director, $language, $poster, $year, $description, $imdb_id,$current_user);
        
    }
}
function add_to_movie_table($db, $movie_name, $genre, $imdb_rating, $director, $language, $poster, $year, $description, $imdb_id,$current_user) {
    $insert_query = "INSERT INTO movie_table (movie_name, genre, imdb_rating, director, language, poster, movie_year, movie_description, imdb_id) 
                     VALUES (:movie_name, :genre, :imdb_rating, :director, :language, :poster, :year, :description, :imdb_id)";

    $insert_statement = $db->prepare($insert_query);
    $insert_statement->bindValue(':movie_name', $movie_name);
    $insert_statement->bindValue(':genre', $genre);
    $insert_statement->bindValue(':imdb_rating', $imdb_rating);
    $insert_statement->bindValue(':director', $director);
    $insert_statement->bindValue(':language', $language);
    $insert_statement->bindValue(':poster', $poster);
    $insert_statement->bindValue(':year', $year);
    $insert_statement->bindValue(':description', $description);
    $insert_statement->bindValue(':imdb_id', $imdb_id);

    if ($insert_statement->execute()) {
        // echo $movie_name.' added to DB'."<br>";
    } else {
        // echo 'Failed to add to DB.';
    }
    $query = "SELECT imdb_id,movie_id FROM movie_table WHERE imdb_id = :imdb_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':imdb_id', $imdb_id, PDO::PARAM_STR);
        $statement->execute();

        $movie = $statement->fetch(PDO::FETCH_ASSOC);
    add_to_watchlist($db, $movie['movie_id'], $current_user);
}
function check_in_watchlist($db, $movie_id, $current_user) {
    global $isWatchlistUpdated; // Ensure this is accessible
    // echo "Checking in watchlist."."<br>";
    
    $query = "SELECT movie_id FROM watchlist WHERE movie_id = :movie_id AND user_id = :user_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':movie_id', $movie_id, PDO::PARAM_INT);
    $statement->bindValue(':user_id', $current_user, PDO::PARAM_INT);
    $statement->execute();
    $watchlist_movie = $statement->fetch(PDO::FETCH_ASSOC);

    if ($watchlist_movie) {
        // echo "Movie already exists in watchlist."."<br>";
        $isWatchlistUpdated = FALSE;
    } else {
        $isWatchlistUpdated = add_to_watchlist($db, $movie_id, $current_user);
    }
    return $isWatchlistUpdated;
}
function check_in_watchedlist($db, $movie_id, $current_user) {
    global $isWatchlistUpdated; // Ensure this is accessible
    // echo "Checking in watchlist."."<br>";
    
    $query = "SELECT movie_id FROM watched WHERE movie_id = :movie_id AND user_id = :user_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':movie_id', $movie_id, PDO::PARAM_INT);
    $statement->bindValue(':user_id', $current_user, PDO::PARAM_INT);
    $statement->execute();
    $watchlist_movie = $statement->fetch(PDO::FETCH_ASSOC);

    if ($watchlist_movie) {
        // echo "Movie already exists in watchlist."."<br>";
        $isWatchlistUpdated = FALSE;
    } else {
        $isWatchlistUpdated = add_to_watchlist($db, $movie_id, $current_user);
    }
    return $isWatchlistUpdated;
}
function add_to_watchlist($db, $movie_id, $current_user) {
    global $isWatchlistUpdated;
    // echo "Adding movie to watchlist."."<br>";
    // echo $current_user ."<br>";
    $insert_query = "INSERT INTO watchlist (movie_id, user_id) VALUES (:movie_id, :user_id)";
    $insert_statement = $db->prepare($insert_query);
    $insert_statement->bindValue(':movie_id', $movie_id, PDO::PARAM_INT);
    $insert_statement->bindValue(':user_id', $current_user, PDO::PARAM_INT);

    if ($insert_statement->execute()) {
        // echo "Movie added to watchlist."."<br>";
        $isWatchlistUpdated = TRUE;
    } else {
        $isWatchlistUpdated = FALSE;
        // echo "Failed to add movie to watchlist.";
    }
    return $isWatchlistUpdated;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input values
    $movie_name = filter_var($_POST['movie_name'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $genre = filter_var($_POST['genre'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $imdb_rating = filter_var($_POST['imbd_rating'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $director = filter_var($_POST['director'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $language = filter_var($_POST['language'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $poster = filter_var($_POST['poster'] ?? '', FILTER_SANITIZE_URL);
    $year = filter_var($_POST['movie_year'] ?? '', FILTER_SANITIZE_NUMBER_INT);
    $description = filter_var($_POST['movie_description'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $imdb_id = filter_var($_POST['imdb_id'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($imdb_id)) {
        // echo "notfound";
        exit();
    }

    if (!empty($movie_name) && !empty($imdb_id)) {
        $query = "SELECT imdb_id,movie_id FROM movie_table WHERE imdb_id = :imdb_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':imdb_id', $imdb_id, PDO::PARAM_STR);
        $statement->execute();

        $movie = $statement->fetch(PDO::FETCH_ASSOC);
        // echo "Checking in movie table"."<br>";
        check_in_movie_table($movie,$db, $movie_name, $genre, $imdb_rating, $director, $language, $poster, $year, $description, $imdb_id,$current_user);        


    } else {
        // echo 'Please fill in all required fields.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Resource/Styles/watchlistmsg.css">
    <title>Movie Submission</title>
</head>
<body>
<style>
        body {
            background-color: #1e1e1e;
            color: white;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        h1 {
            font-size: 2rem;
            margin-bottom: 20px;
        }
        a {
            color: #e64a19;
            text-decoration: none;
            font-size: 1.2rem;
            border: 2px solid #e64a19;
            padding: 10px 20px;
            border-radius: 5px;
            transition: all 0.3s ease-in-out;
        }
        a:hover {
            background-color: #e64a19;
            color: white;
        }
    </style>
    <?php if($isWatchlistUpdated): ?>
            <h1> <?= $_POST['movie_name'] ?> Added to Watchlist </h1>
            <?php else: ?>
            <h1> <?= $_POST['movie_name'] ?> exist in watchlist</h1>
    <?php endif ?>
    <a href="../Resource/pages/home.php">Go back to <?= $_SESSION['user'] ?> 's dashbord. </a>
</body>
</html>
