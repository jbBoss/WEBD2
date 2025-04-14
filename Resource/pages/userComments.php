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


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/comments.css">
    <link rel="stylesheet" href="../Styles/home.css">

    <title>Admin</title>
</head>

<body>
    <?php if (isset($_SESSION['user_id'])): ?>
        <nav>
            <ol>
                <li><a href="userComments.php"><?= $_SESSION['user'] ?>'s Comments & ratings</a></li>
                <li><a href="topComments.php">Most Commented</a></li>
                <li><a href="topRated.php">Top rated</a></li>
            </ol>
            <h1 class="site-title"><a href="../../index.php">MovieConnect</a></h1>
            <ol>
                <li><a href="../grabMovie/addMovie.php">Request New movie</a></li>
                <li><a href="../../config/logout.php">Log out</a></li>
            </ol>
        </nav>
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
                    <li><a class="bigMovieButton" href="">
                            <div class="movie-card">
                                <?php if ($movie['poster'] !== "Default.jpeg"): ?>
                                    <img src="..//grabMovie/uploads/<?= $movie['poster'] ?>" class="card-img-top"
                                        alt="<?= $movie['movie_name'] ?>">
                                <?php else: ?>
                                    <div class="card h-100 text-center p-3">
                                        <p><strong><?= $movie['movie_name'] ?></strong></p>
                                        <p>Poster not available for this movie</p>
                                    </div>
                                <?php endif; ?>
                                <div class="movie-info">
                                    <h3><?= htmlspecialchars($movie['movie_name']) ?></h5>
                                        <h6>Genre: <?= htmlspecialchars($movie['genre']) ?></h6>
                                        <h6>Year: <?= htmlspecialchars($movie['movie_year']) ?></h6>
                                        <p><strong>Rating:</strong> <?= htmlspecialchars($movie['imdb_rating']) ?></p>
                                        <p><strong>Director:</strong> <?= htmlspecialchars($movie['director']) ?></p>
                                        <p><strong>Language:</strong> <?= htmlspecialchars($movie['language']) ?></p>
                                        <br>
                                        <p><strong>Commented On:</strong>
                                            <?= date("F j, Y, g:i a", strtotime($movie['time'])) ?></p>
                                </div>

                                <div class="userCommentsAndRating">
                                    <h3><strong></strong>Comment: </strong><?= ($movie['comment']) ?></h3>
                                    <p><strong> Personal Rating:</strong> <?= htmlspecialchars($movie['rating']) ?></p>
                                    <div class="deleteButton">
                                        <a class="edit-btn" href="updateComment.php?watch_id=<?= $movie['watch_id'] ?>"
                                            onclick="return confirm('edit this comment ?')"> Edit </a>
                                        <a class="delete-btn" href="updateComment.php?Delete_id=<?= $movie['watch_id'] ?>"
                                            onclick="return confirm('delete this comment ?')"> Delete </a>
                                    </div>

                                </div>


                            </div>


                    </li></a>
                <?php endforeach; ?>
            </ol>
        </div>
    </main>

</body>

</html>