<?php
require_once '../../../config/connect.php'; // Ensure database connection file is included


$query = "SELECT m.*, w.*, u.user_fname
          FROM movie_table m
          JOIN watched w ON m.movie_id = w.movie_id
          JOIN user_table u ON w.user_id = u.user_id
          ORDER BY w.time DESC";

$statement = $db->prepare($query);
$statement->execute();

function disemvowel($text)
{
    return preg_replace('/[aeiouAEIOU]/', '', $text);
}

// Fetch the user data
$user_watched_movies = $statement->fetchAll(PDO::FETCH_ASSOC);

// echo json_encode($user_watched_movies);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['watch_id']) && isset($_POST['moderate'])) {
        $watch_id = filter_input(INPUT_POST, 'watch_id', FILTER_SANITIZE_NUMBER_INT);

        $query = "SELECT comment
          FROM watched WHERE watch_id = :watch_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':watch_id', $watch_id, PDO::PARAM_INT);
        $statement->execute();
        $user_comment = $statement->fetch(PDO::FETCH_ASSOC);


        $orginal_comment = $user_comment['comment'];

        $disemvoweledComment = disemvowel($orginal_comment);

        $query = "UPDATE watched SET comment = :comment WHERE watch_id = :watch_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':comment', $disemvoweledComment);
        $statement->bindValue(':watch_id', $watch_id, PDO::PARAM_INT);
        $statement->execute();
         // Redirect to avoid resubmission
    header("Location: comments.php");
    exit();
    }
    if (isset($_POST['watch_id']) && isset($_POST['remove'])) {
        $watch_id = filter_input(INPUT_POST, 'watch_id', FILTER_SANITIZE_NUMBER_INT);

        $query = "DELETE FROM watched WHERE watch_id = :watch_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':watch_id', $watch_id, PDO::PARAM_INT);
        $statement->execute();
         // Redirect to avoid resubmission
    header("Location: comments.php");
    exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link rel="stylesheet" href="../Styles/home.css"> -->
    <link rel="stylesheet" href="../../Styles/admincomments.css">
    <title>Admin</title>
</head>

<body>
    <?php include '../../../config/adminnav.php'; ?>
    <br>
    <main>
        <div>
            <ol>
                <?php foreach ($user_watched_movies as $movie): ?>
                    <li><a class="bigMovieButton" href="">
                            <div class="movie-card">
                            <?php if ($movie['poster'] !== "Default.jpeg"): ?>
                                <img src="../../grabMovie/uploads/<?= $movie['poster'] ?>" class="card-img-top"
                                    alt="<?= $movie['movie_name'] ?>">
                            <?php else: ?>
                                <div class="card h-100 text-center p-3">
                                    <p><strong><?= $movie['movie_name'] ?></strong></p>
                                    <p>Poster not available for this movie</p>
                                </div>
                            <?php endif; ?>
                                <div class="movie-info">
                                    <h3><?= htmlspecialchars($movie['movie_name']) ?></h3>
                                        <h6>Genre: <?= htmlspecialchars($movie['genre']) ?></h6>
                                        <h6>Year: <?= htmlspecialchars($movie['movie_year']) ?></h6>
                                        <p><strong>Rating:</strong> <?= htmlspecialchars($movie['imdb_rating']) ?></p>
                                        <p><strong>Director:</strong> <?= htmlspecialchars($movie['director']) ?></p>
                                        <p><strong>Language:</strong> <?= htmlspecialchars($movie['language']) ?></p>
                                </div>

                                <div class="userCommentsAndRating">
                                    <h2><?= htmlspecialchars($movie['user_fname']) ?></h2>
                                        <h5>Comment: <?= ($movie['comment']) ?></h5>
                                            <p><strong> Personal Rating:</strong>
                                                <?= htmlspecialchars($movie['imdb_rating']) ?></p>
                                </div>
                                <form action="comments.php" class="button-group" method="POST">
                                    <input type="hidden" name="watch_id" value="<?=($movie['watch_id'])?>">
                                    <input class="DisemvowelButton" type="submit" name="moderate" class="remove-btn"
                                        value="Disemvowel comment" onclick="return confirm('Confirm Action? ')">
                                    <input class="deleteButton" type="submit" name="remove" class="remove-btn"
                                        value="delete comment" onclick="return confirm('delete this comment ?')">
                                </form>

                            </div>


                    </a> </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </main>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>