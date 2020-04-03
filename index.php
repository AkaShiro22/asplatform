<?php
include("config.php");

if(isset($_GET["id"]) && $_GET["id"] == "logout" && isset($_COOKIE['voxis_login'])) {
	setcookie("voxis_login", "", time()-3600);
	header('Location: index.php');
}

$loginError = false;

if(isset($_POST["username"]) && isset($_POST["password"]) && md5($_POST["username"].$_POST["password"]) == md5(PANEL_USERNAME.PANEL_PASSWORD)) {
	setcookie("voxis_login", md5($_POST["username"].$_POST["password"]), time()+10800);
	header('Location: index.php');
}

if(isset($_COOKIE["voxis_login"]) && $_COOKIE["voxis_login"] == md5(PANEL_USERNAME.PANEL_PASSWORD)) {
	include("includes/pages/page.php");
} else {
	include("includes/pages/login.php");
}
?>