<h1>Favorite movies</h1>
<div class="row">
	<?php if (isset($filter)) { foreach ($filter as $value) { ?>
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