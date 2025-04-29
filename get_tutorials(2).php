<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tutorials</title>
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      background: #f9f9f9;
      margin: 0;
      padding: 10px;
    }

    .category-buttons {
      display: flex;
      flex-wrap: wrap;
      margin-bottom: 20px;
      gap: 10px;
    }

    .category-buttons button {
      padding: 8px 15px;
      border: none;
      border-radius: 20px;
      background-color: #e0e0e0;
      color: #333;
      font-size: 14px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .category-buttons button.active,
    .category-buttons button:hover {
      background-color: #ff0000;
      color: #fff;
    }

    .search-bar {
      margin-bottom: 20px;
    }

    .search-bar input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 16px;
    }

    .tutorial-card {
      background: white;
      border-radius: 12px;
      margin-bottom: 20px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      overflow: hidden;
    }

    .tutorial-card iframe {
      width: 100%;
      height: 200px;
      border: none;
    }

    .tutorial-content {
      padding: 15px;
    }

    .tutorial-title {
      font-size: 18px;
      font-weight: bold;
      margin-bottom: 5px;
    }

    .tutorial-meta {
      font-size: 12px;
      color: #777;
      margin-bottom: 10px;
    }

    .description {
      font-size: 14px;
      color: #444;
    }

    .hidden {
      display: none;
    }

    .toggle-button {
      color: #1e88e5;
      font-weight: bold;
      cursor: pointer;
      margin-left: 5px;
    }
  </style>
</head>
<body>

  <div class="search-bar">
    <input type="text" id="searchInput" placeholder="Search by Title..." onkeyup="filterTutorials()">
  </div>

  <div class="category-buttons" id="categoryButtons">
    <button class="active" onclick="filterByCategory('All')">All</button>
  </div>

  <div id="tutorials"></div>

  <script>
    let tutorialsData = [];

    function fetchTutorials() {
      fetch('https://stockandroid.onrender.com/get_tutorials.php') 
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            tutorialsData = data.data;
            createCategoryButtons();
            displayTutorials(tutorialsData);
          }
        });
    }

    function createCategoryButtons() {
      const categories = ['All', ...new Set(tutorialsData.map(t => t.category))];
      const categoryContainer = document.getElementById('categoryButtons');
      categoryContainer.innerHTML = '';

      categories.forEach(cat => {
        const btn = document.createElement('button');
        btn.textContent = cat;
        btn.onclick = () => filterByCategory(cat);
        if (cat === 'All') btn.classList.add('active');
        categoryContainer.appendChild(btn);
      });
    }

    function displayTutorials(tutorials) {
      const container = document.getElementById('tutorials');
      container.innerHTML = '';

      tutorials.forEach(tutorial => {
        const createdAt = new Date(tutorial.created_at);
        const timeAgoText = timeAgo(createdAt);

        const shortDescription = tutorial.description.length > 100
          ? tutorial.description.substring(0, 100) + '...'
          : tutorial.description;

        const card = `
          <div class="tutorial-card">
            <iframe src="https://www.youtube.com/embed/${getYouTubeID(tutorial.video_link)}" allowfullscreen></iframe>
            <div class="tutorial-content">
              <div class="tutorial-title">${tutorial.title}</div>
              <div class="tutorial-meta">${tutorial.category} • ${timeAgoText}</div>
              <div class="description">
                <span class="short-text">${shortDescription}</span>
                <span class="full-text hidden">${tutorial.description}</span>
                ${tutorial.description.length > 100 ? '<span class="toggle-button" onclick="toggleDescription(this)">Show More</span>' : ''}
              </div>
            </div>
          </div>
        `;
        container.innerHTML += card;
      });
    }

    function toggleDescription(btn) {
      const description = btn.parentElement;
      const shortText = description.querySelector('.short-text');
      const fullText = description.querySelector('.full-text');

      if (fullText.classList.contains('hidden')) {
        fullText.classList.remove('hidden');
        shortText.classList.add('hidden');
        btn.textContent = 'Show Less';
      } else {
        fullText.classList.add('hidden');
        shortText.classList.remove('hidden');
        btn.textContent = 'Show More';
      }
    }

    function filterTutorials() {
      const search = document.getElementById('searchInput').value.toLowerCase();
      const filtered = tutorialsData.filter(t => t.title.toLowerCase().includes(search));
      displayTutorials(filtered);
    }

    function filterByCategory(category) {
      document.querySelectorAll('.category-buttons button').forEach(btn => btn.classList.remove('active'));
      event.target.classList.add('active');

      if (category === 'All') {
        displayTutorials(tutorialsData);
      } else {
        const filtered = tutorialsData.filter(t => t.category === category);
        displayTutorials(filtered);
      }
    }

    function getYouTubeID(url) {
      const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
      const match = url.match(regExp);
      return (match && match[2].length === 11) ? match[2] : null;
    }

    function timeAgo(date) {
      const now = new Date();
      const seconds = Math.floor((now - date) / 1000);

      let interval = Math.floor(seconds / 31536000);
      if (interval >= 1) return interval + " year" + (interval > 1 ? "s" : "") + " ago";
      interval = Math.floor(seconds / 2592000);
      if (interval >= 1) return interval + " month" + (interval > 1 ? "s" : "") + " ago";
      interval = Math.floor(seconds / 86400);
      if (interval >= 1) return interval + " day" + (interval > 1 ? "s" : "") + " ago";
      interval = Math.floor(seconds / 3600);
      if (interval >= 1) return interval + " hour" + (interval > 1 ? "s" : "") + " ago";
      interval = Math.floor(seconds / 60);
      if (interval >= 1) return interval + " minute" + (interval > 1 ? "s" : "") + " ago";
      return "Just now";
    }

    fetchTutorials();
  </script>

</body>
</html>
