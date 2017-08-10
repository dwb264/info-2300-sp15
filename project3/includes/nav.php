<div class="navbar">
	<!--Nav Links-->
	<div class="nav">
	<?php
		$admin_only = array("Add", "Edit");
		
		$links = array(
			"Home" => "index.php",
			"Albums" => "albums.php",
			"Images" => "images.php",
			"Add" => "add.php",
			"Edit" => "edit.php",
		);
	
		print('<ul>');
	
		foreach ($links as $title => $link) {
			if (in_array($title, $admin_only)) {
				if (isset($_SESSION['logged_user'])) {
					print("<li><a href='$link'>$title</a></li>");
				}
			} else {
				print("<li><a href='$link'>$title</a></li>");
			}
		}
		
		if (!isset($_SESSION['logged_user'])) {
				print("<li><a href='login.php'>Login</a></li>");
		} else {
			print("<li><a href='index.php?logout=1'>Logout</a></li>");
		}
		
		print('</ul>');
		
	?>
	
	<form id="search" action="search-results.php" method="get">
		<input type="text" name="search" placeholder="Search">
		<button type="submit">Go</button>
		<a href="adv-search.php">More Options</a>
	</form>
	
	</div>
	
	
	<a href="index.php"><h1>My Image Gallery</h1></a>
	
	
	
</div>