<?php
/*
 
TEXT TO ARRAY
A set of functions used when turning data.txt into an array of arrays
and when writing data to the data.txt file.

List of functions:
format_url
format_tags
restore_array
array_to_file
array_to_html
make_tag_list

*/

function format_url($url) {
/* Formats a url so that a working link can be generated */
	$test = "/^((http:\/\/)|(https:\/\/))/";
	if (!preg_match($test, $url)) {
		$url = "http://" . $url;
	}
	return $url;
}

function format_tags($tags) {
/* Converts tags from string into an array */
	$tags = preg_split("/((, )|,)/", $tags);
	$formatted_tags = array();
	foreach ($tags as $tag) {
		$tag = trim($tag);
		$tag = preg_replace("/\s/", "-", $tag);
		$formatted_tags[] = $tag;
	}
	return $formatted_tags;
}

function restore_array($filename) {
/* Restores the array of arrays from the .txt file. */
	
	// Get the data file
	$file = file($filename);
	
	// If file is not empty:
	if (!empty($file)) {
	
		// Make a blank array
		$array = array();
	
		// Format each line of the file
		foreach ($file as $item) {
			$item = trim($item);
			// Separate the data by the tab delimiter
			$item = explode("\t", $item);
			// Put the data in an associative array
			$item = array(
				"id" => $item[0],
				"url" => format_url($item[1]),
				"title" => $item[2],
				"description" => $item[3],
				"category" => $item[4],
				"tags" => format_tags($item[5]),
				"points" => intval($item[6]),
				"votes" => intval($item[7]),
				"date" => $item[8],
			);
			// Add the item array to the main array
			$array[] = $item;
		}
		return $array;
	}
}

function array_to_file($array, $filename) {
/* Puts the array of arrays back in the .txt file */
	// turn the array back into a string separated w/ tabs and newlines
	$new_contents = array();
	foreach ($array as $item) {
		$item['tags'] = implode(", ", $item['tags']);
		$item = implode("\t", $item);
		$new_contents[] = $item;
	}
	$new_contents = implode("\n", $new_contents);
	// get the current file contents
	$file = file_get_contents($filename);
	// replace the current contents with the new contents
	$file = file_put_contents($filename, $new_contents);
}

function array_to_html($item) {
/* Converts an item into html */
	
	// Get the category icon, which is also a link to entries in the same category:
	switch($item["category"]) {
		case "Fonts":
			$icon = "<form action='index.php' method='get' class='category'>
			<button type='submit' name='category' value='Fonts'>
			<img src='assets/fonts.png' alt='fonts'>
			</button></form>";
			break;
		case "Icons":
			$icon = "<form action='index.php' method='get' class='category'>
			<button type='submit' name='category' value='Icons'>
			<img src='assets/icons.png' alt='icons'>
			</button></form>";
			break;
		case "Images":
			$icon = "<form action='index.php' method='get' class='category'>
			<button type='submit' name='category' value='Images'>
			<img src='assets/images.png' alt='images'>
			</button></form>";
			break;
		case "Tutorials":
			$icon = "<form action='index.php' method='get' class='category'>
			<button type='submit' name='category' value='Tutorials'>
			<img src='assets/tutorials.png' alt='tutorials'>
			</button></form>";
			break;
		case "Tools":
			$icon = "<form action='index.php' method='get' class='category'>
			<button type='submit' name='category' value='Tools'>
			<img src='assets/tools.png' alt='tools'>
			</button></form>";
			break;
		case "Other":
			$icon = "<form action='index.php' method='get' class='category'>
			<button type='submit' name='category' value='Other'>
			<img src='assets/other.png' alt='other'>
			</button></form>";
			break;
		default:
			$icon = "<form action='index.php' method='get' class='category'>
			<button type='submit' name='category' value='Other'>
			<img src='assets/other.png' alt='other'>
			</button></form>";
			break;
	}
	
	// Make tag buttons:
	$tags = "";
	foreach ($item["tags"] as $tag) {
		$tags .= "<form action='index.php' method='get' class='tagbutton'>
		<button type='submit'  name='tag' value='$tag'>$tag</button>
		</form>";
	}
	
	// Calculate current rating:
	if ($item['votes'] > 0) {
		$rating = round(($item['points'] / $item['votes']), 1);
	} else {
		$rating = 0;
	}
	
	// Make rating menu:
	$rate = "
		<form action='index.php#$item[id]' method='post'>
		<select name='vote'>
			<option value='5'>&#10029;&#10029;&#10029;&#10029;&#10029;</option>
			<option value='4'>&#10029;&#10029;&#10029;&#10029;</option>
			<option value='3'>&#10029;&#10029;&#10029;</option>
			<option value='2'>&#10029;&#10029;</option>
			<option value='1'>&#10029;</option>
		</select>
		<button type='submit' name='rate' value='$item[id]'>OK</button>
		</form>";
	
	// The full html:
	$html = "
	<div class='item' id='$item[id]'>
		<div class='link'>
			$icon
			<h1><a href='$item[url]' target='_blank'>$item[title]</a></h1>
			<p>$item[description]</p>
		</div>
	
		<div class='linkinfo'>
			<div class='date'>
				<strong>Date Added: </strong>$item[date]
			</div>
			<div class='tags'>
				<strong>Tags: </strong>$tags
			</div>
			<div class='rating'>
				<strong>Rating: </strong>$rating ($item[votes] votes)
			</div>
			<div class='rate'>
				<strong>Rate It: </strong>$rate
			</div>
		</div>
	</div>
	";
	
	return $html;
}

function make_tag_list($array) {
/* Gets the tags from the array of arrays and puts them in their own array */
	$tags = array();
	foreach ($array as $item) {
		foreach ($item["tags"] as $tag) {
			$tags[] = trim($tag);
		}
	}
	return $tags;
}


?>