<?php

ini_set('display_errors', '1');
session_start();

?>

<!DOCTYPE html>
<head>
	<title>Images | My Image Gallery</title>
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
	
	$image_sql = "SELECT * FROM Images ORDER BY date_modified DESC;";
	$image_result = $mysqli->query($image_sql);
	
	if (!$image_result) {
		print($mysqli->error);
		exit();
	}
	
?>

<body>
	<div id="container">
	<?php include "includes/nav.php"; ?>
		<div id="content">
			<div class="section">
				<h1>Images</h1>
				<div class="images">
					<?php
						if ($image_result) {
							while ($row = $image_result->fetch_assoc()) {
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