<?php

ini_set('display_errors', '1');
session_start();

// Start database connection
require_once "config.php";
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($mysqli->errno) {
	print $mysqli->error;
	exit();
}

$keyword = $album_result = $image_result = "";

// Input clean-up function
function sanitize_input($input) {
	$input = trim($input);
	$input = strip_tags($input);
	$input = htmlentities($input, ENT_QUOTES);
	return $input;
}

if (isset($_GET['search'])) {
	$keyword = sanitize_input($_GET['search']);
	
	// Search album title and description
	$album_query = "SELECT * FROM Albums 
		WHERE title LIKE '%$keyword%'
		OR description LIKE '%$keyword%';";
	$album_result = $mysqli->query($album_query);
	
	// Search image title, caption, and credits
	$image_query = "SELECT * FROM Images
		WHERE title LIKE '%$keyword%'
		OR caption LIKE '%$keyword%'
		OR credits LIKE '%$keyword%';";
	$image_result = $mysqli->query($image_query);
	
	$keyword = "<b>\"$keyword\"</b>";
}

if (isset($_GET['adv-search'])) {
	$albumtitle = sanitize_input($_GET['albumtitle']);
	$albumdesc = sanitize_input($_GET['albumdesc']);
	$imagetitle = sanitize_input($_GET['imagetitle']);
	$imagecaption = sanitize_input($_GET['imagecaption']);
	$imagecredits = sanitize_input($_GET['imagecredits']);
	
	// Make album where clause
	$album_where = array();
	if (!empty($albumtitle)) {
		$album_where[] = "title LIKE '%$albumtitle%'";
	}
	if (!empty($albumdesc)) {
		$album_where[] = "description LIKE '%$albumdesc%'";
	}
	$album_where = implode(" OR ", $album_where);
	$album_query = "SELECT * FROM Albums WHERE " . $album_where;
	$album_result = $mysqli->query($album_query);
	
	// Make image where clause
	$image_where = array();
	if (!empty($imagetitle)) {
		$image_where[] = "title LIKE '%$imagetitle%'";
	}
	if (!empty($imagecaption)) {
		$image_where[] = "caption LIKE '%$imagecaption%'";
	}
	if (!empty($imagecredits)) {
		$image_where[] = "credits LIKE '%$imagecredits%'";
	}
	$image_where = implode(" OR ", $image_where);
	$image_query = "SELECT * FROM Images WHERE " . $image_where;
	$image_result = $mysqli->query($image_query);
	
	$keyword = "your search.";
}

?>

<!DOCTYPE html>
<head>
	<title>Search Results | My Image Gallery</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:700,300' rel='stylesheet' type='text/css'>
	<meta charset="UTF-8">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script type="text/javascript" src="scripts/nav.js"></script>
</head>

<body>
	<div id="container">
	<?php include "includes/nav.php"; ?>
		<div id="content">
			<div class="section short">
				<h1>Search Results</h1>
				<p>Here's what turned up for <?php echo $keyword ?></p>
			</div>
			<div class="section">
				<div id="album-results">
				<h2>Album Results</h2>
					<?php 
				
					if (empty($album_result) || $album_result->num_rows == 0) {
						print("No albums matched the search.");
					} else {
						while ($row = $album_result->fetch_assoc()) {
							$album_id = $row['album_id'];
							$title = $row['title'];
							$description = $row['description'];
							$cover = $row['cover_file'];
					
							print("<div class='search-result'><a href='view-album.php?albumid=$album_id'>");
							print("<img src='images/$cover' alt='$title'>");
							print("<h3>$title</h3>");
							print("<p>$description</p>");
							print("</a></div>");
						}
					}
				
					?>
				</div>
			
				<div id="image-results">
				<h2>Image Results</h2>
					<?php 
				
					if (empty($image_result) || $image_result->num_rows == 0) {
						print("No images matched the search.");
					} else {
						while ($row = $image_result->fetch_assoc()) {
							$image_id = $row['image_id'];
							$title = $row['title'];
							$caption = $row['caption'];
							$filename = $row['filename'];
					
							print("<div class='search-result'><a href='view-image.php?imageid=$image_id'>");
							print("<img src='images/$filename' alt='$title'>");
							print("<h3>$title</h3>");
							print("<p>$caption</p>");
							print("</a></div>");
						}
					}
				
					?>
				</div>
			</div>
		</div>
		<?php include "includes/bottomnav.php"; ?>
	</div>
</body>
</html>