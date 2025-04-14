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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search_term = isset($_POST['search']) ? '%' . $_POST['search'] . '%' : '';

    $query = "SELECT m.*, w.*
              FROM movie_table m
              JOIN watched w ON m.movie_id = w.movie_id
              WHERE m.movie_name LIKE :search_term 
              ORDER BY m.movie_name ASC";

    $statement = $db->prepare($query);
    $statement->bindValue(':search_term', $search_term, PDO::PARAM_STR); 
    $statement->execute();


    $movie_data = $statement->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link rel="stylesheet" href="../Styles/home.css"> -->
    <link rel="stylesheet" href="../../Styles/movies.css">
    <title>Admin</title>
</head>

<body>
    <?php include '../../../config/adminnav.php'; ?>
    <form action="" method="POST" class="search-form">
            <input type="text" id="search" name="search" class="input-text" placeholder="Search for a movie">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    <br>
    <main>
        <div>
            <ol>
                <?php foreach ($movie_data as $movie): ?>
                    <li><a class="bigMovieButton"
                            href="../../grabMovie/editMovieDetail.php?movie_id=<?= $movie['movie_id'] ?>">
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>