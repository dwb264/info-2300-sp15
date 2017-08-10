$("document").ready(function() {
	// Prevent search with no input
	$(".searchform button").click(function() {
		if (!$(".searchform input").val()) {
			return false;
		}
	});
});