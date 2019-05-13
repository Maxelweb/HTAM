<?php
/*	
 *	HTAccess Manager - v0.1
 *	
 *	Developed by Mariano Sciacco
 *	[marianosciacco.it]
 *
*/

// ------------ Globals ------------

define("VERSION", "v0.1");
$s = $_GET['s'];


ini_set('display_errors', 1);
error_reporting(E_ALL);


include "resources/htam.class.php";

if(isset($_SERVER['PHP_AUTH_USER']))
	$me = new HUser($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'], true);


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
	<small><strong>Current directory:</strong> <?=dirname(__FILE__);?></small>
</header>

<nav>
	&#x1F3E0; <a href="?">Dashboard</a>
	&#x1F4C1; <a href="?s=dir">Manage directory</a>
	&#x1F465; <a href="?s=users">Manage users</a>
</nav>

<article>
<?php
	
	$htam = new htam();

	if($s == "dir")
		include "resources/dir.php";
	else
		include "resources/dash.php";



?>
</article>

<footer>
	HTAccess Manager - <?=VERSION;?>
</footer>

</body>
</html>