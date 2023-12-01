<?php 
session_start();
	//config har databasetilkobling + funksjoner
	include("../Private/config.php");

	//ser om bruker har pågående sesjon / innlogget
	$user_data = check_login($con);
?>

<!DOCTYPE html>
<html>
<head>
	<title>Booking for veiledningstimer!</title>
</head>
<body>

	<a href="Assets/Views/logout.php">Logg ut</a>
	<h1>Dette er hovedsiden</h1>

	<br>
	Hei, <?php echo $user_data['firstname']; ?>
</body>
</html>