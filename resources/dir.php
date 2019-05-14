
<p>If you have created at least one administrator, you can enable or disable protection for the current directory.</p>

<h3>Information</h3>

<ul>
	<li><strong>Directory protection: </strong> <?=$htam->isProtected() ? good("Active") : bad("Disabled") ;?></li>
</ul>

<h3>Options</h3>

<ul>
	<li><a href="?s=dir&a=1">Toggle directory protection</a></li>
	<li><a href="?s=dir&a=2">Show protected sub-directories and files</a></li>
</ul>



<?php
	if($a == 2)
	{
		echo "<h4>Protected files and directories</h4>";
		$subs = $htam->getSubDirFiles();
		if(empty($subs))
			echo "None";
		else
		{
			echo "<ul>";
			foreach ($subs as $sub => $name) {
				echo "<li>$name</li>";
			}
			echo "</ul>";
		}

	}
?>