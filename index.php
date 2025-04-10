<?php
require_once 'config/connect.php'; // Ensure database connection file is included
session_start(); // Start session

// Default query to fetch all movies
$query = "SELECT * FROM movie_table ORDER BY movie_id DESC";
$statement = $db->prepare($query);
$statement->execute();
$user_watched_movies = $statement->fetchAll(PDO::FETCH_ASSOC);

// Handle POST request for filtering and search
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : '';
    $genre = isset($_POST['genre']) ? $_POST['genre'] : '';
    $title_sort = isset($_POST['title_sort']) ? $_POST['title_sort'] : '';
    $search_term = isset($_POST['search']) ? '%' . $_POST['search'] . '%' : '';

    // Start base query for filtering
    $query = "SELECT * FROM movie_table WHERE 1"; // Starting with WHERE 1, meaning no condition

    // Array to collect parameters for binding
    $params = [];

    // Apply rating filter if selected
    if (!empty($rating)) {
        $query .= " AND imdb_rating >= :rating";
        $params[':rating'] = $rating;
    }

    // Apply genre filter if selected
    if (!empty($genre)) {
        $query .= " AND genre = :genre";
        $params[':genre'] = $genre;
    }

    // Apply search if text is entered
    if (!empty($search_term)) {
        $query .= " AND movie_name LIKE :search_term";
        $params[':search_term'] = $search_term;
    }

    // Apply title sorting if selected
    if (!empty($title_sort)) {
        $query .= " ORDER BY movie_name $title_sort";
    } else {
        // Default ordering if no sort option selected
        $query .= " ORDER BY movie_id DESC";
    }

    // Prepare and execute the query with parameter binding
    $statement = $db->prepare($query);
    $statement->execute($params);
    $filtered_movies = $statement->fetchAll(PDO::FETCH_ASSOC);

    // Assign filtered results to the variable for display
    $user_watched_movies = $filtered_movies;
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Resource/Styles/defaultNav.css">
    <link rel="stylesheet" href="Resource/Styles/firstPage.css">

    <title>MovieConnect</title>
</head>

<body style="background-color: #1f1f1f;">
    <?php if (isset($_SESSION['user_id'])): ?>
        <nav class="defaultNav">
            <div class="navbar-top">
                <li class="nav-item"><a href="Resource/pages/userComments.php" class="nav-link"><?= $_SESSION['user'] ?>'s
                        Comments & Ratings</a></li>
            </div>
            <h1 class="site-title">MovieConnect</h1>
            <ol class="nav-list">
                <li class="nav-item"><a href="Resource/grabMovie/addMovie.php" class="nav-link">Request New Movie</a></li>
                <li class="nav-item"><a href="config/logout.php" class="nav-link logout">Log Out</a></li>
            </ol>
        </nav>
    <?php else: ?>
        <nav class="defaultNav">
            <h1 class="site-title">MovieConnect</h1>
            <li class="nav-item"><a href="login.html" class="nav-link login">Log In</a></li>
        </nav>
    <?php endif; ?>

    <?php if (isset($_SESSION['user_id'])): ?>
        <nav class="filter-nav">
            <!-- Filter Form -->
            <form action="index.php" method="POST" class="filter-form">
                <div class="filter-group">
                    <!-- <label for="rating">Rating:</label> -->
                    <select name="rating" id="rating" class="filter-select">
                        <option value="">Rating 1 to 9</option>
                        <option value="1">1+</option>
                        <option value="2">2+</option>
                        <option value="3">3+</option>
                        <option value="4">4+</option>
                        <option value="5">5+</option>
                        <option value="6">6+</option>
                        <option value="7">7+</option>
                        <option value="8">8+</option>
                        <option value="9">9+</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="genre">Genre:</label>
                    <select name="genre" id="genre" class="filter-select">
                        <option value="">Select a genre</option>
                        <option value="action">Action</option>
                        <option value="comedy">Comedy</option>
                        <option value="drama">Drama</option>
                        <option value="horror">Horror</option>
                        <option value="romance">Romance</option>
                        <option value="sci-fi">Sci-Fi</option>
                        <option value="thriller">Thriller</option>
                        <option value="animation">Animation</option>
                        <option value="documentary">Documentary</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="title">Title:</label>
                    <select name="title_sort" id="title" class="filter-select">
                        <option value="">Sort by title</option>
                        <option value="DESC">Z-A</option>
                        <option value="ASC">A-Z</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Apply Filters</button>
            </form>

            <!-- Search Form -->
            <form action="index.php" method="POST" class="search-form">
                <input type="text" id="search" name="search" class="input-text" placeholder="Search for a movie">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </nav>
    <?php endif; ?>
    <main>
        <div class="container">
            <div class="row">
                <?php foreach ($user_watched_movies as $movie): ?>
                    <div class="col-md-3 col-sm-6 mb-4">

                        <div class="card h-100">
                            <?php if ($movie['poster'] !== "Default.jpeg"): ?>
                                <img src="Resource/grabMovie/uploads/<?= $movie['poster'] ?>" class="card-img-top"
                                    alt="<?= $movie['movie_name'] ?>">
                            <?php else: ?>
                                <div class="card h-100 text-center p-3">
                                    <p><strong><?= $movie['movie_name'] ?></strong></p>
                                    <p>Poster not available for this movie</p>
                                </div>
                            <?php endif; ?>
                             <a href="Resource/pages/WatchedMovie.php?movie_id=<?= $movie['movie_id'] ?>"
                             style="text-decoration: none;">

                            <div class="card-body text-center">
                               
                                    <h3 class="card-title"><?= $movie['movie_name'] ?></h3>
                                    <p class="card-text"><strong>Genre:</strong> <?= $movie['genre'] ?></p>
                                    <p class="card-text"><strong>Year:</strong> <?= $movie['movie_year'] ?></p>
                                    <p class="card-text"><strong>Rating:</strong> <?= $movie['imdb_rating'] ?>/10</p>
                                    <p class="card-text"><strong>Language:</strong> <?= $movie['language'] ?></p>
                                    <p class="card-text"><strong>Director:</strong> <?= $movie['director'] ?></p>
                                    <p class="card-text"><strong>Plot:</strong>
                                        <?= substr($movie['movie_description'], 0, 100) ?>...</p>
                            </div>
                            </a>

                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>