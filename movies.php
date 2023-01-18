<?php

require_once("includes/header.php");

if (isset($_GET['genre'])) {

	if (!empty($_GET['genre'])) {
		$filter = array_filter($movies, function($data, $result = []) {
			//var_dump($data['genres']);

			foreach ($data['genres'] as $gen) {
				if (strtolower($gen) == strtolower($_GET['genre'])) {
					$result = array_push($result, $data['id']);
				}
			}
			return $result;
		});
		if (empty($filter)) {
			die("Gen-ul filmului nu a putut fi gasit.");
		}
	} else {
		die("Gen-ul filmului nu este prezent.");
	}

}

if (isset($_GET['page'])) {

	if ($_GET['page'] == 'favorites') {

		$filter = array_filter($movies, function($data, $result = []){
			//var_dump($data);
			$favorite_movie = json_decode($_COOKIE['favorite_movie'], true);

			foreach ($favorite_movie as $key => $value) {
				if ($data['id'] == $key) {
					$result[] = $key;
				}
			}
			return $result;
		});

		if (empty($filter)) {
			die("Nu exista filme favorite.");
		}
	} else {
		die("Page not foud.");
	}

}

//var_dump($filter);

if (isset($_GET['movie_id'])) {
	require_once('includes/movie.php');
} else if (isset($_GET['genre'])) { 
	require_once('includes/genre.php');
} else if (isset($_GET['page'])) {
	if ($_GET['page'] == 'favorites') {
		require_once('includes/favorites.php');
	}
} else {
	require_once('includes/archive-movies.php');
}

require_once("includes/footer.php");

