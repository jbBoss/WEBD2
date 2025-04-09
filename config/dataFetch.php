<?php
$apiKey = "a2c500fb";
$movieTitle = "Inception";
$url = "http://www.omdbapi.com/?t=" . urlencode($movieTitle) . "&apikey=" . $apiKey;

$response = file_get_contents($url);
$data = json_decode($response, true);

if ($data["Response"] == "True") {
    echo "Title: " . $data["Title"] . "<br>";
    echo "Year: " . $data["Year"] . "<br>";
    echo "IMDB Rating: " . $data["imdbRating"] . "<br>";
    echo "Plot: " . $data["Plot"] . "<br>";
} else {
    echo "Error: " . $data["Error"];
}
?>
