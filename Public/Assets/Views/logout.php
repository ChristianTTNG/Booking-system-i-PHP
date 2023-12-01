<?php
session_start();

if(isset($_SESSION['log']))
{	
	//fjerner "log" som holder brukeren logget inn
	unset($_SESSION['log']);
}
//deretter sender til login siden
header("Location: login.php");
die;