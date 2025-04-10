<?php
require('../../config/connect.php');
require('../../config/sessionCheck.php');

$current_user = $_SESSION['user_id'];
$current_user_name = $_SESSION['user'];

$watch_id = filter_input(INPUT_POST, 'watch_id', FILTER_SANITIZE_NUMBER_INT); // Getting the movie ID from the URL for viewing

// echo "The movie ID is: " . htmlspecialchars($watch_id);

$query = "SELECT m.*, w.* 
                  FROM movie_table m 
                  JOIN watched w ON m.movie_id = w.movie_id
                  WHERE w.user_id = :user_id AND w.watch_id = :watch_id";
    
        $statement = $db->prepare($query);
        $statement->bindParam(':user_id', $current_user, PDO::PARAM_INT);
        $statement->bindParam(':watch_id', $watch_id, PDO::PARAM_INT);
    
        $statement->execute();
        $movie = $statement->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check if the watch_id is set and sanitize the input
    if (isset($_POST['edit'])) {
        $query = "SELECT m.*, w.* 
                  FROM movie_table m 
                  JOIN watched w ON m.movie_id = w.movie_id
                  WHERE w.user_id = :user_id AND w.watch_id = :watch_id";
    
        $statement = $db->prepare($query);
        $statement->bindParam(':user_id', $current_user, PDO::PARAM_INT);
        $statement->bindParam(':watch_id', $watch_id, PDO::PARAM_INT);
    
        $statement->execute();
        $movie = $statement->fetch(PDO::FETCH_ASSOC);
    
        // Optional: Debug output
        if (!$movie) {
            echo "Movie not found or no permission.";
        }
    }
    if (isset($_POST['delete'])) {
        echo "Watch ID: " . htmlspecialchars($watch_id) . "<br>";

        echo "deleting Comment" . "<br>";

        $query = "DELETE FROM watched WHERE watch_id = :watch_id";

        $statement = $db->prepare($query);
        $statement->bindValue(':watch_id', $watch_id);
        
        if ($statement->execute()) {
            echo ''. htmlspecialchars($watch_id) . '<br>';
            echo "deleted";
            header("Location: userComments.php");
            exit(); // always use exit after header redirection
        } else {
            echo "Error: Failed to update record.";
        }
        

    }
    if (isset($_POST['update_comment']) && isset($_POST['comment']) ) {
        if (isset($_POST['rating'])){
            $rating = filter_input(INPUT_POST, 'rating', FILTER_SANITIZE_NUMBER_INT);
            
        }else{
            echo "Rating Required";
        }
        $watch_id = filter_input(INPUT_POST, 'update_comment', FILTER_SANITIZE_NUMBER_INT);

        $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);
        

        // Insert comment and rating into the database
        $query = "UPDATE watched 
          SET comment = :comment, rating = :rating 
          WHERE watch_id = :watch_id";

        $statement = $db->prepare($query);
        $statement->bindValue(':comment', $comment);
        $statement->bindValue(':rating', $rating);
        $statement->bindValue(':watch_id', $watch_id, PDO::PARAM_INT);

        if ($statement->execute()) {
            echo "deleted";
            header("Location: userComments.php");
            exit(); // always use exit after header redirection

        } else {
            echo "Error: Failed to update record.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Styles/home.css">
    <link rel="stylesheet" href="../Styles/watchcard.css">
    <link rel="stylesheet" href="../Styles/popup.css">

    <title>Comment & Rate</title>
</head>

<body>
    <?php if (isset($_SESSION['user_id'])): ?>
        <nav>
            <a href=""><?= $_SESSION['user'] ?>'s Comments & ratings</a>
            <h1>MovieConnect</h1>
            <ol>
                <li><a href="../Resources/pages/grabMovie/addMovie.php">Request New movie</a></li>
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
            <div class="movie-card">
            <?php if ($movie['poster'] !== "Default.jpeg"): ?>
                                <img src="../grabMovie/uploads/<?= $movie['poster'] ?>" class="card-img-top"
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
                    <p class="movie-description"><?= ($movie['movie_description']) ?></p>
                </div>

                <!-- Comment and Rating Form -->
                <form class="comment_n_rating" action="updateComment.php" method="post">
                    <div class="comment">
                        <label for="comment">Comment</label>
                        <textarea name="comment" required><?= ($movie['comment']) ?></textarea>
                    </div>

                    <!-- Movie ID Hidden Input -->
                    <input type="hidden" name="update_comment" value="<?= $movie['watch_id'] ?>">

                    <div>
                        <label for="rating">Personal Rating : <?= htmlspecialchars($movie['rating']) ?></label>
                        <div class="rating-options">
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <input type="radio" id="rating<?= $i ?>" name="rating" value="<?= $i ?>"
                                    <?= (isset($_POST['rating']) && $_POST['rating'] == $i) ? 'checked' : '' ?>>
                                <label for="rating<?= $i ?>"><?= $i ?></label>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <button type="submit">Update</button>
                </form>
            </div>
    </main>
</body>

</html>