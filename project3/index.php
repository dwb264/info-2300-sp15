<?php

ini_set('display_errors', '1');	
session_start();

if (isset($_GET['logout'])) {
	$logout = $_GET['logout'];
	if ($logout==1) {
		unset($_SESSION['logged_user']);
		header('Location: index.php');
	}
}
	

?>

<!DOCTYPE html>
<head>
	<title>My Image Gallery</title>
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
	
	// 3 most recent albums only
	$album_sql = "SELECT * FROM Albums ORDER BY date_created DESC LIMIT 3;";
	$album_result = $mysqli->query($album_sql);
	
	// 6 most recent images only
	$image_sql = "SELECT * FROM Images ORDER BY date_modified DESC LIMIT 8;";
	$image_result = $mysqli->query($image_sql);
	
	if (!$album_result) {
		print($mysqli->error);
		exit();
	} 
	
	if (!$image_result) {
		print($mysqli->error);
		exit();
	}
	
?>
		
<body>
	<div id="container">
	<?php include "includes/nav.php"; ?>
		<div id="content">
			<div id="welcome">
				<h1>Welcome!</h1>
				<p>Clouds are free they come and go as they please. In your world you can create anything you desire. Automatically, all of these beautiful, beautiful things will happen. If we're gonna walk though the woods, we need a little path. These things happen automatically. All you have to do is just let them happen. Think about a cloud. Just float around and be there.</p>
			</div>
			<div class="section">
				<h1>Latest Albums</h1>
				<div class="images">
				<?php
					while ($row = $album_result->fetch_assoc()) {
						$album_id = $row['album_id'];
						$background = $row['cover_file'];
						$title = $row['title'];
						$description = $row['description'];
						$date = $row['date_created'];
					
						print("<a href='view-album.php?albumid=$album_id'>");
						// Inline CSS because dynamically generated; styling does not work otherwise
						print("<div class='album' style='background: url(\"images/$background\"); 
							background-size: 210px; background-position: center;'>");
						print("<h2>$title</h2>");
						print("</div></a>");
					}
				?>
				</div>
				<div class="ghost-link"><a href="albums.php">View All Albums</a></div>
			</div>
			
			<div class="section latest-images">
				<h1>Latest Images</h1>
				<div class="images">
				<?php
					while ($row = $image_result->fetch_assoc()) {
						$filename = $row['filename'];
						$image_id = $row['image_id'];
						$image_title = $row['title'];
						print("<a href='view-image.php?imageid=$image_id'>");
						print("<div class='image img-thumbnail' style='background: url(\"images/$filename\");
							background-size: 210px; background-position: center;' title='$image_title | Click to view full-size'>");
						print("</div></a>");
					}
				?>
				</div>
				<div class="ghost-link"><a href="images.php">View All Images</a></div>
			</div>
			<?php include "includes/bottomnav.php"; ?>
		</div>
	</div>
</body>
</html>