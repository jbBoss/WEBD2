<?php
require_once 'connect.php'; // Ensure database connection file is included
session_start(); // Make sure session is started



function approve_movie($db, $request_id)
{

    $query = "SELECT *
          FROM movie_request
          WHERE request_id = :request_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':request_id', $request_id);
    $statement->execute();
    $request = $statement->fetch(PDO::FETCH_ASSOC);
    echo json_encode($request, JSON_PRETTY_PRINT);

    $insert_query = "INSERT INTO movie_table (movie_name, genre, imdb_rating, director, language, poster, movie_year, movie_description, imdb_id) 
                      VALUES (:movie_name, :genre, :imdb_rating, :director, :language, :poster, :year, :description, :imdb_id)";

    $insert_statement = $db->prepare($insert_query);
    $insert_statement->bindValue(':movie_name', $request['movie_name']);
    $insert_statement->bindValue(':genre', $request['genre']);
    $insert_statement->bindValue(':imdb_rating', $request['imdb_rating']);
    $insert_statement->bindValue(':director', $request['director']);
    $insert_statement->bindValue(':language', $request['language']);
    $insert_statement->bindValue(':poster', $request['poster']);
    $insert_statement->bindValue(':year', $request['movie_year']);
    $insert_statement->bindValue(':description', $request['movie_description']);
    $insert_statement->bindValue(':imdb_id', $request['imdb_id']);

    $insert_statement->execute();

    $query = "UPDATE movie_request 
          SET status = :status
          WHERE request_id = :request_id";
    $update_statement = $db->prepare($query);
    $update_statement->bindValue(':status', "Approved");
    $update_statement->bindValue(':request_id', $request_id, PDO::PARAM_INT);
    $update_statement->execute();

}

function reject_movie($db, $request_id){
    $query = "SELECT *
    FROM movie_request
    WHERE request_id = :request_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':request_id', $request_id);
    $statement->execute();
    $request = $statement->fetch(PDO::FETCH_ASSOC);
    echo json_encode($request, JSON_PRETTY_PRINT);
    $query = "UPDATE movie_request 
          SET status = :status
          WHERE request_id = :request_id";
    $update_statement = $db->prepare($query);
    $update_statement->bindValue(':status', "Rejected");
    $update_statement->bindValue(':request_id', $request_id, PDO::PARAM_INT);
    $update_statement->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['request_id'])) {
        $request_id = filter_input(INPUT_POST, 'request_id', FILTER_SANITIZE_NUMBER_INT);
    }
    if (isset($_POST['approve'])) {
        echo "Approved", "<br>";
        echo $_POST['request_id'] . "<br>";
        approve_movie($db, $request_id);

    } elseif (isset($_POST['reject'])) {
        echo 'reject', '<br>';
        echo $_POST['request_id'] . "<br>";
        reject_movie($db, $request_id);

    } elseif (isset($_POST['edit'])) {
        echo $_POST['request_id'] . "<br>";
        echo 'edit', '<br>';
    }
}
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
<a href="../Resource\pages\Admin\admin.html"> go back</a>
</body>

</html>