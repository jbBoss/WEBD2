<?php
require('../../config/connect.php');
session_start();
// Validate movie_id before running query
$movie_id = filter_input(INPUT_GET, 'movie_id',  FILTER_VALIDATE_INT);
if (!$movie_id || !is_numeric($movie_id)) {
    header("Location: ../../index.php");
    exit();
}

// Fetch movie details
$query = "SELECT * FROM movie_table WHERE movie_id = :movie_id";
$statement = $db->prepare($query);
$statement->bindValue(':movie_id', $movie_id, PDO::PARAM_INT);
$statement->execute();
$movie = $statement->fetch(PDO::FETCH_ASSOC);

// If no movie is found, redirect
if (!$movie) {
    header("Location: ../../index.php");
    exit();
}

// Fetch users who watched the movie
$query = "SELECT w.*, u.user_fname 
          FROM watched w
          JOIN user_table u ON w.user_id = u.user_id 
          WHERE w.movie_id = :movie_id";

$statement = $db->prepare($query);
$statement->bindValue(':movie_id', $movie_id, PDO::PARAM_INT);
$statement->execute();
$users = $statement->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Styles/popup.css">
    <link rel="stylesheet" href="../Styles/home.css">
    <link rel="stylesheet" href="../Styles/watchcard.css">
    <title>Comment</title>
</head>
<body>
<?php if (isset($_SESSION['user_id'])): ?>
        <nav>
            <a href="userComments.php"><?= $_SESSION['user'] ?>'s Comments & ratings</a>
            <h1>MovieConnect</h1>
            <ol>
                <li><a href="../grabMovie/addMovie.php">Request New movie</a></li>
                <li></li>
                <li><a href="../../config/logout.php">Log out</a></li>
            </ol>
        </nav>
    <?php else: ?>
        <nav>
            <h1>MovieConnect</h1>
            <li><a href="../..login.html">Log in</a></li>
        </nav>

    <?php endif; ?>

    <main>
        <div id="popupCard" class="popup-card" style="display: block;">
            <div class="popup-content">
                <div>
                    <h1 id="movieName"><?= htmlspecialchars($movie['movie_name']) ?></h1>
                    <h3>Genre</h3>
                    <p id="movieGenere"><?= htmlspecialchars($movie['genre']) ?></p>
                    <h3>Rating</h3>
                    <p id="movieRating"><?= htmlspecialchars($movie['imdb_rating']) ?>/10</p>
                    <h3>Description</h3>
                    <p id="movieDescription"><?= htmlspecialchars($movie['movie_description']) ?></p>
                    <h3>Director</h3>
                    <p id="movieDirector"><?= htmlspecialchars($movie['director']) ?></p>
                    <h3>Language</h3>
                    <p id="movielanguage"><?= htmlspecialchars($movie['language']) ?></p>
                    <form action="../../config/addComments.php" class="button-group" method="POST">
                        <input type="hidden" name="movie_id" value="<?= $movie['movie_id'] ?>"> 
                        <input class="btn btn-success" type="submit" name="add_comment" value="Add comment" >
                    </form>

                </div>
                <?php if ($movie['poster'] !== "Default.jpeg"): ?>
                                <img src="../grabMovie/uploads/<?= $movie['poster'] ?>" class="card-img-top"
                                    alt="<?= $movie['movie_name'] ?>">
                            <?php else: ?>
                                <div class="card h-100 text-center p-3">
                                    <p><strong><?= $movie['movie_name'] ?></strong></p>
                                    <p>Poster not available for this movie</p>
                                </div>
                            <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($users) && is_array($users)): ?>
            <?php foreach ($users as $user): ?>
                <div class = "commentNrating">
                    <h3>User: <?= htmlspecialchars($user['user_fname']) ?></h3>
                    <h4>Comment: <?= htmlspecialchars($user['comment']) ?></h4>
                    <h5><?= htmlspecialchars($user['user_fname']) ?>'s Rating: <?= htmlspecialchars($user['rating']) ?>/10</h5>
                    <p>Commented at: <?= date("F j, Y, g:i a", strtotime($user['time'])) ?></p>

                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No comments yet.</p>
        <?php endif; ?>
        
    </main>

    <!-- <script>
        function postToPhp() {
            fetch('/check-session.php')
    .then(response => response.json())
    .then(data => {
        if (data.loggedIn) {
            console.log("User is logged in:", data.username);
        } else {
            console.log("No active session.");
        }
    })
    .catch(error => console.error("Error:", error));
            alert('Log in to comment!');
            window.location.href = "../../login.html";
        }
    </script> -->
</body>
</html>
