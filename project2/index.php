<?php

ini_set('display_errors', 1);

session_start();

include 'includes/txt2array.php';
include 'includes/searching.php';
include 'includes/valid.php';

$tags = "";
$array = "";
$search_message = "";

function compare_date($item1, $item2) {
/* This function is used with usort to sort by date both in the default
setting and when "sort by newest" is specifically selected */
	$date1 = strtotime($item1['date']);
	$date2 = strtotime($item2['date']);
	if ($date1 == $date2) {
		return 0;
	}
	return ($date1 > $date2) ? 1 : -1;
}

if (file_exists("data.txt")) {
	// Restore the array of arrays
	$array = restore_array("data.txt");
	
	// Sort by newest first by default
	if (!empty($array)) {
		usort($array, "compare_date");
	}
} 

// SORTING
if (isset($_GET['sort']) && !empty($array)) {
	$sort_mode = cleanup_input($_GET['sort']);
	
	if ($sort_mode == "new") {
	// sort by most recent date
		usort($array, "compare_date");
	
	} elseif ($sort_mode == "rating") {
	// sort by highest rating
	
		function compare_rating($item1, $item2) {
			// get rating1
			if ($item1['votes'] > 0) {
				$rating1 = ($item1['points'] / $item1['votes']);
			} else {
				$rating1 = 0;
			}
			// get rating2
			if ($item2['votes'] > 0) {
				$rating2 = ($item2['points'] / $item2['votes']);
			} else {
				$rating2 = 0;
			}
			//compare
			if ($rating1 == $rating2) {
				return 0;
			}
			return ($rating1 > $rating2) ? 1 : -1;
		}
		usort($array, "compare_rating");
	
	} elseif ($sort_mode == "popular") {
	// sort by most votes
		function compare_votes($item1, $item2) {
			if ($item1['votes'] == $item2['votes']) {
				return 0;
			}
			return ($item1['votes'] > $item2['votes']) ? 1 : -1;
		}
		usort($array, "compare_votes");
	}
}

if (isset($_GET['tag']) && !empty($array)) {
// If a tag is clicked, show all items with that tag
	$tag = cleanup_input($_GET['tag']);
	$array = searchfilter_tags($tag, $array);
}

if (isset($_GET['category']) && !empty($array)) {
// If a category is clicked, show all items in that category
	$category = cleanup_input($_GET['category']);
	$array = searchfilter($category, 'category', $array);
}

//RATING
if(isset($_POST['rate']) && !empty($array)) {

	function compare_votes($x, $y) {
		return strcmp($x['votes'], $y['votes']);
	}
	
	$id = cleanup_input($_POST['rate']);
	$vote = intval(cleanup_input($_POST['vote']));
	
	foreach($array as $item) {
		if ($item['id'] == $id) {
			$item_to_update = $item;
			$item_to_update['votes']++;
			$item_to_update['points'] = $item['points'] + $vote; 
			$item_position = array_search($item, $array);
			$array[$item_position] = $item_to_update;
			break;
		}
	} 
	array_to_file($array, "data.txt");
	// Add item id to list of voted items
	$_SESSION["voted"][] = $id;
}

// ----------- // SEARCH FORM // ---------- //

if (isset($_GET['search']) && !empty($array)) {
// BASIC SEARCH (keyword only)
	// Get the keyword
	$keyword = cleanup_input($_GET['keyword']);
	
	if (!empty($keyword)) {
		// Search url, title, description, tags and category
		$url_results = searchfilter_inclusive($keyword, 'url', $array);
		$title_results = searchfilter_inclusive($keyword, 'title', $array);
		$desc_results = searchfilter_inclusive($keyword, 'description', $array);
		$tag_results = searchfilter_tags($keyword, $array);
		$category_results = searchfilter_inclusive($keyword, 'category', $array);
	
		// Change the links that are displayed to the new filtered array
		$array = array_merge($url_results, $title_results, $desc_results, $tag_results, $category_results);
		$array = array_unique($array, SORT_REGULAR);
		
		// Display a message
		$search_message = "<h3>Search results for: \"$keyword\"</h3>
		<p>Not what you were looking for? <a href='index.php'>Click here to reset</a></p>";
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
	<script src="scripts/ratings.js"></script>
	<title>Free Design Resources</title>
</head>
<body>

<?php include 'includes/header.php'; include 'includes/voted.php'; ?>

<div class="content container">
	<!--Search and Sort Forms-->
	<div id="search">
	
		<!--Sort Form-->
		<form id="sort" action="index.php" method="get">
			<span>Sort by:</span>
			<button type="submit" class="sortbutton" name="sort" value="new">Newness</button>
			<button type="submit" class="sortbutton" name="sort" value="rating">Rating</button>
			<button type="submit" class="sortbutton" name="sort" value="popular">Popular</button>
		</form>
		
		<!--Search Form-->
		<form id="searchform" name="search" action="index.php" method="get">
			<input type="text" id="keyword" name="keyword" placeholder="keyword">
			<button type="submit" id="searchbutton" name="search" value="Search">Search</button>
			<a href="search.php">Advanced Search</a>
		</form>
	
	</div>
	
	<!--Message about results of search-->
	<div id="search-message"><?php echo $search_message; ?></div>
	
	<!--Links-->
	<div id="links">
		<?php
			if (!empty($array)) {
				$array = array_reverse($array);
				foreach ($array as $item) {
					echo array_to_html($item);
				}
			} else {
				print("No links found.");
			}
		?>
	</div>
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>