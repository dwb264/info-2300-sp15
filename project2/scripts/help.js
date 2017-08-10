// All the help messages displayed when filling out the "add entry" form

$(document).ready(function() {
	$("#url-field").mouseenter(function() {
		$("#helptitle").text("Link");
		$("#helpdesc").text("Copy and paste the link to the resource you would like to share. Please be sure to enter the full URL.");
	});
	
	$("#title-field").mouseenter(function() {
		$("#helptitle").text("Title");
		$("#helpdesc").text("Give the link a specific, concise title. It may be easiest just to use the page's original title.");
	});
	
	$("#cat-field").mouseenter(function() {
		$("#helptitle").text("Category");
		$("#helpdesc").text("Choose the category that best matches the resource. If it doesn't fit any category, choose \"Other\" from the list.");
	});
	
	$("#desc-field").mouseenter(function() {
		$("#helptitle").text("Description");
		$("#helpdesc").text("Say a little about the resource. What's unique or interesting about it? Keep your description to about 1-2 sentences.");
	});

	$("#tag-field").mouseenter(function() {
		$("#helptitle").text("Tags");
		$("#helpdesc").text("To help others find the resource, tag it! Separate your tags with commas. Please restrain yourself to no more than 5 tags.");
	});
	
	$("#addentry").children().mouseleave(function() {
		$("#helptitle").text("Add a link.");
		$("#helpdesc").text("Fields marked with * are required.");
	});
	
});