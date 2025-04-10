<?php
require 'imageProcessing/vendor/autoload.php';
require('../../config/sessionCheck.php');
require('../../config/connect.php');
use \Gumlet\ImageResize;

$edit_movie = [];

if (isset($_GET['movie_id'])) {
    $movie_id = filter_input(INPUT_GET, 'movie_id', FILTER_SANITIZE_NUMBER_INT);

    $query = "SELECT * FROM movie_table WHERE movie_id = :movie_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':movie_id', $movie_id, PDO::PARAM_INT);
    $statement->execute();
    $edit_movie = $statement->fetch(PDO::FETCH_ASSOC);
}

function add_request($db, $movie_name, $genre, $imdb_rating, $director, $language, $poster, $year, $description, $imdb_id)
{
    $movie_id = filter_input(INPUT_GET, 'movie_id', FILTER_SANITIZE_NUMBER_INT);
    $query = "UPDATE movie_table
              SET imdb_id = :imdb_id,
                  movie_name = :movie_name,
                  movie_year = :movie_year,
                  genre = :genre,
                  movie_description = :movie_description,
                  imdb_rating = :imdb_rating,
                  director = :director,
                  language = :language,
                  poster = :poster
              WHERE movie_id = :movie_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':imdb_id', $imdb_id);
    $statement->bindValue(':movie_name', $movie_name);
    $statement->bindValue(':movie_year', $year);
    $statement->bindValue(':genre', $genre);
    $statement->bindValue(':movie_description', $description);
    $statement->bindValue(':imdb_rating', $imdb_rating);
    $statement->bindValue(':director', $director);
    $statement->bindValue(':language', $language);
    $statement->bindValue(':poster', $poster);
    $statement->bindValue(':movie_id', $movie_id, PDO::PARAM_INT);
    try {
        return $statement->execute();
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return false;
    }
}

function file_upload_path($filename, $folder = 'uploads')
{
    $base = dirname(__FILE__);
    return join(DIRECTORY_SEPARATOR, [$base, $folder, basename($filename)]);
}

function file_is_an_image($temp, $path)
{
    $valid_exts = ['gif', 'jpg', 'jpeg', 'png'];
    $valid_types = ['image/gif', 'image/jpeg', 'image/png'];
    $ext = pathinfo($path, PATHINFO_EXTENSION);
    $type = mime_content_type($temp);
    return in_array($ext, $valid_exts) && in_array($type, $valid_types);
}

// Handle image upload
$poster = $edit_movie['poster'] ?? "Default.jpeg";
$validFile = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $original_filename = uniqid() . '_' . basename($_FILES['image']['name']);
    $temp_path = $_FILES['image']['tmp_name'];
    $new_path = file_upload_path($original_filename);
    if (file_is_an_image($temp_path, $new_path)) {
        if (move_uploaded_file($temp_path, $new_path)) {
            $image = new ImageResize($new_path);
            $image->resizeToWidth(400)->save($new_path);
            $poster = $original_filename;
            $validFile = true;
        }
    }
}

// Handle form submission
$errors = [];
$isDbUpdated = false;

if (isset($_POST['editMovieDetails'])) {
    $imdb_id = empty($_POST['imdb_id']) ? "A2C4E8" : filter_var($_POST['imdb_id'], FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty(trim($_POST['movie_name']))) {
        $errors[] = "Movie name is required.";
    } else {
        $movie_name = filter_var($_POST['movie_name'], FILTER_SANITIZE_SPECIAL_CHARS);
    }

    if (empty($_POST['movie_year']) || !preg_match('/^\d{4}$/', $_POST['movie_year'])) {
        $errors[] = "Invalid or missing year.";
    } else {
        $year = filter_var($_POST['movie_year'], FILTER_SANITIZE_NUMBER_INT);
    }

    if (empty(trim($_POST['genre']))) {
        $errors[] = "Genre is required.";
    } else {
        $genre = filter_var($_POST['genre'], FILTER_SANITIZE_SPECIAL_CHARS);
    }

    if (empty(trim($_POST['movie_description']))) {
        $errors[] = "Description is required.";
    } else {
        $description = filter_var($_POST['movie_description'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    if (empty(trim($_POST['imdb_rating'])) || !is_numeric($_POST['imdb_rating']) || floatval($_POST['imdb_rating']) < 0 || floatval($_POST['imdb_rating']) > 10) {
        $errors[] = "Invalid Rating. Must be between 0 and 10.";
    } else {
        $imdb_rating = filter_var($_POST['imdb_rating'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    if (empty(trim($_POST['director']))) {
        $errors[] = "Director is required.";
    } else {
        $director = filter_var($_POST['director'], FILTER_SANITIZE_SPECIAL_CHARS);
    }

    if (empty(trim($_POST['language']))) {
        $errors[] = "Language is required.";
    } else {
        $language = filter_var($_POST['language'], FILTER_SANITIZE_SPECIAL_CHARS);
    }

    // Use previous image if no new file uploaded
    if (!$validFile && isset($edit_movie['poster'])) {
        $poster = $edit_movie['poster'];
    }

    if (empty($errors)) {
        $isDbUpdated = add_request($db, $movie_name, $genre, $imdb_rating, $director, $language, $poster, $year, $description, $imdb_id);
        if ($isDbUpdated) {
            header("Location: /WD/0_Project_WEBD/Resource/pages/Admin/updateMovie.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Movie</title>
    <link rel="stylesheet" href="../Styles/home.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php if (isset($_SESSION['user_id'])): ?>
    <nav>
        <a href="../pages/userComments.php"><?= $_SESSION['user'] ?>'s Comments & Ratings</a>
        <h1>Comments and Ratings</h1>
        <ol>
            <li><a href="../grabMovie/addMovie.php">Request New Movie</a></li>
            <li><a href="../../config/logout.php">Log Out</a></li>
        </ol>
    </nav>
<?php else: ?>
    <nav>
        <h1>MovieConnect</h1>
        <li><a href="../../login.html">Log In</a></li>
    </nav>
<?php endif; ?>

<main>
    <?php if ($isDbUpdated): ?>
        

    <?php elseif (!empty($errors)): ?>
        <ul class="errors">
            <?php foreach ($errors as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <div class="api_control">
        <input type="text" id="search" placeholder="Search for a movie...">
        <div>
        <?php if ($edit_movie['poster'] !== "Default.jpeg"): ?>
                                            <img src="uploads/<?= $edit_movie['poster'] ?>" class="card-img-top"
                                                alt="<?= $edit_movie['movie_name'] ?>">
                                        <?php else: ?>
                                            <div class="card h-100 text-center p-3">
                                                <p><strong><?= $edit_movie['movie_name'] ?></strong></p>
                                                <p>Poster not available for this movie</p>
                                            </div>
                                        <?php endif; ?>
        </div>
    </div>

    <div class="form_control">
        <form id="movie-form" method="POST" action="editMovieDetail.php?movie_id=<?= $edit_movie['movie_id'] ?>" enctype="multipart/form-data">
            <input type="text" id="imdb_id" name="imdb_id" hidden value="<?= $edit_movie['imdb_id'] ?? '' ?>">

            <label>Title: <input type="text" id="title" name="movie_name" value="<?= $edit_movie['movie_name'] ?>" required></label>
            <label>Year: <input type="number" id="year" name="movie_year" value="<?= $edit_movie['movie_year'] ?>" required></label>
            <label>Director: <input type="text" id="director" name="director" value="<?= $edit_movie['director'] ?>" required></label>
            <label>Genre: <input type="text" id="genre" name="genre" value="<?= $edit_movie['genre'] ?>" required></label>
            <label>Language: <input type="text" id="language" name="language" value="<?= $edit_movie['language'] ?>" required></label>
            <label>Rating: <input type="text" id="rating" name="imdb_rating" value="<?= $edit_movie['imdb_rating'] ?>" required></label>

            <input type='file' name='image' id='image' accept="image/*">
            <label>Plot: 
                <textarea id="plot" name="movie_description" required><?= trim($edit_movie['movie_description']) ?></textarea>
            </label>

            <input type="submit" name="editMovieDetails" value="Submit Movie">
            <button type="reset">Reset</button>
        </form>
    </div>
</main>

<script src="script.js"></script>
</body>
</html>
