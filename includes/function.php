<?php

class Connection {
	public function __construct() {
		$this->createTabels();
	}

	public function dbConnect() {
		return new PDO("mysql:host=localhost; dbname=php-proiect", "root", "");
	}

	public function createTabels() {
		//var_dump(file_get_contents("database.sql"));
		$st = $this->dbConnect()->prepare("DESCRIBE `reviews`");
		if (!$st->execute()) {
			$st = $this->dbConnect()->prepare(file_get_contents("database.sql"));
			$st->execute();
		}
	}
}

class General {
	protected $db;

	public function __construct() {
		$this->db = new Connection();
		$this->db = $this->db->dbConnect();
	}

	public function saveMessage($data) {
		$st = $this->db->prepare("INSERT INTO `reviews` (`movie_id`, `name`, `email`, `mesaj`) VALUES (?, ?, ?, ?);");
		if ($st->execute($data)) {
			return true;
		} else {
			return false;
		}
	}

	public function getMessages($movie_id) {
		$st = $this->db->prepare("SELECT `name`, `mesaj` FROM `reviews` WHERE `movie_id` = ?;");
		if ($st->execute([$movie_id])) {
			return $st->fetchAll(PDO::FETCH_ASSOC);
		} else {
			return false;
		}
	}

	public function checkEmail($email) {
		$st = $this->db->prepare("SELECT `email` FROM `reviews` WHERE `email` = ?;");
		if ($st->execute([$email])) {
			if ($st->rowCount() > 0) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}

function runtime_prettier($runtime) {
	return sprintf("%02d:%02d", floor($runtime / 60), $runtime % 60)." Hours";
}

function check_old_movie($year) {
	if ((date('Y') - $year) >= 40) {
		return "(Old movie: " . (date('Y') - $year) . " years)";
	} else {
		return false;
	}
}

function check_cookie_favorite($id) {
	if (isset($_COOKIE['favorite_movie'])) {
		foreach ((array)json_decode($_COOKIE['favorite_movie']) as $key => $val) {
			if ((int)$key == (int)$id) {
				return true;
			} else {
				return false;
			}
		}
	}
}

function check_favorite_movie() {
	return (isset($_COOKIE['favorite_movie'])) ? true : false;
}

function increment_json_file($movie_id) {
	$file = "assets\movie-favorites.json";
	$content = file_get_contents($file);
	$new_data = [];

	if (array_key_exists($movie_id, json_decode($content, true))) {
		foreach (json_decode($content, true) as $key => $value) {
			if ($key == $movie_id) {
				$new_data = $new_data + [$key => $value + 1];
			}
			$new_data = $new_data + [$key => $value];
		}
	} else {
		$new_data = json_decode($content, true) + [$movie_id => 1];
	}

	file_put_contents($file, json_encode($new_data));

}

function decrement_json_file($movie_id) {
	$file = "assets\movie-favorites.json";
	$content = file_get_contents('assets\movie-favorites.json');
	$new_data = [];

	//var_dump((array)json_decode($content)->$movie_id);

	if ((array)json_decode($content)->$movie_id) {
		foreach ((array)json_decode($content) as $key => $value) {
			if ($key == $movie_id) {
				$new_data = $new_data + [$key => $value - 1];
			}
			$new_data = $new_data + [$key => $value];
		}
	} else {
		$new_data = (array)json_decode($content) + [$movie_id => 1];
	}

	file_put_contents($file, json_encode($new_data));

}

function get_json_file_favorite($movie_id) {
	$content = (array)json_decode(file_get_contents('assets\movie-favorites.json'));
	//var_dump($content);

	if (!empty($content[$movie_id])) {
		return $content[$movie_id];
	} else {
		return 0;
	}

}

function check_poster($url, $movie_id) {
	$file = "assets\movie-images.json";
	$arr = json_decode(file_get_contents($file), true);
	$exp_date = time() + (30 * 24 * 60 * 60);

	if (empty($arr)) {
		$arr = array();
		if (@getimagesize($url)) {
			//var_dump([$movie_id => [1, $exp_date]]);
			$arr = $arr + [$movie_id => [1, $exp_date]];
		} else {
			$arr = $arr +  [$movie_id => [0, $exp_date]];
		}
		file_put_contents($file, json_encode($arr));
	} else {
		if (array_key_exists($movie_id, $arr)) {
			if (time() >= $arr[$movie_id][1]) {
				if (@getimagesize($url)) {
					$arr[$movie_id] = [1, $exp_date]; 
				} else {
					$arr[$movie_id] = [0, $exp_date];
				}
				file_put_contents($file, json_encode($arr));
			} else {
				return $arr[$movie_id][0];
			}
		} else {
			if (@getimagesize($url)) {
				$arr = $arr + [$movie_id => [1, $exp_date]];
			} else {
				$arr = $arr +  [$movie_id => [0, $exp_date]];
			}
			file_put_contents($file, json_encode($arr));
		}
	}
}

function check_rating($id) {
	if (isset($_COOKIE['rating'])) { //Verifica daca este setat COOKIE
		$data = json_decode($_COOKIE['rating'], true);
		if (array_key_exists($id, $data)) {
			return true;
		} else {
			return false;
		}
	}
}

function rating($data) {
	if (isset($_COOKIE['rating'])) { //Verifica daca este setat COOKIE
		$datas = json_decode($_COOKIE['rating'], true);
		foreach ($data as $key => $value) {
			if (array_key_exists($key, $datas)) {
				foreach ($value as $k => $v) {
					if (array_key_exists($k, $datas[$key])) {
						$data[$key][$k] = $datas[$key][$k] + $v;
						$data[$key] = $data[$key] + $datas[$key];
					} else {
						$data[$key] = $data[$key] + $datas[$key];
					}
				}
			} else {
		 		$data = $data + $datas;
			}
		}
		return setcookie("rating", json_encode($data), (time() + 31536000), "/");
	} else {
		return setcookie("rating", json_encode($data), (time() + 31536000), "/");
	}
}

function stock_rating_score($movie_id, $star) {
	$path = 'assets/movie-rating.json';
	if (!file_exists($path)) {
		file_put_contents($path, json_encode([$movie_id => [$star => 1]]));
	} else {
		$content = json_decode(file_get_contents($path), true);
		$data = null;

		if (array_key_exists($movie_id, $content)) {
			$filter = array_filter($content[$movie_id], function($data, $return = []) {
					return array_push($return, $data);
			});

			if (!empty($filter)) {
				if (array_key_exists($star, $filter)) {
					$content[$movie_id][$star] = $content[$movie_id][$star]+1;
					$data = $content;
				} else {
					$content[$movie_id] = $content[$movie_id] + [$star => 1];
					$data = $content;
				}
			}
		} else {
			$data = $content + [$movie_id => [$star => 1]];
		}

		//var_dump($data);
		file_put_contents($path, json_encode($data));
	}
}

function user_rating($movie_id) {
	$content = json_decode($_COOKIE['rating'], true);
	$stars = null;

	if (array_key_exists($movie_id, $content)) {
		foreach ($content[$movie_id] as $stars => $votes) {
			//var_dump($stars);
			$stars = $stars;
		}
		return (int)$stars ;
	} else {
		return false;
	}
}

function rating_votes($movie_id) {
	$path = 'assets/movie-rating.json';
	$content = json_decode(file_get_contents($path), true);
	$votes = 0;

	if (array_key_exists($movie_id, $content)) {
		$n = count($content[$movie_id]);
		foreach ($content[$movie_id] as $stars => $vote) {
			$votes += $vote;
		}
		return (int)$votes ;
	} else {
		return false;
	}
}
