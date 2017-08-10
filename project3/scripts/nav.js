$("document").ready(function() {
	// Prevent search with no input
	$("#search button").click(function() {
		if (!$("#search input").val()) {
			return false;
		}
	});
});