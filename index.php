<?php
/*	
 *	HTAccess Manager - v0.1
 *	
 *	Developed by Mariano Sciacco
 *	[marianosciacco.it]
 *
*/

// ------------ Globals ------------

ini_set('display_errors', 1);
ini_set("allow_url_fopen", 1);
error_reporting(E_ALL);


define("VERSION", "v0.1");
$s = isset($_GET['s']) ? $_GET['s'] : "";
$a = isset($_GET['a']) ? $_GET['a'] : 0;

// ------------ Classes and Functions ------------

include "resources/htam.class.php";


// ------------ Initialization and Authorization ------------

$htam = new htam(dirname(__FILE__));

if(isset($_SERVER['PHP_AUTH_USER']))
{
	$findme = $htam->findUser($_SERVER['PHP_AUTH_USER']);
	$me = $findme > -1 ? $htam->getUsers()[$findme] : new HUser();
}

// ---------------------------------

?>
<!DOCTYPE html>
<html>
<head>
	<title>HTAccess Manager</title>
	<meta content='width=device-width, initial-scale=1' name='viewport' />
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<header>
	<h2>HTAccess Manager</h2>
	<small><strong>Current directory:</strong> <?=$htam->dir();?> <?=$htam->icon();?></small>
</header>

<nav>
	<span>&#x1F3E0; <a href="?">Dashboard</a></span>
	<span>&#x1F4C1; <a href="?s=dir">Manage directory</a></span>
	<span>&#x1F465; <a href="?s=users">Manage users</a></span>
</nav>

<article>
<?php
	if($s == "dir")
		include "resources/dir.php";
	elseif($s == "users")
		include "resources/users.php";
	elseif($s == "updates")
		include "resources/updates.php";
	else
		include "resources/dash.php";
?>
</article>

<footer>
	<a href="https://github.com/Maxelweb/HTAM">HTAM <?=VERSION;?></a> - Developed by <a href='https://marianosciacco.it'>M. Sciacco</a> - <a href="?s=updates">Check for updates</a>
</footer>

</body>
</html>