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

$albums = $mysqli->query("SELECT * FROM Albums");
$images = $mysqli->query("SELECT * FROM Images");

$album_id = $album_msg = $album_title = $album_desc = $album_title_err = $album_desc_err = $cover_err = "";
$image_msg = $image_credits = $image_desc = $image_title = $image_title_err = $image_desc_err = "";
$date_err = $file_err = $credits_err = "";
$date_taken = $autodate = "";
$new_albums = null;

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

// ALBUM EDITING

// Update album
if (isset($_POST['save-album'])) {
	$album_id = sanitize_input($_POST['save-album']);
	$album_title = sanitize_input($_POST['album-title']);
	$album_desc = sanitize_input($_POST['album-desc']);
	
	$errors = False;
	
	// Validate form input
	if (!$album_title) {
		$errors = True;
		$album_msg .= "<p>Album title cannot be blank.</p>";
	} elseif (strlen($album_title) > 50) {
		$errors = True;
		$album_msg .= "<p>Album title cannot be longer than 50 characters.</p>";
	}
	
	if (!$album_desc) {
		$errors = True;
		$album_msg .= "<p>Album description cannot be blank.</p>";
	} elseif (strlen($album_desc) > 255) {
		$errors = True;
		$album_msg .= "<p>Album description cannot be longer than 255 characters.</p>";
	}
	
	if ($errors == False) {
		// Construct query
		$set = "title = '$album_title', description = '$album_desc', date_modified = NOW()";
	
		$album_query = "UPDATE Albums SET $set WHERE album_id = '$album_id';";
		$album_result = $mysqli->query($album_query);
		if ($album_result) {
			header('Location: edit.php?editalbum#edit-album');
		} else {
			$album_msg = "<p>Changes were not saved. Something went wrong.</p>";
		}	
	}
}

// Delete album
if (isset($_POST['delete-album'])) {
	$album_id = $_POST['delete-album'];
	$delete = $mysqli->query("DELETE FROM Albums WHERE album_id = '$album_id';");
	if ($delete==true) {
		header('Location: edit.php?deletealbum#edit-album');
	} else {
		$album_msg = "<p>Album could not be deleted.</p>";
	}
}

// Edit message
if (isset($_GET['editalbum'])) {
	$album_msg = "<p>Changes saved successfully.</p>";
}

// Delete message
if (isset($_GET['deletealbum'])) {
	$album_msg = "<p>Album was deleted successfully.</p>";
}

// IMAGE EDITING

// Update Image
if (isset($_POST['save-image'])) {
	$image_id = sanitize_input($_POST['save-image']);
	$title = sanitize_input($_POST['image-title']);
	$caption = sanitize_input($_POST['image-desc']);
	$date_taken = sanitize_input($_POST['date-taken']);
	$credits = sanitize_input($_POST['credits']);
	
	$errors = False;
	
	// Input validations
	if (!$title) {
		$errors = True;
		$image_msg .= "<p>Image title cannot be blank.</p>";
	} elseif (strlen($title) > 50) {
		$errors = True;
		$image_msg .= "<p>Image title cannot be longer than 50 characters.</p>";
	}
	
	if (!$caption) {
		$errors = True;
		$image_msg .= "<p>Image description cannot be blank.</p>";
	} elseif (strlen($caption) > 255) {
		$errors = True;
		$image_msg .= "<p>Image description cannot be longer than 255 characters.</p>";
	}

	if (!$credits) {
		$errors = True;
		$image_msg .= "<p>Image credits cannot be blank.</p>";
	} elseif (strlen($credits) > 255) {
		$errors = True;
		$image_msg .= "<p>Image credits cannot be longer than 255 characters.</p>";
	}
	
	if (!$date_taken) {
		$errors = True;
		$image_msg .= "<p>Date cannot be blank.</p>";
	} elseif (!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $date_taken)) {
		$errors = True;
		$image_msg .= "<p>Please use format YYYY-MM-DD for the date.</p>";
	} 
	
	if ($errors==False) {
		// Construct query
		$set = "title = '$title', caption = '$caption', date_taken = '$date_taken', credits = '$credits'";
	
		$album_query = "UPDATE Images SET $set WHERE image_id = '$image_id';";
		$album_result = $mysqli->query($album_query);
		if ($album_result) {
			header('Location: edit.php?editimage#edit-image');
		} else {
			$image_msg = "<p>Changes were not saved. Something went wrong.</p>";
		}	
	}
}

// Delete Image 
if (isset($_POST['delete-image'])) {
	$image_id = $_POST['delete-image'];
	$delete = $mysqli->query("DELETE FROM Images WHERE image_id = '$image_id';");
	if ($delete==true) {
		header('Location: edit.php?deleteimage#edit-image');
	} else {
		$image_msg = "<p>Image could not be deleted.</p>";
	}
}

// Edit message
if (isset($_GET['editimage'])) {
	$image_msg = "<p>Changes saved successfully.</p>";
}

// Delete message
if (isset($_GET['deleteimage'])) {
	$image_msg = "<p>Image was deleted successfully.</p>";
}

// Change Image-Album Relationships
if (isset($_POST['album-rels'])) {
	$image_id = $_POST['album-rels'];
	if (isset($_POST['inalbums'])) {
		$album_ids = $_POST['inalbums'];
	}
	
	// Get the ids of the albums the image was originally in
	// TODO THIS IS WRONG, FIX IT
	$old_albums = array();
	$getoldalbums = $mysqli->query("SELECT album_id FROM ImagesinAlbums WHERE image_id = $image_id");
	while ($row = $getoldalbums->fetch_row()) {
		$old_albums[] = $row[0];
	}
	
	// Delete old associations for the image
	$mysqli->query("DELETE FROM ImagesinAlbums WHERE image_id = $image_id");
	
	// Insert new associations for the image
	if (!empty($album_ids)) {
		$insert_values = array();
		foreach ($album_ids as $album_id) {
			$insert_values[] = "($image_id, $album_id)";
		}
		$insert_values = implode(", ", $insert_values);
		$mysqli->query("INSERT INTO ImagesinAlbums (image_id, album_id) VALUES $insert_values;");
		
		// Get the ids that were in new but not in old:
		$new_albums = array_diff($album_ids, $old_albums);
		// Get the ids that were in old but not in new:
		$old_albums = array_diff($old_albums, $album_ids);
		
	}
	
	// Albums whose date_modified should be changed
	$albums_to_update = array();
	foreach ($old_albums as $a_id) {
		$albums_to_update[] = "album_id = $a_id";
	}
	if ($new_albums) {
		foreach ($new_albums as $a_id) {
			$albums_to_update[] = "album_id = $a_id";
		}
	}
	
	if (!empty($albums_to_update)) {
		$where = "WHERE " . implode(" OR ", $albums_to_update);
		$mysqli->query("UPDATE Albums SET date_modified = NOW() $where");
	}
	
	$image_msg = "<p>Changes saved successfully.</p>";
}

?>

<!DOCTYPE html>
<head>
	<title>Edit Content | My Image Gallery</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:700,300' rel='stylesheet' type='text/css'>
	<meta charset="UTF-8">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script type="text/javascript" src="scripts/nav.js"></script>
	<script type="text/javascript" src="scripts/edit.js"></script>
	<script type="text/javascript" src="scripts/smoothscroll.js"></script>
</head>

<body>
	<div id="container">
		<?php include "includes/nav.php"; ?>
		<div id="content">
			<div id="top">
				<h1>Edit Content</h1>
				<div class="ghost-link"><a href="#edit-album">Edit Album</a></div>
				<div class="ghost-link"><a href="#edit-image">Edit Image</a></div><br><br>
				<p><u><a href="#help">Need Help?</a></u></p>
			</div>
			<div id="help" class="section">
				<h2>Editing Help</h2>
				<h3>Edit album/image information</h3>
					<ul>
						<li>Click the pencil icon <img src='assets/edit-icons-01.png' alt='pencil'> to begin editing. You can now
						edit the album/image information by typing in the text boxes.</li>
						<li>Click the cancel icon <img src='assets/edit-icons-04.png' alt='cancel'> to stop editing without saving changes.
					Any unsaved changes will disappear when you refresh the page.</li> 
						<li>Click the save icon <img src='assets/edit-icons-02.png' alt='save'> to permanently save changes.</li>
					</ul>
				<h3>Delete an album or image</h3>
					<ul>
						<li>Click the trash icon <img src='assets/edit-icons-03.png' alt='trash'> to delete an item. This is permanent!
						Deleted albums and images cannot be recovered except by re-adding them via the add page.</li>
						<li>Note that deleting an album will not delete the images it contains.</li>
					</ul>
				<h3>Add or remove an image from albums</h3>
					<ul>
						<li>Click the album icon <img src='assets/edit-icons-05.png' alt='album'> under <b>Options</b> in the images table.
						This brings up a form where you can choose the albums to which the image belongs.</li>
						<li>Click the Save button to make the change permanent. Click the album icon again to make the form go away without saving.</li>
					</ul>
			</div>
			<div id="edit-album" class="section">
				<h2>Edit Album</h2>
				<u><a href="#top">top</a></u><br><br>
				<noscript>This doesn't work with JavaScript disabled!</noscript>
				<div class="form-msg"><?php echo $album_msg; ?></div>
				<?php
					if ($albums) {
						print("<table>");
						print("
							<tr class='thead'>
								<td>Cover</td>
								<td>Title</td>
								<td>Description</td>
								<td>Date Created</td>
								<td>Date Modified</td>
								<td>Options</td>
							</tr>
						");
						while($row = $albums->fetch_assoc()) {
							$album_id = $row['album_id'];
							$cover_file = $row['cover_file'];
							$title = $row['title'];
							$description = $row['description'];
							$date_created = substr($row['date_created'], 0, 10);
							$date_modified = $row['date_modified'];
							if (!$date_modified) {
								$date_modified = "Never";
							} else {
								$date_modified = substr($date_modified, 0, 10);
							}
							
							print("
								<form action='edit.php#edit-album' method='POST'>
								<tr>
									<td><img src='images/$cover_file' alt='$title'></td>
									<td><input type='text' name='album-title' value='$title' disabled></td>
									<td><textarea name='album-desc' disabled>$description</textarea></td>
									<td>$date_created</td>
									<td>$date_modified</td>
									<td>
										<button class='edit' type='button'><img src='assets/edit-icons-01.png' alt='Edit' title='Edit'></button>
										<button type='submit' name='save-album' class='save' value='$album_id' disabled><img src='assets/edit-icons-02.png' alt='Save' title='Save'></button>
										<button type='submit' name='delete-album' class='delete-album' value='$album_id'><img src='assets/edit-icons-03.png' alt='Delete' title='Delete'></button>
									</td>
								</tr>
								</form>
							");
						}
						print("</table>");
					}
				?>
			</div>
			
			<div id="edit-image" class="section">
				<h2>Edit Image</h2>
				<u><a href="#top">top</a></u><br><br>
				<noscript>This doesn't work with JavaScript disabled!</noscript>
				<div class="form-msg"><?php echo $image_msg; ?></div>
				<?php
					if ($images) {
					
						$albumlist = array();
						$albumtitles = $mysqli->query("SELECT album_id, title FROM albums");
						while ($row = $albumtitles->fetch_assoc()) {
							$albumlist[$row['album_id']] = $row['title']; 
						}
						
						print("<table>");
						print("
							<tr class='thead'>
								<td>Image</td>
								<td>Title</td>
								<td>Caption</td>
								<td>Date Taken</td>
								<td>Credits</td>
								<td>Options</td>
							</tr>
						");
						while($row = $images->fetch_assoc()) {
							$image_id = $row['image_id'];
							$filename = $row['filename'];
							$title = $row['title'];
							$caption = $row['caption'];
							$date_taken = $row['date_taken'];
							$credits = $row['credits'];
							
							// Make checkboxes
							$inalbums = $mysqli->query("SELECT Albums.album_id
								FROM ImagesInAlbums INNER JOIN Albums 
									on ImagesInAlbums.album_id = Albums.album_id 
								WHERE image_id = $image_id");
								
							$albumids = array();
							while ($row = $inalbums->fetch_row()) {
								$albumids[] = $row[0];
							}
							
							$checkboxes = "";
							
							foreach ($albumlist as $a_id=>$a_title) {
								if (in_array($a_id, $albumids)) {
									$checkboxes .= "<input type='checkbox' name='inalbums[]' value='$a_id' checked> $a_title ";
								} else {
									$checkboxes .= "<input type='checkbox' name='inalbums[]' value='$a_id'> $a_title ";
								}
							}
							
							print("
								<form action='edit.php#edit-image' method='POST'>
								<tr>
									<td><img src='images/$filename' alt='$title'></td>
									<td><input type='text' name='image-title' value='$title' disabled></td>
									<td><textarea name='image-desc' disabled>$caption</textarea></td>
									<td><input type='text' name='date-taken' value='$date_taken' disabled></td>
									<td><textarea name='credits' disabled>$credits</textarea></td>
									<td>
										<button class='edit' type='button'><img src='assets/edit-icons-01.png' alt='Edit' title='Edit'></button>
										<button type='submit' name='save-image' class='save' value='$image_id' disabled><img src='assets/edit-icons-02.png' alt='Save' title='Save'></button>
										<button type='submit' name='delete-image'  class='delete-image' value='$image_id'><img src='assets/edit-icons-03.png' alt='Delete' title='Delete'></button>
										<button type='button' class='show-album-form' value='$image_id'><img src='assets/edit-icons-05.png' alt='Albums' title='Edit Albums'></button>
									</td>
								</tr>
								</form>
								<form action='edit.php#edit-image' method='POST'>
								<tr class='inalbums $image_id'>
									<td colspan=6>
										Change this image's album affiliation:
										$checkboxes
										<button class='savealbums' type='submit' name='album-rels' value='$image_id'>Save</button>
									</td>
								</tr>
								</form>
							");
						}
						print("</table>");
					}
				?>
				
			</div>
		</div>
		<?php include "includes/bottomnav.php"; ?>
	</div>
</body>
</html>