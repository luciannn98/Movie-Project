<?php
error_reporting(E_ALL);

require_once('function.php');

$conn = new General;

define("TAG", "CGL");

if (explode('\\', $_SERVER['SCRIPT_FILENAME'])[5] != 'index.php' || explode('\\', $_SERVER['SCRIPT_FILENAME'])[5] != 'contact.php') {
	$movies = json_decode(file_get_contents('./assets/movies-list-db.json'), true)['movies'];
	$genres = json_decode(file_get_contents('./assets/movies-list-db.json'), true)['genres'];
}

$menu = [
	"Home" => "index.php",
	"Movies" => "movies.php",
	"Contact" => "contact.php",
	"Genres" => "genres.php",
	"Favorites" => "movies.php?page=favorites"
];

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php
		switch (basename($_SERVER['PHP_SELF'])) {
			case "index.php":
				print "Home - Cristea Gabriel-Lucian";
				break;
			case "movies.php":
				print "Lista filme - Cristea Gabriel-Lucian";
				break;
			case "contact.php":
				print "Contact - Cristea Gabriel-Lucian";
				break;
			case "search-results.php":
				print "Search results - Cristea Gabriel-Lucian";
				break;
			default:
				print "Cristea Gabriel-Lucian";
				break;
		}
	?></title>
	<link rel="stylesheet" type="text/css" href="style.css">

	<!-- CSS only -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- JavaScript Bundle with Popper -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<div class="container-fluid">
			<a class="navbar-brand" href="index.php">CGL</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav me-auto mb-2 mb-lg-0">
					<?php
					if (isset($menu)) {
						foreach ($menu as $key => $value) {
							if ($key == "Favorites") {
								if (check_favorite_movie()) { ?>
									<li class="nav-item">
										<a class="nav-link <?php if (explode('\\', $_SERVER['SCRIPT_FILENAME'])[5] == $value) { print "active"; } ?>" href="<?php print $value; ?>"><?php print $key; ?></a>
									</li>
								<?php } } else { ?>
									<li class="nav-item">
										<a class="nav-link <?php if (explode('\\', $_SERVER['SCRIPT_FILENAME'])[5] == $value) { print "active"; } ?>" href="<?php print $value; ?>"><?php print $key; ?></a>
									</li>
							<?php } } } ?>
				</ul>
				<?php require_once('search-form.php'); ?>
			</div>
		</div>
	</nav>

	<div class="container">