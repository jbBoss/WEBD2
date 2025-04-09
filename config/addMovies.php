<?php
require('connect.php');

$isDbUpdated = false;

function check_in_movie_table($movie, $db, $movie_name, $genre, $imdb_rating, $director, $language, $poster, $year, $description, $imdb_id) {
    global $isDbUpdated;

    if ($movie) { // If movie exists
        $isDbUpdated = false;
    } else {
        $isDbUpdated = true;
        add_to_movie_table($db, $movie_name, $genre, $imdb_rating, $director, $language, $poster, $year, $description, $imdb_id);
    }
}

function add_to_movie_table($db, $movie_name, $genre, $imdb_rating, $director, $language, $poster, $year, $description, $imdb_id) {
    $rating= $imdb_rating;
    // echo $rating;
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

    $insert_statement->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input values
    $movie_name = filter_var($_POST['movie_name'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $genre = filter_var($_POST['genre'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $imdb_rating = filter_var($_POST['imdb_rating'] ?? '', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    // echo $imdb_rating;
    $director = filter_var($_POST['director'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $language = filter_var($_POST['language'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $poster = filter_var($_POST['poster'] ?? '', FILTER_SANITIZE_URL);
    $year = filter_var($_POST['movie_year'] ?? '', FILTER_SANITIZE_NUMBER_INT);
    $description = filter_var($_POST['movie_description'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $imdb_id = filter_var($_POST['imdb_id'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($imdb_id)) {
        exit();
    }

    if (!empty($movie_name) && !empty($imdb_id)) {
        $query = "SELECT imdb_id, movie_id FROM movie_table WHERE imdb_id = :imdb_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':imdb_id', $imdb_id, PDO::PARAM_STR);
        $statement->execute();

        $movie = $statement->fetch(PDO::FETCH_ASSOC);

        check_in_movie_table($movie, $db, $movie_name, $genre, $imdb_rating, $director, $language, $poster, $year, $description, $imdb_id);
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

<?php if ($isDbUpdated): ?>
    <h1><?= htmlspecialchars($_POST['movie_name']) ?> Added to DB</h1>
<?php else: ?>
    <h1><?= htmlspecialchars($_POST['movie_name']) ?> Exists in DB</h1>
<?php endif; ?>

<a href="../Resource/pages/home.php">
    Go back to Admin's dashboard.
</a>

</body>
</html>
