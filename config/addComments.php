<?php
require('connect.php');
require('sessionCheck.php');

$current_user = $_SESSION['user_id'];
$current_user_name = $_SESSION['user'];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $movie_id = filter_input(INPUT_GET, 'movie_id', FILTER_SANITIZE_NUMBER_INT); // Getting the movie ID from the URL for viewing
// echo 'movie' . $movie_id . '<br>';
// Display the movie_id
// echo "The movie ID is: " . htmlspecialchars($movie_id);

    // Check if the mo
    $query = "SELECT * FROM movie_table WHERE movie_id = :movie_id";
    $statement = $db->prepare($query);
    $statement->bindParam(':movie_id', $movie_id, PDO::PARAM_INT);
    // Execute the query
    $statement->execute();
    // Fetch the movie data (assuming it's only one movie)
    $movie = $statement->fetch(PDO::FETCH_ASSOC);

    if ($movie) {
        // echo "Movie Found: " . htmlspecialchars($movie['movie_name']);
    } else {
        header("Location: ../index.php");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']) && isset($_POST['rating'])) {


    if (empty(trim($_POST['comment']))) {
        echo "Error: Failed to update record Fill all fields.";
        $movie_id = filter_input(INPUT_POST, 'movie_id', FILTER_SANITIZE_NUMBER_INT);
        $query = "SELECT * FROM movie_table WHERE movie_id = :movie_id";
        $statement = $db->prepare($query);
        $statement->bindParam(':movie_id', $movie_id, PDO::PARAM_INT);
        // Execute the query
        $statement->execute();
        // Fetch the movie data (assuming it's only one movie)
        $movie = $statement->fetch(PDO::FETCH_ASSOC);


    } else {

        $movie_id = filter_input(INPUT_POST, 'movie_id', FILTER_SANITIZE_NUMBER_INT);
        $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);
        $rating = filter_input(INPUT_POST, 'rating', FILTER_SANITIZE_NUMBER_INT);

        $query = "INSERT INTO watched (movie_id, user_id, comment, rating) VALUES (:movie_id, :user_id, :comment, :rating)";
        $statement = $db->prepare($query);
        $statement->bindValue(':comment', $comment);
        $statement->bindValue(':user_id', $current_user);
        $statement->bindValue(':rating', $rating);
        $statement->bindValue(':movie_id', $movie_id, PDO::PARAM_INT);

        if ($statement->execute()) {
            header("Location: ../Resource/pages/WatchedMovie.php?movie_id=" . $movie_id);
            exit();
        } else {

            echo "Error: Failed to update record .";
        }
    }
    echo " Insert comment and rating into the Fields";

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Resource/Styles/home.css">
    <link rel="stylesheet" href="../Resource/Styles/watchcard.css">
    <title>Comment & Rate</title>
</head>

<body>
    <?php if (isset($_SESSION['user_id'])): ?>
        <nav>
            <a href=""><?= $_SESSION['user'] ?>'s Comments & ratings</a>
            <h1>MovieConnect</h1>
            <ol>
                <li><a href="../Resource/grabMovie/addmovie.php">Request New movie</a></li>
                <li></li>
                <li><a href="logout.php">Log out</a></li>
            </ol>
        </nav>
    <?php else: ?>
        <nav>
            <h1>MovieConnect</h1>
            <li><a href="login.html">Log in</a></li>
        </nav>

    <?php endif; ?>
    <main>
        <?php if (isset($movie)): ?>
            <div class="movie-card">
                <?php if ($movie['poster'] !== "Default.jpeg"): ?>
                    <img src="../Resource/grabMovie/uploads/<?= $movie['poster'] ?>" class="card-img-top"
                        alt="<?= $movie['movie_name'] ?>">
                <?php else: ?>
                    <div class="card h-100 text-center p-3">
                        <p><strong><?= $movie['movie_name'] ?></strong></p>
                        <p>Poster not available for this movie</p>
                    </div>
                <?php endif; ?>
                <div>
                    <h2 class="movie-title"><?= htmlspecialchars($movie['movie_name']) ?></h2>
                    <p class="movie-year"><?= htmlspecialchars($movie['movie_year']) ?></p>
                    <p class="movie-genre"><?= htmlspecialchars($movie['genre']) ?></p>
                    <p class="movie-description"><?= htmlspecialchars($movie['movie_description']) ?></p>
                </div>

                <form class="comment_n_rating" action="addComments.php" method="post">
                    <div class="comment">
                        <label for="comment">Comment</label>
                        <textarea name="comment" required></textarea>
                    </div>

                    <input type="hidden" name="movie_id" value="<?= $movie['movie_id'] ?>">

                    <div>
                        <label for="rating">Personal Rating</label>
                        <div class="rating-options">
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <input required type="radio" id="rating<?= $i ?>" name="rating" value="<?= $i ?>"
                                    <?= (isset($_POST['rating']) && $_POST['rating'] == $i) ? 'checked' : '' ?>>
                                <label for="rating<?= $i ?>"><?= $i ?></label>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <button type="submit">Post</button>
                </form>
            </div>
        <?php else: ?>
            <p>No movie details found.</p>
        <?php endif; ?>
    </main>
</body>

</html>