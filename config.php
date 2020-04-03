<?php
//**** Voxis Platform login details ****//
// Username
define("PANEL_USERNAME", "");

// Password
define("PANEL_PASSWORD", "");

//**** License settings ****//
// Your nickname
define("USER_NICKNAME", "");

// Your license key
define("LICENSE_KEY", "");

//**** pipl.com's API key ****//
define("PIPL_API_KEY", "");

//**** MySQL settings - You can get this info from your web host ****//
// The name of the database for Voxis Platform
define("DB_NAME", "");

// MySQL database username
define("DB_USER", "");

// MySQL database password
define("DB_PASSWORD", "");

// MySQL hostname
define("DB_HOST", "");

//**** That's all, stop editing! ****//

// if something wrong- remove next line and report errors to dev team
error_reporting(E_ERROR | E_PARSE);

require_once "includes/omnipay/autoload.php";
require_once "includes/piplapis/search.php";
require_once "includes/piplapis/data/fields.php";
require_once "functions.php";

$VoxisSqlLink = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
?>