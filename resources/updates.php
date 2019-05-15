<?php


$hup = new HUpdates();

?>

<section>

	<?php 

		if($hup->latestVersion() == "N/A")
			echo bad("Unable to check for new updates. Try later.");
		elseif($hup->currentVersion() != $hup->latestVersion()) 
			echo bad("Your version is <b>not</b> up to date.");
		else
			echo good("Your version is up to date.");
	?>

	<h3>Updates checker</h3>
	<ul>
		<li><strong>Current version:</strong> <?=$hup->currentVersion();?></li>
		<li><strong>Latest version:</strong> <?=$hup->latestVersion();?></li>
	</ul>

	<?php
		if(!empty($hup->info()))
		{
			echo "<div class='responsive'><table>";
			foreach ($hup->info() as $key => $value) 
			{
				echo "<td><strong>$key</strong></td><td>$value</td>";
			}
			echo "</div>";
		}
	?>

</section>