<?php
require_once 'connect.php';
session_start();

function get_request_by_id($db, $request_id) {
    $query = "SELECT * FROM movie_request WHERE request_id = :request_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':request_id', $request_id);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
}

function approve_movie($db, $request_id)
{
    $request = get_request_by_id($db, $request_id);

    if (!$request) {
        echo "<div class='status error'>❌ Error: No request found with ID $request_id.</div>";
        return;
    }

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

    if ($insert_statement->execute()) {
        $update_query = "UPDATE movie_request SET status = :status WHERE request_id = :request_id";
        $update_statement = $db->prepare($update_query);
        $update_statement->bindValue(':status', "Approved");
        $update_statement->bindValue(':request_id', $request_id, PDO::PARAM_INT);
        $update_statement->execute();

        echo "<div class='status success'>✅ Movie approved and added to the database.</div>";
    } else {
        echo "<div class='status error'>❌ Failed to insert movie into the database.</div>";
    }
}

function reject_movie($db, $request_id)
{
    $request = get_request_by_id($db, $request_id);

    if (!$request) {
        echo "<div class='status error'>❌ Error: No request found with ID $request_id.</div>";
        return;
    }

    $update_query = "UPDATE movie_request SET status = :status WHERE request_id = :request_id";
    $update_statement = $db->prepare($update_query);
    $update_statement->bindValue(':status', "Rejected");
    $update_statement->bindValue(':request_id', $request_id, PDO::PARAM_INT);
    if ($update_statement->execute()) {
        echo "<div class='status reject'>❌ Movie request rejected.</div>";
    } else {
        echo "<div class='status error'>⚠️ Failed to reject the movie request.</div>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['request_id'])) {
        $request_id = filter_input(INPUT_POST, 'request_id', FILTER_SANITIZE_NUMBER_INT);
    }

    if (isset($_POST['approve'])) {
        approve_movie($db, $request_id);
    } elseif (isset($_POST['reject'])) {
        reject_movie($db, $request_id);
    } elseif (isset($_POST['edit'])) {
        echo "<div class='status info'>✏️ Edit action triggered for Request ID: $request_id</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MovieConnect - Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f4f9;
            padding: 2rem;
            color: #333;
        }

        .status {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 6px;
            font-weight: bold;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .reject {
            background-color: #fff3cd;
            color: #856404;
        }

        .info {
            background-color: #cce5ff;
            color: #004085;
        }

        a {
            display: inline-block;
            padding: 0.6rem 1rem;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <a href="../Resource/pages/Admin/admin.html">⬅️ Go Back</a>
</body>
</html>
