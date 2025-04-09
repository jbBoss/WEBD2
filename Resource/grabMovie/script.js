const apiKey = 'a2c500fb'; 

document.getElementById('search').addEventListener('input', async (e) => {
  const query = e.target.value;
  const resultsDiv = document.getElementById('search-results');
  resultsDiv.innerHTML = '';

  if (query.length < 3) return;

  try {
    const res = await fetch(`https://www.omdbapi.com/?apikey=${apiKey}&s=${encodeURIComponent(query)}`);
    const data = await res.json();

    if (data.Response === "True") {
      data.Search.forEach(movie => {
        const item = document.createElement('div');
        item.classList.add('search-result');
        item.dataset.imdbid = movie.imdbID;

        const poster = document.createElement('img');
        poster.src = movie.Poster !== "N/A" ? movie.Poster : 'placeholder.jpg'; // 
        poster.alt = `${movie.Title} Poster`;
        poster.style.width = '50px';
        poster.style.height = '75px';
        poster.style.objectFit = 'cover';
        poster.style.marginRight = '10px';

        const text = document.createElement('span');
        text.textContent = `${movie.Title} (${movie.Year})`;

        item.appendChild(poster);
        item.appendChild(text);

        resultsDiv.appendChild(item);
      });
    } else {
      resultsDiv.innerHTML = `<div class="search-result">No results found</div>`;
    }
  } catch (err) {
    console.error(err);
    resultsDiv.innerHTML = `<div class="search-result">Error fetching data</div>`;
  }
});

document.getElementById('search-results').addEventListener('click', async (e) => {
  const target = e.target.closest('.search-result');
  if (!target) return;
  
  const imdbID = target.dataset.imdbid;
  console.log(imdbID);
  const res = await fetch(`https://www.omdbapi.com/?apikey=${apiKey}&i=${imdbID}`);
  const movie = await res.json();

  document.getElementById('title').value = movie.Title || '';
  document.getElementById('year').value = movie.Year || '';
  document.getElementById('director').value = movie.Director || '';
  document.getElementById('genre').value = movie.Genre || '';
  document.getElementById('plot').value = movie.Plot || '';
  document.getElementById('language').value = movie.Language || '';
  document.getElementById('rating').value = movie.imdbRating || '';
  document.getElementById('imdb_id').value = imdbID || 'nop';
  

  document.getElementById('search-results').innerHTML = '';
  document.getElementById('search').value = `${movie.Title} (${movie.Year})`;

  // Optional: update poster preview
  const posterEl = document.getElementById('movie_poster');
  posterEl.src = movie.Poster !== "N/A" ? movie.Poster : 'placeholder.jpg';
});
