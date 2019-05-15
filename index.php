<?php
/*	--------------------------------------------
 
 			[HTAccess Manager - v0.1]
 				Compact version

	--------------------------------------------

	MIT License

	Copyright (c) 2019 Mariano Sciacco

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.

	--------------------------------------------

 	Checkout https://github.com/Maxelweb/HTAM for more information
 
 	Developed by Mariano Sciacco
 	[marianosciacco.it]
   --------------------------------------------

*/

// ------------ Globals ------------

ini_set('display_errors', 1);
ini_set("allow_url_fopen", 1);
error_reporting(E_ALL);

define("VERSION", "0.1");
define("REPO", "https://github.com/Maxelweb/HTAM");
$s = isset($_GET['s']) ? $_GET['s'] : "";
$a = isset($_GET['a']) ? $_GET['a'] : 0;

// ------------ Classes and Functions ------------

include "resources/core.php";


// ------------ Initialization and Authorization ------------

$htam = new htam(dirname(__FILE__));

if(isset($_SERVER['PHP_AUTH_USER']))
	$findme = $htam->findUser($_SERVER['PHP_AUTH_USER']);
else
	$findme = -1;

$me = $findme > -1 ? $htam->getUsers()[$findme] : new HUser();

if($htam->isProtected() && !$me->isAdmin())
{
	echo "<h1><a href='".REPO."'>HTAccess Manager</a></h1>";
	echo "<p>You logged in as a user. You must be an admin to access this page.</p>";
	die();
}

// ---------------------------------

?>
<!DOCTYPE html>
<html>
<head>
	<title>HTAccess Manager</title>
	<meta content='width=device-width, initial-scale=1' name='viewport'>
	<meta name="description" content="HTAccess Manager, manage protected directory with user access">
  	<meta name="keywords" content="HTAM,htaccess,manager,htpasswd,maxelweb">
  	<meta name="author" content="Mariano Sciacco">
  	<link rel="stylesheet" type="text/css" href="resources/style.css">
</head>
<body>

<header>
	<h1>HTAccess Manager</h1>
	<small><strong>Current directory:</strong> <?=$htam->dir();?> <?=$htam->icon();?></small>
</header>

<nav>
	<span>&#x1F3E0; <a href="?">Dashboard</a></span>
	<span>&#x1F4C1; <a href="?s=dir">Manage directory</a></span>
	<span>&#x1F465; <a href="?s=users">Manage users</a></span>
	<span>&#x2615; <a href="?s=updates">Updates</a></span>
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
	&#x2615; <a href="<?=REPO;?>">HTAM v<?=VERSION;?></a> &raquo; developed by <a href='https://marianosciacco.it' target="_blank">Maxelweb</a>
</footer>

</body>
</html>