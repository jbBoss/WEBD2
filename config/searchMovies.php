<?php
require('connect.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Resource//Styles/home.css">
    <link rel="stylesheet" href="../Resource/Styles/popup.css">
    <head>
</head>
    <title>Add Movies </title>
</head>
<body>
<nav>
    <a href="../Resource/pages/admin.html"> Admin's Dashboard</a>
        
        
        <h1>MovieConnect</h1>
        <ol>
            <li><a href="watched.php">Watched</a></li>
            <li><a href="watchlist.php">WatchList</a></li>
            <li><a href="#">Profile</a></li>
            <li><a href="../../config/logout.php">Log Out</a></li>
        </ol>
    </nav>
    <main>
            
            <div class="search-field">
                <form class="homesearch " action=" ">
                    <input type="text"  id="searchBox" name="search" placeholder="Type a movie name..." autocomplete="off">
                    <button type="submit" class="btn">Search</button>
                </form>
            </div>
        <section id="results" class="container">  
            
        </section>

        <div id="popupCard" class="popup-card" style="display: none;">
            <div class="popup-content">
                <h1 id="movieName">Movie name</h2>
                <h3>movieGenere</h3>
                <p id="movieGenere">Here is the movie description...</p>
                <h3>movieRating</h3>
                <p id="movieRating">Here is the movie description...</p>
                <h3>movieDescription</h3>
                <p id="movieDescription">Here is the movie description...</p>
                <h3>movieDirector</h3>
                <p id="movieDirector">Here is the movie description...</p>
                <h3>movielanguage</h3>
                <p id="movielanguage">Here is the movie description...</p>
                <button class="btn" onclick="closePopup()">Close</button>
                <button class="btn" onclick="postToPhp()">Add to watch list</button>
            </div>
        </div>

        
    </main>
    <footer>
        <p> Lorem ipsum dolor sit amet consectetur adipisicing elit. .</p>
    </footer>
    <script src="adminScript.js"></script>
</body>
</html>