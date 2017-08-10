<?php

ini_set('display_errors', '1');
session_start();

?>

<!DOCTYPE html>
<head>
	<title>Albums | My Image Gallery</title>
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
	
	$sql = "SELECT * FROM Albums ORDER BY date_created DESC;";
	$result = $mysqli->query($sql);
	
	if (!$result) {
		print($mysqli->error);
		exit();
	}
	
?>

<body>
	<div id="container">
	<?php include "includes/nav.php"; ?>
		<div id="content">
			<div class="section">
				<h1>Albums</h1>
				<div class="images">
					<?php
						while ($row = $result->fetch_assoc()) {
							$album_id = $row['album_id'];
							$background = $row['cover_file'];
							$title = $row['title'];
							$description = $row['description'];
							$date = $row['date_created'];
					
							print("<a href='view-album.php?albumid=$album_id'>");
							// Inline CSS because dynamically generated, styling does not work otherwise
							print("<div class='album' style='background: url(\"images/$background\"); 
								background-size: 210px; background-position: center;'>");
							print("<h2>$title</h2>");
							print("</div></a>");
						}
					?>
				</div>
			</div>
		</div>
		<?php include "includes/bottomnav.php"; ?>
	</div>
</body>
</html>