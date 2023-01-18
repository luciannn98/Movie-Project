<?php
require_once('includes/header.php');


if (!empty($_GET['movie_id'])) {
	$filter = array_filter($movies, function($data) {
		if ($data['id'] == (int)$_GET['movie_id']) {
			return ($data['id'] == (int)$_GET['movie_id']);
			//var_dump( $data['id'] );
		} else {
			return false;
		}
	});
	if (empty($filter)) {
		die("ID-ul filmului nu a putut fi gasit.");
	}
} else {
	die("ID-ul filmului nu este prezent.");
}

if (isset($_POST['addfav'])) {
	$movie_id = [(int)$_GET['movie_id'] => (int)$_POST['favorite']];

	if (isset($_COOKIE['favorite_movie'])) {
		foreach ((array)json_decode($_COOKIE['favorite_movie']) as $key => $val) {
			if ((int)$key == (int)$_GET['movie_id']) {
				$movie_id = array_replace($movie_id, [$_GET['movie_id'] => $val + $_POST['favorite']]);
			} else {
				$movie_id = $movie_id + [$key => $val];
			}
		}
		//var_dump($movie_id);
		setcookie("favorite_movie", json_encode($movie_id), (time() + 31536000), "/");
	} else {
		setcookie("favorite_movie", json_encode($movie_id), (time() + 31536000), "/");
	}
	increment_json_file($_GET['movie_id']);
}

if (isset($_POST['removefav'])) {
	if (isset($_COOKIE['favorite_movie'])) {
		$movie_id = [];
		foreach (json_decode($_COOKIE['favorite_movie'], true) as $key => $val) {
			if ((int)$key == (int)$_GET['movie_id']) {
				$movie_id = ['remove' => $_POST['removefav']];
			}
			$movie_id = $movie_id + [$key => $val];
		}
		//var_dump($movie_id);
		setcookie("favorite_movie", json_encode($movie_id), (time() + 31536000), "/");
	}
	decrement_json_file($_GET['movie_id']);
}

if (isset($_POST['rate_movie'])) {
	if (!empty($_POST['rate'])) {
		$rate = [(int)$_GET['movie_id'] => [(int)$_POST['rate'] => 1]];
		if (rating($rate)) {
			stock_rating_score($_GET['movie_id'], $_POST['rate']);
		} else {
			die("Function error");
		}
	} else {
		die("Rating incorect.");
	}
}

if (isset($_POST['sendRew'])) {
	//var_dump($_POST['data']);
	list($id, $name, $email, $mesaj) = $_POST['data'];

	if (!empty($_POST['gdpr'])) {
		if (!empty($name) && !empty($name) && !empty($email) && !empty($mesaj)) {
			if (!$conn->checkEmail($email)) {
				if ($conn->saveMessage($_POST['data'])) {
					$error = ['success', "Success! Ai trimis un revriew."];
				} else {
					$error = ['danger', "Ceva nu a mers bine, incearca din nou mai tarziu"];
				}
			} else {
				$error = ['danger', "Se pare că ai mai lăsat un revriew pentru acest film. Nu poți să lași mai multe review-uri pentru același film."];
			}
		} else {
			$error = ['danger', "Completeaza toate campurile!"];
		}
	} else {
			$error = ['danger', "Pentru a trimite formularul este necesar sa fi de acord cu procesarea datelor!"];
	}
}

// print "<pre>";
// stock_rating_score(55, 5);
//var_dump($_COOKIE['rating']);

//setcookie("favorite_movie", null, -1, "/");
//var_dump((array)json_decode($_COOKIE['favorite_movie']));
//var_dump(check_cookie_favorite($_GET['movie_id']));

//increment_json_file($_GET['movie_id']);

if (isset($filter)) { foreach ($filter as $key => $value) { ?>

<h1><?php print $value['title']; ?></h1>
<?php if (isset($error)) { ?>
<div class="alert alert-<?php print $error[0]; ?>" role="alert">
	<?php print $error[1]; ?>
</div>
<?php } ?>
<form method="POST">
	<?php if (!check_cookie_favorite($_GET['movie_id'])) { ?>
		<input type="hidden" name="favorite" value="1">
		<button type="submit" name="addfav" class="m-1 btn btn-primary position-relative">Add favorite
			<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
				<?php print get_json_file_favorite($_GET['movie_id']); ?>
				<span class="visually-hidden">Add favorit</span>
			</span>
		</button>
	<?php } else { ?>
		<input type="hidden" name="favorite" value="0">
		<button type="submit" name="removefav" class="m-1 btn btn-danger position-relative">Remove favorite</button>
	<?php } ?>
</form>

<div class="row">
	<div class="col-4">
		<?php if (check_poster($value['posterUrl'], $value['id'])) { ?>
			<img class="card-img-top" src="<?php print $value['posterUrl']; ?>" alt="Card image cap" width="350" height="500">
		<?php } else { ?>
			<img class="card-img-top" src="img/350x500.png" alt="Card image cap" width="350" height="500">
		<?php } ?>
	</div>
	<div class="col-8">
		<h2><?php print $value['year']; ?><span class="badge bg-secondary"><?php print check_old_movie($value['year']); ?></span></h2>
		<p><?php print $value['plot']; ?></p>
		<p>Directed by: <b><?php print $value['director']; ?></b></p>
		<p>Runtime: <b><?php print runtime_prettier($value['runtime']); ?></b></p>
		<p>Genuri: <b><?php print implode(', ', $value['genres']); ?></b></p>
		<p><b>Cast:</b></p>
		<ul>
			<?php foreach(explode(', ', $value['actors']) as $key) { ?>
				<li><?php print $key; ?></li>
			<?php } ?>
		</ul>
		<?php if (!check_rating($_GET['movie_id'])) { if (rating_votes($_GET['movie_id']) == false) { ?>
				<p>Fi primul care voteaza</p>
			<?php } ?>
			<form method="POST">
				<div class="rate">
					<input type="radio" id="star5" name="rate" value="5" />
					<label for="star5" title="5 star">5 stars</label>
					<input type="radio" id="star4" name="rate" value="4" />
					<label for="star4" title="4 star">4 stars</label>
					<input type="radio" id="star3" name="rate" value="3" />
					<label for="star3" title="3 star">3 stars</label>
					<input type="radio" id="star2" name="rate" value="2" />
					<label for="star2" title="2 star">2 stars</label>
					<input type="radio" id="star1" name="rate" value="1" />
					<label for="star1" title="1 star">1 star</label><br/>
					<button type="submit" name="rate_movie" class="btn btn-success btn-xs">Rate</button>
				</div>
			</form>
		<?php } else { ?>
			<p><?php print rating_votes($_GET['movie_id']); ?> vizitatori au votat acest film.</p>
			<div class="rating">
				<?php for ($i = 5; $i >= 1; $i--) { if (user_rating($_GET['movie_id']) == $i) { ?>
					<input type="radio" id="star<?php print $i; ?>" name="rate" value="<?php print $i; ?>" disabled checked/>
					<label for="star<?php print $i; ?>" title="<?php print $i; ?> star"><?php print $i; ?> stars</label>
				<?php } else { ?>
					<input type="radio" id="star<?php print $i; ?>" name="rate" value="<?php print $i; ?>" disabled />
					<label for="star<?php print $i; ?>" title="<?php print $i; ?> star"><?php print $i; ?> stars</label>
				<?php } } ?>
			</div>
		<?php } ?>
	</div>
	<form role="form" id="contact-form" class="contact-form m-2" method="POST">
		<input type="hidden" name="data[]" value="<?php print $_GET['movie_id']; ?>">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<input type="text" class="form-control m-1" name="data[]" autocomplete="off" id="Name" placeholder="Name">
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<input type="email" class="form-control m-1" name="data[]" autocomplete="off" id="email" placeholder="E-mail">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<textarea class="form-control textarea m-1" rows="3" name="data[]" id="Message" placeholder="Message"></textarea>
				</div>
			</div>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="checkbox" value="true" id="flexCheckDefault" name="gdpr">
			<label class="form-check-label" for="flexCheckDefault">
				Sunt de acord cu procesarea datelor cu caracter personal
			</label>
		</div>
		<div class="row">
			<div class="col-md-12">
				<button type="submit" class="btn btn-primary" name="sendRew">Send a message</button>
			</div>
		</div>
	</form>
	<hr class="my-3">
	<div class="col" id="comment-list">
		<?php if ($conn->getMessages($_GET['movie_id'])) { foreach ($conn->getMessages($_GET['movie_id']) as $key) { ?>
		<div class="media g-mb-30 media-comment">
			<span data-letters="<?php print substr($key['name'], 0, 1); ?>"></span>
			<div class="media-body u-shadow-v18 g-bg-secondary g-pa-30">
				<div class="g-mb-15">
					<h5 class="h5 g-color-gray-dark-v1 mb-0"><?php print $key['name']; ?></h5>
				</div>

				<p><?php print $key['mesaj']; ?></p>
			</div>
		</div>
		<hr class="my-3">
		<?php } } else { print "Fii primul care lasă un review pentru acest film!"; } ?>
	</div>
</div>

<?php } } require_once('includes/footer.php'); ?>