<?php
require('../../config/connect.php');
require('../../config/sessionCheck.php');
require 'imageProcessing/vendor/autoload.php';
use \Gumlet\ImageResize;

$current_user = $_SESSION['user_id'];

function add_request($db, $movie_name, $genre, $imdb_rating, $director, $language, $poster, $year, $description, $imdb_id, $current_user) {
    $status = "Pending";
    $query = "INSERT INTO movie_request (imdb_id, user_id, status, movie_name, movie_year, genre, movie_description, imdb_rating, director, language, poster) 
              VALUES (:imdb_id, :user_id, :status, :movie_name, :movie_year, :genre, :movie_description, :imdb_rating, :director, :language, :poster)";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':imdb_id', $imdb_id);
    $stmt->bindValue(':user_id', $current_user);
    $stmt->bindValue(':status', $status);
    $stmt->bindValue(':movie_name', $movie_name);
    $stmt->bindValue(':movie_year', $year);
    $stmt->bindValue(':genre', $genre);
    $stmt->bindValue(':movie_description', $description);
    $stmt->bindValue(':imdb_rating', $imdb_rating);
    $stmt->bindValue(':director', $director);
    $stmt->bindValue(':language', $language);
    $stmt->bindValue(':poster', $poster);
    try {
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return false;
    }
}

function file_upload_path($filename, $folder = 'uploads') {
    $base = dirname(__FILE__);
    return join(DIRECTORY_SEPARATOR, [$base, $folder, basename($filename)]);
}

function file_is_an_image($temp, $path) {
    $valid_exts = ['gif', 'jpg', 'jpeg', 'png'];
    $valid_types = ['image/gif', 'image/jpeg', 'image/png'];
    $ext = pathinfo($path, PATHINFO_EXTENSION);
    $type = mime_content_type($temp);
    return in_array($ext, $valid_exts) && in_array($type, $valid_types);
}

// Image Upload
$poster = "Default.jpeg";
$validFile = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $original_filename = uniqid() . '_' . basename($_FILES['image']['name']);
    $temp_path = $_FILES['image']['tmp_name'];
    $new_path = file_upload_path($original_filename);
    if (file_is_an_image($temp_path, $new_path)) {
        if (move_uploaded_file($temp_path, $new_path)) {
            $image = new ImageResize($new_path);
            // Resize to medium and save with the same filename
            $image->resizeToWidth(400)->save($new_path); // Overwrite the original file with the resized image
            
            $poster = $original_filename;
            $validFile = true;
        }
    }
}


// Validation
$errors = [];
// IMDb ID Handling
if (empty($_POST['imdb_id'])) {
    $imdb_id = "A2C4E8"; 
} else {
    $imdb_id = filter_var($_POST['imdb_id'], FILTER_SANITIZE_SPECIAL_CHARS);
}
if (empty(trim($_POST['movie_name']))) {
    $errors[] = "Movie name is required.";
} else {
    $movie_name = filter_var($_POST['movie_name'], FILTER_SANITIZE_SPECIAL_CHARS);
}
if (empty($_POST['movie_year'])) {
    $errors[] = "Year is required.";
} elseif (!preg_match('/^\d{4}$/', $_POST['movie_year'])) {
    $errors[] = "Invalid year.";
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
if (empty(trim($_POST['imdb_rating']))) {
    $errors[] = "IMDb rating is required.";
} elseif (!is_numeric($_POST['imdb_rating']) || floatval($_POST['imdb_rating']) < 0 || floatval($_POST['imdb_rating']) > 10) {
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

$isDbUpdated = false;
if (empty($errors)) {
    $isDbUpdated = add_request($db, $movie_name, $genre, $imdb_rating, $director, $language, $poster, $year, $description, $imdb_id, $current_user);
} elseif (!empty($errors)) {
    foreach ($errors as $error) {
        // Error messages handled in HTML
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Submission Status</title>
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

        .error {
            color: red;
        }
    </style>
</head>

<body>
    <?php if (!empty($errors)): ?>
        <h1 class="error">Movie submission failed. Please fix the errors.</h1>
        <?php foreach ($errors as $error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    <?php elseif ($isDbUpdated): ?>
        <h1><?= htmlspecialchars($movie_name) ?> has been added to the request list!</h1>
    <?php else: ?>
        <h1 class="error">Something went wrong during submission.</h1>
    <?php endif; ?>

    <a href="addmovie.php">Go back</a>
</body>

</html>