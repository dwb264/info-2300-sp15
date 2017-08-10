<?php
/*

SEARCHING
A set of search procedures to be used in different situations depending on the kind of results desired. 

List of functions:
searchfilter
searchfilter_inclusive
searchfilter_tags
searchfilter_rating

*/

function searchfilter($target, $parameter, $array) {
/* Finds items in the array of arrays that match a target for a certain parameter and returns those items */
	$filtered = array();
	foreach ($array as $item) {
		if ($item[$parameter] === $target) {
			$filtered[] = $item;
		}
	}
	return $filtered;
}

function searchfilter_inclusive($target, $parameter, $array) {
/* Finds items in the array of arrays that contain (but don't have to exactly match) a target for a certain parameter and returns those items */
	$filtered = array();
	foreach ($array as $item) {
		if (stristr($item[$parameter], $target)) {
			$filtered[] = $item;
		}
	}
	return $filtered;
}

function searchfilter_tags($target_tag, $array) {
/* Finds items with a certain tag */
	$filtered = array();
	foreach ($array as $item) {
		if (in_array($target_tag, $item["tags"])) {
			$filtered[] = $item;
		}
	}
	return $filtered;
}

function searchfilter_rating($min_rating, $array) {
/* Finds items with a rating above a specified threshold */
	$filtered = array();
	foreach ($array as $item) {
		if ($item['votes'] > 0) {
			$rating = intval($item['points'] / $item['votes']);
		} else {
			$rating = 0;
		}
		if ($rating >= $min_rating) {
			$filtered[] = $item;
		}
	}
	return $filtered;
}


?>