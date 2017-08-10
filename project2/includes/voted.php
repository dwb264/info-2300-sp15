<!--Keep track of which items have been voted on, allows prevention of multiple votes using jQuery-->
<div id="voted">
<?php
	if (!empty($_SESSION['voted'])) {
		$voted = implode($_SESSION['voted'], ", ");
		print "$voted";
	}

?>
</div>