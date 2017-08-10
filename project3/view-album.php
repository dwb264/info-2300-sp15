<?php

ini_set('display_errors', '1');
session_start();

?>

<!DOCTYPE html>
<head>
	<title>Viewing Album | My Image Gallery</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:700,300' rel='stylesheet' type='text/css'>
	<meta charset="UTF-8">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script type="text/javascript" src="scripts/nav.js"></script>
</head>

<?php
	require_once "config.php";
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	
	if ($mysqli->errno) {
		print $mysqli->error;
		exit();
	}
	
	$album_title = "None";
	$result = "";
	
	// Input clean-up function
	function sanitize_input($input) {
		$input = trim($input);
		$input = strip_tags($input);
		$input = htmlentities($input, ENT_QUOTES);
		return $input;
	}
	
	if (isset($_GET['albumid'])) {
		$albumid = sanitize_input($_GET['albumid']);
		$result = $mysqli->query("SELECT * FROM Images 
			INNER JOIN ImagesinAlbums 
				ON Images.image_id = ImagesinAlbums.image_id
			WHERE ImagesinAlbums.album_id = $albumid
			ORDER BY date_modified DESC;");
		
		// Get Album Info
		$album_info = $mysqli->query("SELECT * FROM Albums WHERE album_id = $albumid;");
		if ($album_info) {
			$album_info = $album_info->fetch_assoc();
			$album_title = $album_info['title'];
			$album_desc = $album_info['description'];
			$cover = $album_info['cover_file'];
			$date_created = substr($album_info['date_created'], 0, 10);
			if ($album_info['date_modified']) {
				$date_modified = substr($album_info['date_modified'], 0, 10);
			} else {
				$date_modified = "Never";
			}
		}
	}
	
?>

<body>
	<div id="container">
		<?php include "includes/nav.php"; ?>
		<div id="content">
			<div class="section">
			
				<?php
					if ($album_info) {
						print("<img class='viewalbumcover' src='images/$cover' alt='$title'>");
						print("<h1>$album_title</h1>");
						print("<p>$album_desc</p>");
						print("<p>Created: $date_created | Modified: $date_modified</p>");
					} else {
						print("<h1>Sorry, this album does not exist.</h1>");
					}
				?>
				
				<div class="images">
				<?php
					if ($result && $result->num_rows > 0) {
						while ($row = $result->fetch_assoc()) {
							$filename = $row['filename'];
							$image_id = $row['image_id'];
							$image_title = $row['title'];
							print("<a href='view-image.php?imageid=$image_id'>");
							print("<div class='image img-thumbnail' style='background: url(\"images/$filename\");
								background-size: 210px; background-position: center;' title='$image_title | Click to view full-size'>");
							print("</div></a>");
						}
					} else {
						print("<p>No pictures to display.</p>");
					}
				?>
				</div>
			</div>
		</div>
		<?php include "includes/bottomnav.php"; ?>
	</div>
</body>
</html>