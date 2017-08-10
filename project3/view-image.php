<?php

ini_set('display_errors', '1');
session_start();

?>

<!DOCTYPE html>
<head>
	<title>Viewing Image | My Image Gallery</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:700,300' rel='stylesheet' type='text/css'>
	<meta charset="UTF-8">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script type="text/javascript" src="scripts/nav.js"></script>
	<script type="text/javascript" src="scripts/img_properties.js"></script>
</head>

<?php
	require_once "config.php";
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	
	if ($mysqli->errno) {
		print $mysqli->error;
		exit();
	}
	
	$image_title = "None";
	$image_info = "";
	
	// Input clean-up function
	function sanitize_input($input) {
		$input = trim($input);
		$input = strip_tags($input);
		$input = htmlentities($input, ENT_QUOTES);
		return $input;
	}
	
	if (isset($_GET['imageid'])) {
		$image_id = sanitize_input($_GET['imageid']);
		$image_info = $mysqli->query("SELECT * FROM Images 
			WHERE image_id = '$image_id'");
		
		// Get Image Info
		$image_info = $image_info->fetch_assoc();
		$image_title = $image_info['title'];
		$caption = $image_info['caption'];
		$filename = $image_info['filename'];
		$date_taken = $image_info['date_taken'];
		$date_modified = $image_info['date_modified'];
		$credits = $image_info['credits'];
	}
	
?>

<body>
	<div id="container">
		<?php include "includes/nav.php"; ?>
		<div id="content">
			<div class="section">
				<div class="backbutton"><button onclick="window.history.back()">&larr; Back</button></div><br>
				<?php
					if (!empty($image_info)) {
						print("<h2>$image_title</h2>");
						print("<p><i>$caption</i></p>");
						print("<img src='images/$filename' alt='$image_title' class='fullsize'>");
						print("<p>Taken: $date_taken | Updated: $date_modified<br>");
						print("Credits: $credits</p>");
					} else {
						print("<h3>That image does not exist. <a href='index.php'><u>Return to homepage</u></a></h3>");
					}
				?>
				</div>
			</div>
			<?php include "includes/bottomnav.php"; ?>
		</div>
	</div>
</body>
</html>