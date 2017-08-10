$(document).ready(function() {
	// Get ids of voted items from a hidden div
	var voted_ids = $("#voted").text();
	var voted = voted_ids.split(", ");
	
	// Replace vote menu with confirmation message for voted items
	$.each(voted, function(i, val) {
		$val = $.trim(val);
		if ($val) $("#"+$val+" .rate").html("<b>&#10003; Thanks for voting!</b>");
	});
});