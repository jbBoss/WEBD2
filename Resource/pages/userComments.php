<?php
require_once '../../config/connect.php'; // Ensure database connection file is included
require '../../config/sessionCheck.php';
$current_user = $_SESSION['user_id'];
$query = "SELECT m.*, w.*
          FROM movie_table m
          JOIN watched w ON m.movie_id = w.movie_id
          WHERE w.user_id = :user_id
          ORDER BY w.time DESC";

$statement = $db->prepare($query);
$statement->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$statement->execute();



// Fetch the user data
$user_commented_movies = $statement->fetchAll(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search_term = isset($_POST['search']) ? '%' . $_POST['search'] . '%' : '';

    $query = "SELECT m.*, w.*
              FROM movie_table m
              JOIN watched w ON m.movie_id = w.movie_id
              WHERE w.user_id = :user_id AND m.movie_name LIKE :search_term 
              ORDER BY m.movie_name ASC";

    $statement = $db->prepare($query);
    $statement->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $statement->bindValue(':search_term', $search_term, PDO::PARAM_STR); // missing in your original
    $statement->execute();


    $user_commented_movies = $statement->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/comments.css">
    <link rel="stylesheet" href="../Styles/defaultNav.css">

    <title>Admin</title>
</head>

<body>
    <?php if (isset($_SESSION['user_id'])): ?>
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
        <form action="" method="POST" class="search-form">
            <input type="text" id="search" name="search" class="input-text" placeholder="Search for a movie">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    <?php else: ?>
        <nav>
            <h1>MovieConnect</h1>
            <li><a href="../..login.html">Log in</a></li>
        </nav>

    <?php endif; ?>

    <br>
    <main>
    <div>
    <ol>
<?php foreach ($user_commented_movies as $movie): ?>
    <li>
        <div class="movie-card">
            <?php if ($movie['poster'] !== "Default.jpeg"): ?>
                <img src="../grabMovie/uploads/<?= $movie['poster'] ?>" class="card-img-top" alt="<?= $movie['movie_name'] ?>">
            <?php else: ?>
                <img src="fallback.jpg" class="card-img-top" alt="No Poster Available">
            <?php endif; ?>
            
            <div class="movie-info">
                <h3>
                    <a class="bigMovieButton" href="#" style="color: #e64a19; text-decoration: none;">
                        <?= htmlspecialchars($movie['movie_name']) ?>
                    </a>
                </h3>
                <h6>Genre: <?= htmlspecialchars($movie['genre']) ?></h6>
                <h6>Year: <?= htmlspecialchars($movie['movie_year']) ?></h6>
                <p><strong>Rating:</strong> <?= htmlspecialchars($movie['imdb_rating']) ?></p>
                <p><strong>Director:</strong> <?= htmlspecialchars($movie['director']) ?></p>
                <p><strong>Language:</strong> <?= htmlspecialchars($movie['language']) ?></p>
                <p><strong>Commented On:</strong> <?= date("F j, Y, g:i a", strtotime($movie['time'])) ?></p>
            </div>

            <div class="userCommentsAndRating">
                <h3><strong>Comment:</strong> <?= htmlspecialchars($movie['comment']) ?></h3>
                <p><strong>Personal Rating:</strong> <?= htmlspecialchars($movie['rating']) ?></p>
                <div class="deleteButton">
                    <a class="edit-btn" href="updateComment.php?watch_id=<?= $movie['watch_id'] ?>" onclick="return confirm('edit this comment?')">Edit</a>
                    <a class="delete-btn" href="updateComment.php?Delete_id=<?= $movie['watch_id'] ?>" onclick="return confirm('delete this comment?')">Delete</a>
                </div>
            </div>
        </div>
    </li>
<?php endforeach; ?>
</ol>

    </div>
</main>


</body>

</html>