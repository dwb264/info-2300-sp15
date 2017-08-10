<?php

ini_set('display_errors', 1);

session_start();

include 'includes/txt2array.php';
include 'includes/searching.php';
include 'includes/valid.php';

$tags = "";
$array = "";
$search_message = "";
$class = "";
$no_results_message = "";

// Make the array
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

// Generate the array (but don't display it)
if (file_exists("data.txt")) {
	$pre_array = restore_array("data.txt");
} 

// Make list of all existing tags
if (!empty($pre_array)) {
	$tags = make_tag_list($pre_array);
	$tags = array_unique($tags);
	sort($tags);
}

// ----------- // SEARCH FORM // ---------- //

if (isset($_GET['advsearch']) && !empty($pre_array)) {

if (file_exists("data.txt")) {
	$array = restore_array("data.txt");
} 

// Begin search form handling
	$restriction = cleanup_input($_GET['restriction']);
	$url_keyword = cleanup_input($_GET['url_keyword']);
	$title_keyword = cleanup_input($_GET['title_keyword']);
	$desc_keyword = cleanup_input($_GET['desc_keyword']);
	$tags = (isset($_GET['tags'])) ? cleanup_input($_GET['tags']) : "";
	$category = cleanup_input($_GET['category']);
	$rating = intval(cleanup_input($_GET['rating']));
	
	// hide the search form when displaying results
	$class = "class='search-hidden'";
	
	$search_message = "<h3>Search results:</h3>
		<p>Not what you were looking for? <a href='search.php'>Search again</a> or <a href='index.php'>return to homepage</a>.";
		
	if ($restriction === 'any') {
	// return results that meet ANY single one of the criteria
	
		$search_results = array();
		
		// URL keyword
		if (!empty($url_keyword)) {
			// Search urls
			$url_results = searchfilter_inclusive($url_keyword, 'url', $array);
			// Add matches to search results array
			$search_results = array_merge($url_results, $search_results);
		}
		
		// Title keyword
		if (!empty($title_keyword)) {
			// Search urls
			$title_results = searchfilter_inclusive($title_keyword, 'title', $array);
			// Add matches to search results array
			$search_results = array_merge($title_results, $search_results);
		}
		
		// Description keyword
		if (!empty($desc_keyword)) {
			// Search urls
			$desc_results = searchfilter_inclusive($desc_keyword, 'description', $array);
			// Add matches to search results array
			$search_results = array_merge($desc_results, $search_results);
		}
		
		// category
		if(!empty($category)) {
			$cat_match = searchfilter($category, 'category', $array);
			$search_results = array_merge($cat_match, $search_results);
		}
		
		// tags
		if (!empty($tags)) {
			$tag_results = searchfilter_tags($tags, $array);
			$search_results = array_merge($tag_results, $search_results);
		}
		
		// rating
		if (!empty($rating)) {
			// array to collect the results
			$rating_match = array();
			
			// convert rating to int
			$rating = intval($rating);
			
			// find all ratings greater than or equal to that rating
			$rating_match = searchfilter_rating($rating, $array);
			
			$rating_match = array_unique($rating_match, SORT_REGULAR);
			$search_results = array_merge($rating_match, $search_results);
		}
		
		// Display the results
		if (!empty($search_results)) {
			$search_results = array_unique($search_results, SORT_REGULAR);
			$array = $search_results;
		} else {
			$array = "";
			$no_results_message = "Sorry, no links matched your search.";
		}
	
	} elseif ($restriction === 'all') {
	// return results that meet ALL the criteria (default)
	// (I decided to switch which mode is the default mid-way through making this, which is why this is below 'any'.)
		
		// URL keyword
		if (!empty($url_keyword)) {
			// Search url
			$url_results = searchfilter_inclusive($url_keyword, 'url', $array);
			// Get rid of duplicates
			$url_results = array_unique($url_results, SORT_REGULAR);
		} else {
			// Otherwise, no url specified
			$url_results = $array;
		}
		
		// Title keyword
		if (!empty($title_keyword)) {
			// Search titles
			$title_results = searchfilter_inclusive($title_keyword, 'title', $array);
			// Get rid of duplicates
			$title_results = array_unique($title_results, SORT_REGULAR);
		} else {
			// Otherwise, no title specified
			$title_results = $array;
		}
		
		// Description keyword
		if (!empty($desc_keyword)) {
			// Search url
			$desc_results = searchfilter_inclusive($desc_keyword, 'description', $array);
			// Get rid of duplicates
			$desc_results = array_unique($desc_results, SORT_REGULAR);
		} else {
			// Otherwise, nothing specified
			$desc_results = $array;
		}
		
		// tags
		if (!empty($tags)) {
			$tag_results = searchfilter_tags($tags, $array);
		} else {
			$tag_results = $array;
		}
		
		// category
		if(!empty($category)) {
			$cat_match = searchfilter($category, 'category', $array);
		} else {
			$cat_match = $array;
		}
		
		// rating
		if (!empty($rating)) {
			// array to collect the results
			$rating_match = array();
			
			// convert rating to int
			$rating = intval($rating);
			
			// find all ratings greater than or equal to that rating
			$rating_match = searchfilter_rating($rating, $array);
			$rating_match = array_unique($rating_match, SORT_REGULAR);
		
		} else {
			$rating_match = $array;
		}
		
		// comparison of ids for use in array_uintersect below
		function compare_id($x, $y) {
			return strcmp($x['id'], $y['id']);
		}
		
		// find intersection of arrays (items common to all of them)
		$search_results = array_uintersect($url_results, $title_results, $desc_results, $tag_results, $cat_match, $rating_match, 'compare_id');
		
		// If the search results are something other than the entire array:
		if (!empty($search_results)) {
			// Filter out the duplicates and display the results
			$search_results = array_unique($search_results, SORT_REGULAR);
			$array = $search_results;
		// Otherwise display nothing:
		} else {
			$array = "";
			$no_results_message = "Sorry, no links matched your search.";
		}
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
	<title>Advanced Search | Free Design Resources</title>
</head>
<body>

<?php include 'includes/header.php'; include 'includes/voted.php'; ?>

<div class="content container">
	<div id="advanced-search" <?php echo $class ?> >
		<h3>Advanced Search Options</h3>
		<p>Use this form to refine your search. All fields are optional.</p>
		<!--Search Form-->
		<form id="advsearchform" name="advsearch" action="search.php" method="get">
			<div class="form-container">
				<!--Left Column-->
				<div id="left-col">
					<div class="searchformfield">
						<label for="url_keyword">URL contains:</label><br>
						<input type="text" class="keyword" id="url_keyword" name="url_keyword" placeholder="keyword">
					</div>
			
					<div class="searchformfield">
						<label for="title_keyword">Title contains:</label><br>
						<input type="text" class="keyword" id="title_keyword" name="title_keyword" placeholder="keyword">
					</div>
			
					<div class="searchformfield">
						<label for="desc_keyword">Description contains:</label><br>
						<input type="text" class="keyword" id="desc_keyword" name="desc_keyword" placeholder="keyword">
					</div>
				</div>
			
				<!--Right Column-->
				<div id="right-col">
					<div class="searchformfield" id="category">
					<label>Category:</label>
						<select name="category">
						<option value="">(any)</option>
						<option value="Fonts">Fonts</option>
						<option value="Icons">Icons</option>
						<option value="Images">Images</option>
						<option value="Tutorials">Tutorials</option>
						<option value="Other">Other</option>
						</select>
					</div>
					<br>
			
					<div class="searchformfield" id="rating">
					<label>Minimum rating:</label>
						<select name="rating">
						<option value="0">(no stars)</option>
						<option value="1">&#10029;</option>
						<option value="2">&#10029;&#10029;</option>
						<option value="3">&#10029;&#10029;&#10029;</option>
						<option value="4">&#10029;&#10029;&#10029;&#10029;</option>
						<option value="5">&#10029;&#10029;&#10029;&#10029;&#10029;</option>
						</select>
					</div>
			
					<div class="searchformfield">
					Tags:<br>
						<div id="taglist">
						<?php
							if (!empty($tags)) {
								foreach ($tags as $tag) {
									echo "<span class='searchtag'>
									<input type='radio' name='tags' id='$tag' value='$tag'><label for='$tag'>$tag</label>
									</span>";
								}
							} else {
								echo "No tags to display.";
							}
						?>
						</div>
					</div>
				
					<div class="searchformfield" id="restrict">
						<strong>Restrict results to match:</strong><br>
						<input type="radio" name="restriction" id="all" value="all" checked>
						<label for="all">ALL of these criteria</label><br>
						<input type="radio" name="restriction" id="any" value="any">
						<label for="any">ANY of these criteria</label>
						
					</div>
				</div>
			</div>
			
			<br>
			<input type="submit" id="searchbutton" name="advsearch" value="Search">
		</form>
	
	</div>
	
	<!--Links-->
	<div id="links">
	
	<!--Message about results of search-->
	<div id="search-message"><?php echo $search_message; ?></div>
	
		<?php
			if (!empty($array)) {
				$array = array_reverse($array);
				foreach ($array as $item) {
					echo array_to_html($item);
				}
			} else {
				echo $no_results_message;
			}
		?>
	</div>
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>