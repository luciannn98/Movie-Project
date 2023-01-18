<?php require_once('includes/header.php');
if (isset($_GET['search'])) {
	if (empty($_GET['search'])) {
		die("Mai intai introdu un cuvant pentru a cauta ceva.");
	} else if (strlen($_GET['search']) < 3) {
		die("Introdu cel putin 3 caractere. Tu ai introdus doar ".strlen($_GET['search']));
	}
} ?>

<h1>Search results for: <?php if (isset($_GET['search'])) { print($_GET['search']); } ?></h1>

<?php require('includes/search-form.php');

$filter = array_filter($movies, function($data, $return = []) {
	if (stripos($data['title'], $_GET['search']) === false) {
		return false;
	} else {
		return array_push($return, $data['id']);
	}
}); ?>

<h1>Movies</h1>
<div class="row">
	<?php if (isset($filter)) { foreach ($filter as $key => $value) { ?>
	<div class="col-sm">
		<div class="card" id="<?php print $value['id']; ?>" style="width:12rem;">
			<?php if (check_poster($value['posterUrl'], $value['id'])) { ?>
				<img class="card-img-top" src="<?php print $value['posterUrl']; ?>" alt="Card image cap" width="200" height="250">
			<?php } else { ?>
				<img class="card-img-top" src="img/200x250.png" alt="Card image cap" width="200" height="250">
			<?php } ?>
			<div class="card-body">
				<h5 class="card-title"><?php print $value['title']; ?></h5>
				<p class="card-text"><?php
					if (strlen($value['plot']) < 100) {
						print $value['plot'];
					} else {
						print substr($value['plot'], 0, 100).'...';
					}
				?></p>
				<a href="movies.php?movie_id=<?php print $value['id']; ?>" class="btn btn-primary">Read more</a>
			</div>
		</div>
	</div>
	<?php } } ?>
</div>

<?php if ($filter == []) {
	die("Filmul nu a putut fi gasit, incearca o alta combinatie de caractere.");
}


require_once('includes/footer.php'); ?>