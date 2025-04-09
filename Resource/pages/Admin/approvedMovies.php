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
    <br>
    <main>
        <div>
            <ol>
                <div class="container">
                    <div class="row">
                        <?php foreach ($movies as $movie): ?>
                            <li><a class="bigMovieButton" href="">
                                    <div class="movie-card">
                                        <img src="../../grabMovie/uploads/<?= htmlspecialchars($movie['poster']) ?>"
                                            alt="<?= htmlspecialchars($movie['movie_name']) ?> poster">
                                        <div class="movie-info">
                                            <h3><?= htmlspecialchars($movie['movie_name']) ?></h5>
                                                <h6>Genre: <?= htmlspecialchars($movie['genre']) ?></h6>
                                                <h6>Year: <?= htmlspecialchars($movie['movie_year']) ?></h6>
                                                <p><strong>Rating:</strong> <?= htmlspecialchars($movie['imdb_rating']) ?>
                                                </p>
                                                <p><strong>Director:</strong> <?= htmlspecialchars($movie['director']) ?>
                                                </p>
                                                <p><strong>Language:</strong> <?= htmlspecialchars($movie['language']) ?>
                                                </p>
                                                <p><strong>IMDB ID:</strong> <?= ($movie['imdb_id']) ?>
                                                </p>
                                                <br>
                                                <p><strong>User: </strong><?= $movie['user_id'] ?></p>
                                                <p><strong>Requested On:</strong>
                                                    <?= date("F j, Y, g:i a", strtotime($movie['time'])) ?></p>
                                        </div>

                                        <div class="userCommentsAndRating">
                                            <!-- <form action="../../../config/approveSubmission.php" class="button-group"
                                                method="POST">
                                                <div class="deleteButton">
                                                    <input type="hidden" name="request_id"
                                                        value=" <?= ($movie['request_id']) ?>">
                                                    <input class="" type="submit" name="approve" class="remove-btn"
                                                        value="Approve"
                                                        onclick="return confirm('Approve this Submission ?')">
                                                    <input class="" type="submit" name="reject" class="remove-btn"
                                                        value="Reject"
                                                        onclick="return confirm('Reject this Suubmission ?')">

                                                    <input class="" type="submit" name="edit" class="remove-btn"
                                                        value="Edit Details"> -->

                                                </div>
                                            </form> 
                                        </div>

                                    </div>


                            </li></a>
                            <br>
                        <?php endforeach; ?>
                    </div>
                </div>
            </ol>
        </div>
    </main>

</body>

</html>