$("document").ready(function() {
	$(".edit").click(function() {
		$row = $(this).parent().parent();
		$inputs = $row.children("td").children("input, textarea, .save");
			if ($inputs.attr("disabled")) {
				$inputs.removeAttr("disabled");
				$(this).html("<img src='assets/edit-icons-04.png' alt='Cancel' title='Cancel'>");
			} else {
				$inputs.attr("disabled", true);
				$(this).html("<img src='assets/edit-icons-01.png' alt='Edit' title='Edit'>");
			}
	});
	
	$(".show-album-form").click(function() {
		$id = $(this).attr("value");
		$(".inalbums."+$id).fadeToggle();
	});
	
	$(".delete-album").click(function() {
		$delete = confirm("Do you really want to delete this album? This cannot be undone.");
		if ($delete == false) {
			return false;
		}
	});
	
	$(".delete-image").click(function() {
		$delete = confirm("Do you really want to delete this image? This cannot be undone.");
		if ($delete == false) {
			return false;
		}
	});
});