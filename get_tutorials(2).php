<?php
header("Access-Control-Allow-Origin: *");
include 'db.php';
date_default_timezone_set('Asia/Kolkata'); // Set timezone if needed

function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $difference = time() - $timestamp;

    if ($difference < 60)
        return 'Just now';
    elseif ($difference < 3600)
        return floor($difference / 60) . ' minutes ago';
    elseif ($difference < 86400)
        return floor($difference / 3600) . ' hours ago';
    elseif ($difference < 604800)
        return floor($difference / 86400) . ' days ago';
    elseif ($difference < 2592000)
        return floor($difference / 604800) . ' weeks ago';
    elseif ($difference < 31536000)
        return floor($difference / 2592000) . ' months ago';
    else
        return floor($difference / 31536000) . ' years ago';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tutorials</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 10px;
            background: #f9f9f9;
        }
        h1 {
            text-align: center;
            color: #cc0000;
            margin-bottom: 10px;
        }
        .search-filter {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }
        input[type="text"], select {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            width: 100%;
            box-sizing: border-box;
        }
        .tutorial {
            background: #fff;
            margin-bottom: 15px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0px 2px 8px rgba(0,0,0,0.1);
        }
        .tutorial iframe {
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
            margin: 5px 0;
            color: #333;
        }
        .category-badge {
            display: inline-block;
            background-color: #cc0000;
            color: #fff;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 12px;
            margin-bottom: 10px;
        }
        .tutorial-description {
            font-size: 14px;
            color: #555;
            margin-top: 8px;
            line-height: 1.4;
        }
        .show-more {
            color: #cc0000;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
            margin-top: 5px;
            display: inline-block;
        }
        .meta {
            font-size: 12px;
            color: #999;
            margin-top: 8px;
        }
        .no-data {
            text-align: center;
            color: #cc0000;
            margin-top: 30px;
            font-size: 18px;
        }
    </style>

    <script>
    function searchAndFilterTutorials() {
        var searchInput = document.getElementById("searchInput").value.toLowerCase();
        var categoryFilter = document.getElementById("categoryFilter").value.toLowerCase();
        var tutorials = document.getElementsByClassName("tutorial");

        var anyVisible = false;
        for (var i = 0; i < tutorials.length; i++) {
            var title = tutorials[i].getElementsByClassName("tutorial-title")[0].innerText.toLowerCase();
            var category = tutorials[i].getElementsByClassName("category-badge")[0].innerText.toLowerCase();

            var matchesSearch = title.includes(searchInput);
            var matchesCategory = (categoryFilter === "all" || category === categoryFilter);

            if (matchesSearch && matchesCategory) {
                tutorials[i].style.display = "";
                anyVisible = true;
            } else {
                tutorials[i].style.display = "none";
            }
        }

        document.getElementById("noDataMessage").style.display = anyVisible ? "none" : "block";
    }

    function toggleDescription(id) {
        var desc = document.getElementById('desc-' + id);
        var btn = document.getElementById('btn-' + id);

        if (desc.classList.contains('expanded')) {
            desc.classList.remove('expanded');
            desc.innerText = desc.getAttribute('data-short');
            btn.innerText = 'Show More';
        } else {
            desc.classList.add('expanded');
            desc.innerText = desc.getAttribute('data-full');
            btn.innerText = 'Show Less';
        }
    }
    </script>
</head>
<body>

<h1>Tutorials</h1>

<div class="search-filter">
    <input type="text" id="searchInput" onkeyup="searchAndFilterTutorials()" placeholder="Search tutorials...">
    <select id="categoryFilter" onchange="searchAndFilterTutorials()">
        <option value="all">All Categories</option>
        <?php
        $categoryQuery = "SELECT DISTINCT category FROM tutorials";
        $categoryResult = $conn->query($categoryQuery);
        if ($categoryResult->num_rows > 0) {
            while ($cat = $categoryResult->fetch_assoc()) {
                echo '<option value="' . htmlspecialchars($cat['category']) . '">' . htmlspecialchars($cat['category']) . '</option>';
            }
        }
        ?>
    </select>
</div>

<div id="tutorialsList">
    <?php
    function getYoutubeEmbedUrl($url) {
        preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|v\/|shorts\/))([^\&\?\/]+)/', $url, $matches);
        return isset($matches[1]) ? 'https://www.youtube.com/embed/' . $matches[1] : '';
    }

    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        $query = "SELECT * FROM tutorials ORDER BY id DESC";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $id = 0;
            while ($row = $result->fetch_assoc()) {
                $embedUrl = getYoutubeEmbedUrl($row['video_link']);
                $description = htmlspecialchars($row['description']);
                $shortDescription = strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description;

                echo '<div class="tutorial">';
                if (!empty($embedUrl)) {
                    echo '<iframe src="' . htmlspecialchars($embedUrl) . '" allowfullscreen></iframe>';
                }
                echo '<div class="tutorial-content">';
                echo '<div class="category-badge">' . htmlspecialchars($row['category']) . '</div>';
                echo '<div class="tutorial-title">' . htmlspecialchars($row['title']) . '</div>';
                echo '<div class="tutorial-description" id="desc-' . $id . '" data-short="' . $shortDescription . '" data-full="' . $description . '">' . $shortDescription . '</div>';
                if (strlen($description) > 100) {
                    echo '<div class="show-more" id="btn-' . $id . '" onclick="toggleDescription(' . $id . ')">Show More</div>';
                }
                echo '<div class="meta">Posted ' . timeAgo($row['created_at']) . '</div>';
                echo '</div></div>';

                $id++;
            }
        } else {
            echo '<div class="no-data">No tutorials found</div>';
        }
        $conn->close();
    }
    ?>
</div>

<div id="noDataMessage" class="no-data" style="display:none;">No matching tutorials found</div>

</body>
</html>
