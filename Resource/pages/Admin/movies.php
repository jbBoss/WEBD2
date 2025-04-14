<?php
require_once '../../../config/connect.php'; // Ensure database connection file is included

try {
    $query = "SELECT * FROM movie_table
    ORDER BY movie_id DESC";
    $statement = $db->prepare($query);
    $statement->execute();
    $movie_data = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Styles/adminMovies.css">
    <title>Admin</title>
</head>

<body>
    <?php include '../../../config/adminnav.php'; ?>
    <br>
    <main>
        <div>
            <ol>
                <?php foreach ($movie_data as $movie): ?>
                    <li><a class="bigMovieButton" href="">
                            <div class="movie-card">
                                <div class="movie-info">
                                    <h5><?= htmlspecialchars($movie['movie_name']) ?></h5>
                                    <h6>Genre: <?= htmlspecialchars($movie['genre']) ?></h6>
                                    <h6>Year: <?= htmlspecialchars($movie['movie_year']) ?></h6>
                                    <p><strong>Rating:</strong> <?= htmlspecialchars($movie['imdb_rating']) ?></p>
                                    <p><strong>Description:</strong> <?= htmlspecialchars($movie['movie_description']) ?>
                                    </p>
                                    <p><strong>Director:</strong> <?= htmlspecialchars($movie['director']) ?></p>
                                    <p><strong>Language:</strong> <?= htmlspecialchars($movie['language']) ?></p>

                                    <a href="../../../config/deleteMovie.php?movie_id=<?= $movie['movie_id'] ?>">
                                        </button>
                                        <button>delete
                                    </a>


                                </div>

                                <?php if ($movie['poster'] !== "Default.jpeg"): ?>
                                    <img src="../../grabMovie/uploads/<?= $movie['poster'] ?>" class="card-img-top"
                                        alt="<?= $movie['movie_name'] ?>">
                                <?php else: ?>
                                    <div class="card h-100 text-center p-3">
                                        <p><strong><?= $movie['movie_name'] ?></strong></p>
                                        <p>Poster not available for this movie</p>
                                    </div>
                                <?php endif; ?>

                            </div>
                    </li></a>
                <?php endforeach; ?>
            </ol>
        </div>
    </main>


</body>

</html>