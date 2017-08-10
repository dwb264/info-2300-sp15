<?php session_start(); ?>

<!DOCTYPE html>
<head>
	<title>Advanced Search | My Image Gallery</title>
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
			<div class="section">
				<h1>Advanced Search</h1>
				<form action='search-results.php' method='GET' class='search-form'>
			
				<div class='field'>
					<label for="albumtitle">Album Title Contains</label>
					<input type="text" name="albumtitle" id="albumtitle">
				</div>
			
				<div class='field'>
					<label for="albumdesc">Album Description Contains</label>
					<input type="text" name="albumdesc" id="albumdesc">
				</div>
			
				<div class='field'>
					<label for="imagetitle">Image Title Contains</label>
					<input type="text" name="imagetitle" id="imagetitle">
				</div>
			
				<div class='field'>
					<label for="imagecaption">Image Caption Contains</label>
					<input type="text" name="imagecaption" id="imagecaption">
				</div>
			
				<div class='field'>
					<label for="imagecredits">Image Credits Contain</label>
					<input type="text" name="imagecredits" id="imagecredits" >
				</div>
			
				<button type="submit" name="adv-search">Search</button>
				
				</form>
			</div>
		</div>
		<?php include "includes/bottomnav.php"; ?>
	</div>
</body>
</html>