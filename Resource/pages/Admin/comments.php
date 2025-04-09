<?php
require_once '../../../config/connect.php'; // Ensure database connection file is included


$query = "SELECT m.*, w.*, u.user_fname
          FROM movie_table m
          JOIN watched w ON m.movie_id = w.movie_id
          JOIN user_table u ON w.user_id = u.user_id
          ORDER BY w.time DESC";

$statement = $db->prepare($query);
$statement->execute();

    
    // Fetch the user data
    $user_watched_movies = $statement->fetchAll(PDO::FETCH_ASSOC);
    
    // echo json_encode($user_watched_movies);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST['watch_id'])){
            $watch_id = filter_input(INPUT_POST, 'watch_id', FILTER_SANITIZE_NUMBER_INT);

            $query = "UPDATE watched SET comment = :comment WHERE watch_id = :watch_id";
            $statement = $db->prepare($query);
            $statement->bindValue(':comment', "THIS COMMENT WAS DELETED");        
            $statement->bindValue(':watch_id', $watch_id, PDO::PARAM_INT);
            $statement->execute();
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
    <link rel="stylesheet" href="../../Styles/comments.css">
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
                        <img src="../../grabMovie/uploads/<?= htmlspecialchars($movie['poster']) ?>" alt="<?= htmlspecialchars($movie['movie_name']) ?> poster">
                        <div class="movie-info">
                            <h3><?= htmlspecialchars($movie['movie_name']) ?></h5>
                            <h6>Genre: <?= htmlspecialchars($movie['genre']) ?></h6>
                            <h6>Year: <?= htmlspecialchars($movie['movie_year']) ?></h6>
                            <p><strong>Rating:</strong> <?= htmlspecialchars($movie['imdb_rating']) ?></p>
                            <p><strong>Director:</strong> <?= htmlspecialchars($movie['director']) ?></p>
                            <p><strong>Language:</strong> <?= htmlspecialchars($movie['language']) ?></p>
                        </div>
                        
                        <div class="userCommentsAndRating">
                        <h2><?= htmlspecialchars($movie['user_fname']) ?></h5>
                            <h5>Comment: <?= htmlspecialchars($movie['comment']) ?></h6>
                            <p><strong> Personal Rating:</strong> <?= htmlspecialchars($movie['imdb_rating']) ?></p>
                        </div>
                        <form  action="comments.php" class="button-group" method="POST">
                                <input type="hidden" name="watch_id" value=" <?=($movie['watch_id']) ?>"> 
                                <input class= "deleteButton" type="submit" name="remove" class="remove-btn" value="delete comment" onclick="return confirm('delete this comment ?')">
                        </form>
                                               
                    </div>
                    
                    
                </li></a>
            <?php endforeach; ?>
        </ol>
    </div>
</main>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
