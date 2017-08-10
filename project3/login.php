<?php

ini_set('display_errors', '1');

session_start();

// Start database connection
require_once "config.php";
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($mysqli->errno) {
	print $mysqli->error;
	exit();
}

$login_err = $username = "";
$salt = "mKPJ4LR3xTUPyS6";

// Input clean-up function
function sanitize_input($input) {
	$input = trim($input);
	$input = strip_tags($input);
	$input = htmlentities($input, ENT_QUOTES);
	return $input;
}

if (isset($_POST['submit'])) {
	$username = sanitize_input($_POST['username']);
	$password = sanitize_input($_POST['password']);
	$hashpassword = hash('sha256', $password.$salt);
	
	$user = $mysqli->query("SELECT * FROM Users WHERE username = '$username' AND hashpassword = '$hashpassword';");
	if ($user && $user->num_rows == 1) {
		$_SESSION['logged_user'] = $username;
	} else {
		$login_err = "<p>Incorrect username or password.</p>";
	}
}
?>

<!DOCTYPE html>
<head>
	<title>Login | My Image Gallery</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:700,300' rel='stylesheet' type='text/css'>
	<meta charset="UTF-8">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script type="text/javascript" src="scripts/nav.js"></script>
</head>

<body>
	<div id="container">
	<?php include "includes/nav.php"; ?>
		<div id="content">
			<div id="top" class="login">
			
			<?php
				if (!isset($_SESSION['logged_user'])) {
			?>
				<h1>Login</h1>
				<?php echo $login_err ?>
				<form action="login.php" method="POST">
					<input type="text" name="username" placeholder="username"><br>
					<input type="password" name="password" placeholder="password"><br>
					<button type="submit" name="submit">Submit</button>
				</form>
			
			<?php
				} else {
					print("<h1>Welcome, $username</h1>");
				}
			?>
				
			</div>
		</div>
		<?php include "includes/bottomnav.php"; ?>
	</div>
</body>
</html>