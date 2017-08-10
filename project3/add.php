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

// Initialize error message variables
$album_msg = $album_title = $album_desc = $album_title_err = $album_desc_err = $cover_err = "";
$image_msg = $image_credits = $image_desc = $image_title = $image_title_err = $image_desc_err = $date_err = $file_err = $credits_err = "";
$date_taken = $autodate = "";

// Image defaults
$image_types = array("image/gif", "image/jpeg", "image/png");
$default_cover = "cover-01.png";

// Input clean-up function
function sanitize_input($input) {
	$input = trim($input);
	$input = strip_tags($input);
	$input = htmlentities($input, ENT_QUOTES);
	return $input;
}

// ALBUM SUBMISSION
if (isset($_POST['submit-album'])) {
	$album_title = sanitize_input($_POST['album-title']);
	$album_desc = sanitize_input($_POST['album-desc']);
	$cover = $_FILES['album-cover'];
	
	// Validate form input
	if (!$album_title) {
		$album_title_err = "Please enter album title.";
	} elseif (strlen($album_title) > 50) {
		$album_title_err = "Album title cannot be longer than 50 characters.";
	}
	
	if (!$album_desc) {
		$album_desc_err = "Please enter album description.";
	} elseif (strlen($album_desc) > 255) {
		$album_desc_err = "Album description cannot be longer than 255 characters.";
	}
	
	if ($cover['size'] != 0) {
		if (!(in_array($cover['type'], $image_types))) {
			// Cover file type is invalid
			$cover_err .= "Cover file must be jpg, gif, or png. ";
		}
		
		if ($cover['size'] > 2097152) {
			// File size is too big
			$cover_err .= "Cover file cannot be larger than 2MB. ";
		}
		
		if (!($cover_err)) {
			// No problems with the cover image; make new unique filename
			$temp_name = $cover['tmp_name'];
			$old_name = $cover['name'];
			
			$old_name = explode(".", $old_name);
			$extension = end($old_name);
			
			$cover_filename = "cover_" . time() . ".$extension";
		}
	} else {
		// No cover chosen: use default
		$cover_filename = $default_cover;
	}
	
	// No form errors; attempt to create album
	if (!$album_title_err && !$album_desc_err && !$cover_err) {
		if ($cover_filename != $default_cover) {
			$upload_cover = move_uploaded_file($temp_name, "images/$cover_filename");
		} 
		$add_album = $mysqli->query(
			"INSERT INTO Albums (title, description, cover_file) 
			VALUES ('$album_title', '$album_desc', '$cover_filename')");		
		if ($add_album) {
			// Success
			$album_msg = "<p>Album '$album_title' was successfully created.</p>";
			// Clear form fields
			$album_title = $album_desc = "";
		} else {
			// Failure
			$album_msg = "<p>Album '$album_title' could not be created: database query failed.</p>";
			// Get rid of the cover file that was just uploaded
			if ($cover_filename != $default_cover) {
				unlink("images/$cover_filename");
			} 
		}
	} else {
		$album_msg = "<p>Please fix errors</p>";
	}
}

// IMAGE SUBMISSION
if (isset($_POST['submit-image'])) {
	$image_title = sanitize_input($_POST['image-title']);
	$image_desc = sanitize_input($_POST['image-desc']);
	$date_taken = sanitize_input($_POST['date-taken']);
	$image_credits = sanitize_input($_POST['image-credits']);
	if (isset($_POST['albums'])) {
		$albums = array_filter($_POST['albums'], "sanitize_input");
	}
	$file = $_FILES['image'];
	
	// Validate form
	if (!$image_title) {
		$image_title_err = "Please enter image title.";
	} elseif (strlen($image_title) > 50) {
		$image_title_err = "Image title cannot be longer than 50 characters.";
	}
	
	if (!$image_desc) {
		$image_desc_err = "Please enter image description.";
	} elseif (strlen($image_desc) > 255) {
		$image_desc_err = "Image description cannot be longer than 255 characters.";
	}

	if (!$image_credits) {
		$credits_err = "Please enter credits for the image.";
	} elseif (strlen($image_credits) > 255) {
		$credits_err = "Image credits cannot be longer than 255 characters.";
	}
	
	// Image validation
	if ($file['size']==0) {
		$file_err = "Please choose an image file to upload.";
	} elseif (!(in_array($file['type'], $image_types))) {
		$file_err .= "Image must be jpg, gif, or png.";
	}
	if ($file['size'] > 2097152) {
		$file_err .= "File cannot be larger than 2MB. ";
	}
	
	if (isset($_POST['autodate'])) {
		// Auto-date was selected
		if (!$file_err && $file['type']=="image/jpeg") {
			// No errors and file is correct type; attempt to get date
			$exif_data = exif_read_data($file['tmp_name']);
			if (!empty($exif_data['DateTimeOriginal'])) {
				// Photo has DateTime; convert it to usable date format
				$date_taken = $exif_data['DateTimeOriginal'];
				$date_taken = substr(str_replace(":", "-", $date_taken), 0, 10);
			} else {
				// The file was ok but there was no exif DateTime
				$date_err = "Could not get date from file. Please enter date manually.";
			}
		} else {
			// There was a problem with the file
			$date_err = "Could not get date from file. Please enter date manually.";
		}
	} elseif (!$date_taken) {
		$date_err = "Please enter date image was taken.";
	} elseif (!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $date_taken)) {
		$date_err = "Please use format YYYY-MM-DD for the date.";
	} 
	
	// No form errors; attempt to add image
	if (!$image_title_err && !$image_desc_err && !$date_err && !$credits_err && !$file_err) {
		// Rename and move image
		$temp_name = $file['tmp_name'];
		$old_name = $file['name'];
		
		$old_name = explode(".", $old_name);
		$extension = end($old_name);
		
		$filename = "image_" . time() . ".$extension";
		
		move_uploaded_file($temp_name, "images/$filename");
		
		// Add image to database
		$add_image = $mysqli->query(
			"INSERT INTO Images (title, caption, date_taken, credits, filename) 
			VALUES ('$image_title', '$image_desc', '$date_taken', '$image_credits', '$filename');"
		);
			
		if ($add_image) {

			// Success
			$image_msg = "<p>Image '$image_title' was successfully uploaded.</p>";
			
			// Add to album(s) if image upload worked
			if (isset($_POST['albums'])) {
				// Get the image ID of the just-uploaded image
				$image_id = $mysqli->query("SELECT image_id FROM Images WHERE title = '$image_title';");
				$image_id = $image_id->fetch_row();
				$image_id = $image_id[0];
				
				// Insert row(s) into ImagesinAlbums
				foreach ($albums as $album) {
					$image_in_album = $mysqli->query(
						"INSERT INTO ImagesinAlbums(album_id, image_id)
						VALUES ('$album', '$image_id');"
					);
				}
			}
			
			// Clear form fields
			$image_credits = $image_desc = $image_title = $date_taken = "";
		} else {
			// Failure
			$image_msg = "<p>Image '$image_title' could not be uploaded: database query failed</p>";
			// Get rid of the image file that was just uploaded
			unlink("images/$filename");
		}
	} else {
		$image_msg = "<p>Please fix errors</p>";
	}
}

?>

<!DOCTYPE html>
<head>
	<title>Add Content | My Image Gallery</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:700,300' rel='stylesheet' type='text/css'>
	<meta charset="UTF-8">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script type="text/javascript" src="scripts/nav.js"></script>
	<script type="text/javascript" src="scripts/smoothscroll.js"></script>
</head>

<body>
	<div id="container">
		<?php include "includes/nav.php"; ?>
		<div id="content">
			<div id="top">
				<h1>Add Content</h1>
				<div class="ghost-link"><a href="#add-album">Add Album</a></div>
				<div class="ghost-link"><a href="#add-image">Add Image</a></div>
			</div>
			<!--Add Album Form-->
			<div id="add-album" class="section">
				<h2>Add Album</h2>
				<u><a href="#top">top</a></u>
				<div class="form-msg"><?php echo $album_msg; ?></div>
				<form action="add.php#add-album" method="POST" class="add-form" enctype="multipart/form-data">
					
					<div class="field"><label for="album-title">Album title:</label>
					<span class="form-err"><?php echo $album_title_err ?></span>
					<input type="text" name="album-title" id="album-title" placeholder="My Album" value="<?php echo $album_title ?>" maxlength="50"></div>
					
					<div class="field"><label for="album-desc">Album description:</label>
					<span class="form-err"><?php echo $album_desc_err ?></span>
					<textarea name="album-desc" id="album-desc" placeholder="Just another album."><?php echo $album_desc ?></textarea></div>
					
					<div class="field">Cover photo (optional):
					<div class="tip"><img src="assets/tip-02.png" alt="tip"><p>For best results, upload a square
					or vertical image 210 x 210 px or larger.
					If you don't upload a cover image, your album will have the default cover.</p></div><br>
					<span class="form-err"><?php echo $cover_err; ?></span>
					<input type="file" name="album-cover"></div>
					
					<div class="field">
					<input type="submit" name="submit-album" value="Submit">
					</div>
				
				</form>
			</div>
			
			<!--Add Image Form-->
			<div id="add-image" class="section">
				<h2>Add Image</h2>
				<u><a href="#top">top</a></u>
				<div class="form-msg"><?php echo $image_msg; ?></div>
				<form action="add.php#add-image" method="POST" class="add-form" enctype="multipart/form-data">
				
					<div class="field"><label for="image-title">Image title:</label>
					<span class="form-err"><?php echo $image_title_err ?></span>
					<input type="text" name="image-title" id="image-title" value="<?php echo $image_title ?>" placeholder="My Image"></div>
					
					<div class="field"><label for="image-desc">Image description:</label>
					<span class="form-err"><?php echo $image_desc_err ?></span>
					<textarea name="image-desc" id="image-desc" placeholder="A picture I took."><?php echo $image_desc ?></textarea></div>
					
					<div class="field"><label for="date-taken">Date taken (YYYY-MM-DD):</label>
					<input type="checkbox" name="autodate" value="yes">Auto
					<div class="tip"><img src="assets/tip-02.png" alt="tip"><p>Enter the approximate date
					the photo was taken or created. If photo is a JPG directly from a camera/phone, 
					select "Auto" to attempt to get the exact date from the photo's exif data.</p></div>
					<span class="form-err"><?php echo $date_err ?></span>
					<input type="text" name="date-taken" id="date-taken" value="<?php echo $date_taken?>" placeholder="<?php echo date("Y-m-d") ?>">
					</div>
					
					<div class="field"><label for="image-credits">Image credits:</label>
					<span class="form-err"><?php echo $credits_err ?></span>
					<input type="text" name="image-credits" id="image-credits" value="<?php echo $image_credits ?>" placeholder="Me"></div>
					
					<div class="field"><label>Upload image file:</label>
					<div class="tip"><img src="assets/tip-02.png" alt="tip"><p>Image should be jpg, png, or gif format, and ideally at least 500px wide.
					File size should be less than 2MB.</p></div><br>
					<span class="form-err"><?php echo $file_err ?></span>
					<input type="file" name="image"></div>
					
					<div class="field"><label>Add to album(s) (optional):</label>
					<div class="tip"><img src="assets/tip-02.png" alt="tip"><p>You will also be able to do this later via the "Edit" page. 
					Uncategorized images show up on the "All Images" page.</p></div>
						<div id="album-list">
						<?php
							$album_list = $mysqli->query("SELECT album_id, title FROM Albums");
							while ($row = $album_list->fetch_assoc()) {
								$album_id = $row['album_id'];
								$title = $row['title'];
								echo "<input type='checkbox' name='albums[]' value='$album_id'> $title<br>";
							}
						?>
						</div>
					</div>
					
					<div class="field"><input type="submit" name="submit-image" value="Submit"></div>
				
				</form>
			</div>
		</div>
		<?php include "includes/bottomnav.php"; ?>
	</div>
</body>
</html>