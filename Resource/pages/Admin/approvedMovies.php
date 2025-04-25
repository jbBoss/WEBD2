<?php
require_once '../../../config/connect.php'; // Ensure database connection file is included
session_start(); // Make sure session is started

$query = $query = "SELECT *
                    FROM movie_request
                    WHERE status = 'Approved'
                    ORDER BY time DESC";        
;
$statement = $db->prepare($query);
$statement->execute();
$movies = $statement->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="request.css">
    <title>MovieConnect</title>
</head>

<body>
<?php include '../../../config/adminnav.php'; ?>

    <br>
    <main>
    <div class="container">
        <div class="row">
            <ol>
                <?php foreach ($movies as $movie): ?>
                    <li>
                        <div class="movie-card bigMovieButton">
                            <?php if ($movie['poster'] !== "Default.jpeg"): ?>
                                <img src="../../grabMovie/uploads/<?= $movie['poster'] ?>" class="card-img-top"
                                    alt="<?= htmlspecialchars($movie['movie_name']) ?>">
                            <?php else: ?>
                                <div class="card h-100 text-center p-3">
                                    <p><strong><?= htmlspecialchars($movie['movie_name']) ?></strong></p>
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
                                <p><strong>IMDB ID:</strong> <?= htmlspecialchars($movie['imdb_id']) ?></p>
                                <p><strong>User:</strong> <?= htmlspecialchars($movie['user_id']) ?></p>
                                <p><strong>Requested On:</strong> <?= date("F j, Y, g:i a", strtotime($movie['time'])) ?></p>
                            </div>
                        </div>
                    </li>
                    <br>
                <?php endforeach; ?>
            </ol>
        </div>
    </div>
</main>


</body>

</html>