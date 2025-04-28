<?php
header("Access-Control-Allow-Origin: *");
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tutorials List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1100px;
            margin: auto;
            background: #fff;
            padding: 20px 30px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .search-bar {
            text-align: center;
            margin-bottom: 30px;
        }
        .search-bar input {
            width: 80%;
            max-width: 500px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .tutorial {
            border-bottom: 1px solid #ccc;
            padding: 20px 0;
        }
        .tutorial:last-child {
            border-bottom: none;
        }
        .tutorial h2 {
            margin: 0;
            color: #0056b3;
        }
        .tutorial p {
            margin: 5px 0;
            color: #555;
        }
        .tutorial iframe {
            margin-top: 10px;
            width: 100%;
            height: 400px;
            border: none;
            border-radius: 8px;
        }
        .no-data {
            text-align: center;
            color: red;
            margin-top: 30px;
            font-size: 18px;
        }
        .meta {
            font-size: 14px;
            color: #888;
            margin-top: 5px;
        }
    </style>

    <script>
    function searchTutorials() {
        var input = document.getElementById("searchInput");
        var filter = input.value.toLowerCase();
        var tutorials = document.getElementsByClassName("tutorial");

        for (var i = 0; i < tutorials.length; i++) {
            var title = tutorials[i].getElementsByTagName("h2")[0];
            if (title) {
                var textValue = title.textContent || title.innerText;
                if (textValue.toLowerCase().indexOf(filter) > -1) {
                    tutorials[i].style.display = "";
                } else {
                    tutorials[i].style.display = "none";
                }
            }
        }
    }
    </script>
</head>
<body>

<div class="container">
    <h1>All Tutorials</h1>

    <div class="search-bar">
        <input type="text" id="searchInput" onkeyup="searchTutorials()" placeholder="Search by title...">
    </div>

    <?php
    function getYoutubeEmbedUrl($url) {
        preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|v\/|shorts\/))([^\&\?\/]+)/', $url, $matches);
        return isset($matches[1]) ? 'https://www.youtube.com/embed/' . $matches[1] : '';
    }

    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        $query = "SELECT * FROM tutorials ORDER BY id DESC";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $embedUrl = getYoutubeEmbedUrl($row['video_link']);
                echo '<div class="tutorial">';
                echo '<h2>' . htmlspecialchars($row['title']) . '</h2>';
                echo '<p><strong>Category:</strong> ' . htmlspecialchars($row['category']) . '</p>';
                if (!empty($embedUrl)) {
                    echo '<iframe src="' . htmlspecialchars($embedUrl) . '" allowfullscreen></iframe>';
                } else {
                    echo '<p><strong>Video:</strong> <a href="' . htmlspecialchars($row['video_link']) . '" target="_blank">Watch Here</a></p>';
                }
                echo '<p><strong>Description:</strong><br>' . nl2br(htmlspecialchars($row['description'])) . '</p>';
                echo '<p class="meta">Created at: ' . htmlspecialchars($row['created_at']) . '</p>';
                echo '</div>';
            }
        } else {
            echo '<div class="no-data">No tutorials found</div>';
        }
        $conn->close();
    }
    ?>

</div>

</body>
</html>
