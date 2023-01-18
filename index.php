<?php

require_once('includes/header.php');

//Get current hour
$hour = date("H");

if ($hour < 12):
    $mesaj = "Good morning";
elseif ($hour < 17):
    $mesaj = "Good afternoon";
elseif ($hour < 21):
    $mesaj = "Good evening";
else:
    $mesaj = "Hello";
endif;

?>

<center>

	<h1>
		<?php print $mesaj; ?> 
		<span style="color:red;">Website-ul</span>
		<span style="color:yellow;">meu de</span>
		<span style="color:blue;">filme.</span>
	</h1>
	<a href="movies.php" class="btn btn-danger btn-lg">Vezi toate filmele</a>
</center>


<?php require_once('includes/footer.php');