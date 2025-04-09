console.log("hello"); // Fixed typo

const API_KEY = "a2c500fb"; // Replace with your OMDb API key
const searchBox = document.getElementById("searchBox");
const resultsDiv = document.getElementById("results");

let timeout = null;
let currentMovieData = null; // Store the current movie data for use in postToPhp

searchBox.addEventListener("input", function () {
    clearTimeout(timeout);
    const query = searchBox.value.trim();

    if (query.length < 3) {
        resultsDiv.innerHTML = "";
        return;
    }

    timeout = setTimeout(() => {
        fetchMovies(query);
    }, 300); // Debounce time (300ms)
});

async function fetchMovies(query) {
    const url = `https://www.omdbapi.com/?s=${query}&apikey=${API_KEY}`;
    try {
        const response = await fetch(url);
        const data = await response.json();

        if (data.Search) {
            displayResults(data.Search);
        } else {
            resultsDiv.innerHTML = "<p>No results found</p>";
        }
    } catch (error) {
        console.error("Error fetching data:", error);
    }
}

function displayResults(movies) {
    resultsDiv.innerHTML = "";
    
    const row = document.createElement("div");
    row.classList.add("row");

    movies.forEach((movie) => {
        const col = document.createElement("div");
        const buttondiv = document.createElement("div");
        col.classList.add("col-md-3", "mb-4"); // 4 columns per row

        const card = document.createElement("div");
        card.classList.add("card", "bg-dark", "text-white", "h-100");

        const img = document.createElement("img");
        img.classList.add("card-img-top");
        img.src = movie.Poster !== "N/A" ? movie.Poster : "https://via.placeholder.com/150";
        img.alt = movie.Title;

        const cardBody = document.createElement("div");
        cardBody.classList.add("card-body");

        const title = document.createElement("h5");
        title.classList.add("card-title");
        title.textContent = movie.Title;

        const text = document.createElement("p");
        text.classList.add("card-text");
        text.textContent = `Year: ${movie.Year}`;

        const link = document.createElement("a");
        link.classList.add("btn", "btn-primary", "mr-2");
        link.href = `https://www.imdb.com/title/${movie.imdbID}/`;
        link.target = "_blank";
        link.textContent = "View on IMDb";

        const view = document.createElement("a");
        view.classList.add("btn", "movieDetail", "btn-primary");
        view.href = "#";
        view.setAttribute("data-imdbid", movie.imdbID); // Store imdbID for later use
        view.textContent = "View More";

        buttondiv.appendChild(link);
        buttondiv.appendChild(view);

        cardBody.appendChild(title);
        cardBody.appendChild(text);
        cardBody.appendChild(buttondiv);

        card.appendChild(img);
        card.appendChild(cardBody);

        col.appendChild(card);
        row.appendChild(col);
    });

    resultsDiv.appendChild(row);

    // Add event listeners dynamically for each "View More" button
    document.querySelectorAll('.movieDetail').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default anchor behavior
            const imdbID = e.target.getAttribute('data-imdbid');
            fetchMovieDetails(imdbID); // Fetch detailed information for the movie
        });
    });
}

// Fetch detailed movie info
async function fetchMovieDetails(imdbID) {
    const url = `https://www.omdbapi.com/?i=${imdbID}&apikey=${API_KEY}`;
    try {
        const response = await fetch(url);
        const data = await response.json();

        if (data) {
            // Store the movie data for later use
            currentMovieData = data;
            
            // Display detailed movie info in the pop-up
            document.getElementById('movieName').textContent = data.Title || 'No description available.';
            document.getElementById('movieGenere').textContent = data.Genre || 'No description available.';
            document.getElementById('movieRating').textContent = data.imdbRating || 'No description available.';
            document.getElementById('movieDescription').textContent = data.Plot || 'No description available.';
            document.getElementById('movieDirector').textContent = data.Director || 'No description available.';
            document.getElementById('movielanguage').textContent = data.Language || 'No description available.';
            document.getElementById('popupCard').style.display = 'flex';
        }
    } catch (error) {
        console.error("Error fetching movie details:", error);
    }
}

// Close the pop-up
function closePopup() {
    document.getElementById('popupCard').style.display = 'none';
}

// Function to add to watchlist and submit to PHP
function postToPhp() {
    if (!currentMovieData) return;
    
    document.getElementById('popupCard').style.display = 'none';

    const form = document.createElement("form");
    form.action = "../../config/addToWatchlist.php";
    form.method = "POST";

    const movieTitleInput = document.createElement("input");
    movieTitleInput.type = "hidden";
    movieTitleInput.name = "movie_name";
    movieTitleInput.value = currentMovieData.Title;

    const movieYearInput = document.createElement("input");
    movieYearInput.type = "hidden";
    movieYearInput.name = "movie_year";
    movieYearInput.value = currentMovieData.Year;

    const movieGenreInput = document.createElement("input");
    movieGenreInput.type = "hidden";
    movieGenreInput.name = "genre";
    movieGenreInput.value = currentMovieData.Genre;

    const movieRatingInput = document.createElement("input");
    movieRatingInput.type = "hidden";
    movieRatingInput.name = "imbd_rating";
    movieRatingInput.value = currentMovieData.imdbRating;

    const movieDirectorInput = document.createElement("input");
    movieDirectorInput.type = "hidden";
    movieDirectorInput.name = "director";
    movieDirectorInput.value = currentMovieData.Director;

    const movieIdInput = document.createElement("input");
    movieIdInput.type = "hidden";
    movieIdInput.name = "imdb_id";
    movieIdInput.value = currentMovieData.imdbID;

    console.log("id="+movieIdInput.value);

    const movieLanguageInput = document.createElement("input");
    movieLanguageInput.type = "hidden";
    movieLanguageInput.name = "language";
    movieLanguageInput.value = currentMovieData.Language;

    const moviePosterInput = document.createElement("input");
    moviePosterInput.type = "hidden";
    moviePosterInput.name = "poster";
    moviePosterInput.value = currentMovieData.Poster;

    const movieDescriptionInput = document.createElement("input");
    movieDescriptionInput.type = "hidden";
    movieDescriptionInput.name = "movie_description";
    movieDescriptionInput.value = currentMovieData.Plot;

    form.appendChild(movieTitleInput);
    form.appendChild(movieYearInput);
    form.appendChild(movieGenreInput);
    form.appendChild(movieRatingInput);
    form.appendChild(movieDirectorInput);
    form.appendChild(movieIdInput);
    form.appendChild(movieLanguageInput);
    form.appendChild(moviePosterInput);
    form.appendChild(movieDescriptionInput);

    document.body.appendChild(form); // Append the form to the document
    form.submit(); // Submit the form
}

document.getElementById('closePopupButton').addEventListener('click', closePopup);