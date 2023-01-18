<?php require_once('includes/header.php'); ?>

<ol class="list-group list-group-numbered m-2">
	<?php foreach ($genres as $key) { ?>
		<a href="movies.php?genre=<?php print $key; ?>" class="nounderline">
			<li class="list-group-item d-flex justify-content-between align-items-start">
				<div class="ms-2 me-auto">
					<div class="fw-bold"><?php print $key; ?></div>
				</div>
			</li>
		</a>
	<?php } ?>
</ol>

<?php require_once('includes/footer.php'); ?>