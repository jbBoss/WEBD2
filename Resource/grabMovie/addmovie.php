<?php
require 'imageProcessing/vendor/autoload.php';
require('../../config/sessionCheck.php');
use \Gumlet\ImageResize;


// Optional flags for UI feedback
$image_upload_detected = $_GET['upload'] ?? false;
$validFile = $_GET['valid'] ?? false;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Movie Entry</title>
    <link rel="stylesheet" href="../Styles/defaultNav.css">
    <?php if ($_SESSION['user_id'] === 12): ?>
    <link rel="stylesheet" href="adminaddmovie.css">
       
        <?php else: ?>
    <link rel="stylesheet" href="style.css">
    <?php endif; ?>

</head>

<body>
    <?php if ($_SESSION['user_id'] === 12): ?>
        <?php include '../../config/adminnav.php'; ?>
    <?php elseif (isset($_SESSION['user_id'])): ?>
        <nav class="defaultNav">
            <div class="navbar-top">
                <ol class="nav-list">
                    <li class="nav-item"><a href="../pages/userComments.php" class="nav-link"><?= $_SESSION['user'] ?>'s
                            Comments & Ratings</a></li>
                </ol>
            </div>
            <h1 class="site-title"><a href="../../index.php">MovieConnect</a></h1>

            <ol class="nav-list">
                <li class="nav-item"><a href="../grabMovie/addMovie.php" class="nav-link">Request New Movie</a></li>
                <li class="nav-item"><a href="../../config/logout.php" class="nav-link logout">Log Out</a></li>
            </ol>
        </nav>
    <?php else: ?>
        <nav>
            <h1>MovieConnect</h1>
            <li><a href="../..login.html">Log in</a></li>
        </nav>

    <?php endif; ?>
    <h4>Request New Movie</h4>
    <main>
        <div class="api_control">
            <input type="text" id="search" placeholder="Search Here to Grab Details Faster">
            <div>
                <p id="search-results">

                </p>
            </div>
        </div>
        <div class="form_control">
            <form id="movie-form" method="POST" action="submit.php" enctype="multipart/form-data">
                <input type="text" id="imdb_id" name="imdb_id" hidden>

                <label>Title: <input type="text" id="title" name="movie_name" required></label>
                <label>Year: <input type="number" id="year" name="movie_year" required></label>
                <label>Director: <input type="text" id="director" name="director" required></label>
                <label>Genre: <input type="text" id="genre" name="genre" required></label>
                <label>Language: <input type="text" id="language" name="language" required></label>
                <label>Rating: <input type="text" id="rating" name="imdb_rating" required></label>

                <input type='file' name='image' id='image' accept="image/*">
                <label>Plot: <textarea id="plot" name="movie_description" required></textarea></label>

                <input type="submit" value="Submit Movie">
                <button type="reset">Reset</button>
            </form>
        </div>
    </main>

    <script src="script.js"></script>
</body>

</html>