<?php

ini_set('display_errors', 1);

include 'includes/txt2array.php';
include 'includes/valid.php';
include 'includes/searching.php';

// Set all echoed variables to blank
$errors = $url = $title = $description = $category = $tags = "";

// Form validation
if (isset($_POST['submit'])) {
	$url = cleanup_input($_POST['url']);
	$title = cleanup_input($_POST['title']);
	$description = cleanup_input($_POST['description']);
	$category = cleanup_input($_POST['category']);
	$tags = cleanup_input($_POST['tags']);
	
	// Check URL
	if (empty($url)) {
		$errors .= "Enter a link<br>";
	} elseif (!(validate_url($url))) {
		$errors .= "Make sure your link is valid.<br>";
	} else {
		$array = restore_array("data.txt");
		if (!empty($array)) {
			$duplicates = searchfilter_inclusive(format_url($url), "url", $array);
			if (!empty($duplicates)) {
				$errors .= "Link is a duplicate. Try something else.<br>";
			}
		}
	}
	
	// Check title, description
	$errors .= validate_input($title, "title", 3, 100);
	$errors .= validate_input($description, "description", 10, 200);
	$errors .= validate_input($category, "category", 3, 140);
	
	if (substr_count($tags, ",") > 4) {
		$errors .= "Cannot have more than 5 tags.";
	}
		
	if (empty($errors)) {
		$url = format_url($url);
		
		// Remove newlines from description
		// Regex from http://kaspars.net/blog/web-development/regex-remove-all-line-breaks-from-text
		$description = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $description);
		
		// If they didn't supply tags, tag it as "misc"
		if (empty($tags)) {
			$tags = "misc";
		}
		
		
		// Get today's date in format 20YY-MM-DD Hour:Min am/pm
		date_default_timezone_set("America/New_York");
		$date = date("Y-m-d h:i a");
		
		// Ratings: set the rating to 0 and num. of votes to 0
		$rating = 0;
		$votes = 0;
		
		// ID: make a unique 13-char id for the item
		$id = uniqid();
		
		// Open the file
		$file = fopen('data.txt', 'a');
		
		// Format the new entry data
		$new_entry = array(
			$id,
			$url,
			$title,
			$description,
			$category,
			$tags,
			$rating,
			$votes,
			$date,
		);
		
		$new_entry = implode("\t", $new_entry) . "\n";
		fputs($file, $new_entry);
		fclose($file);
		
		$url = $title = $description = $category = $tags = "";
		$errors = "<b>Thanks for your submission.<br><a href='index.php'>Back to homepage</a></b>";
		
	} else {
		$errors = "<b>Please fix errors:</b><br>" . $errors;
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="css/style.css">
	<link href='http://fonts.googleapis.com/css?family=Patua+One' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,600' rel='stylesheet' type='text/css'>
	<link rel="shortcut icon" href="assets/favicon.ico" type="image/x-icon" />
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="scripts/help.js"></script>
	<title>Add Entry | Free Design Resources</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="content container">
	<!--Add-Entry Form-->
	<form id="addentry" name="addentry" action="addentry.php" method="post">
	
	<span class="formfield" id="url-field">
		<label for="url">Link *</label>
		<input type="text" name="url" id="url" value="<?php echo $url; ?>" placeholder="http://color.adobe.com">
	</span>
	
	<span class="formfield" id="title-field">
		<label for="title">Title *</label>
		<input type="text" name="title" id="title" value="<?php echo $title; ?>" placeholder="Adobe Kuler">
	</span>
	
	<span class="formfield" id="cat-field">
		<label for="category">Category *</label>
		<select name="category" id="category">
			<option value="">(Select One)</option>
			<option value="Fonts">Fonts</option>
			<option value="Icons">Icons</option>
			<option value="Images">Images</option>
			<option value="Tutorials">Tutorials</option>
			<option value="Tools">Tools</option>
			<option value="Other">Other</option>
		</select>
	</span>
	
	<span class="formfield" id="desc-field">
		<label for="description">Description *</label>
		<textarea name="description" id="description" placeholder="Lorem ipsum dolor sit amet..."><?php echo $description; ?></textarea>
	</span>
	
	<span class="formfield" id="tag-field">
		<label for="tags">Tags</label>
		<input type="text" name="tags" id="tags" value="<?php echo $tags; ?>" placeholder="separate, with, commas">
	</span>
	
	<button type="submit" id="submit" name="submit" value="Submit">Submit</button>
	
	</form>
	
	<!--Explanations-->
	<div id="help">
		<h1 id="helptitle">Add a link.</h1>
		<p id="helpdesc">Fields marked with * are required.</p>
		<span class="errors"><?php echo $errors; ?></span>
	</div>
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>