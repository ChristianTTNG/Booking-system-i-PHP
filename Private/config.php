<?php
//database parameter
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "bookingsystem_veiledning";

//database tilkobling
$con = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname);

//Func for sjekk av innlogging
function check_login($con) {
	//hvis sesjon har lagret brukerdata
	if(isset($_SESSION['log'])){

		$username = $_SESSION['log'];
		$query = "select * from users where username = '$username' limit 1";
		$result = mysqli_query($con,$query);

		//hvis query gir svar og den ikke er tom-
		if($result && mysqli_num_rows($result) > 0)
		{
			//returnerer brukerdata
			$user_data = mysqli_fetch_assoc($result);
			return $user_data;
		}
	}

	//sendes til login (hvis ikke brukerdata ble sendt tilbake)
	header("Location: Assets/Views/login.php");
	die;

}

//Trimmer ned inputs for sikker drift
function validData($x){
	$x = trim($x);
	$x = stripslashes($x);
	$x = Strip_tags($x);
	$x = htmlspecialchars($x);
	return $x;
}

?>