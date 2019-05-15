<section>
	<h3>&#x1F4C1; Directory Management</h3>

<?php

if($s == "dir")
{

	if($htam->hasHtaccess() && $htam->hasHtpasswd() && !$htam->checkPermissions())
	{
		echo bad("The <code>.htaccess</code> or <code>.htpasswd</code> files are not readable or writable. Set the correct permissions (664) from FTP or <a href='?s=dir&a=6'>attempt to change permissions</a>. ");
	}

	if($a == 1)
	{
		if(!$htam->isProtected())
		{
			if(!$htam->hasHtaccess() || !$htam->hasHtpasswd() || $htam->countAdmin() == 0)
			{
				echo bad("Before enabling protection, you must <a href='?s=dir&a=3'>create htaccess and htpasswd</a> and add at least <strong>one administrator</strong> in <a href='?s=users'>Manage users</a>.");
			}
			else
			{
				if($htam->addProtection())
					echo good("Directory protection activated.");
				else
					echo bad("Error: unable to add directory protection. Check file permissions.");
			}
		}
		else
		{
			
						
			if($htam->removeProtection())
				echo good("Directory protection removed.");
			else
				echo bad("Error: unable to remove directory protection. Check file permissions.");
		
		}
	}
	elseif($a == 3)
	{
		if($htam->createHtfiles())
			echo good("Files created successfully.");
		else
			echo bad("Error: unable to create one or more files to initialize the script. Change manually write permissions for this folder.");
	}
	elseif($a == 4)
	{
		if($htam->isProtected())
			echo bad("You must disable protection to delete Htaccess.");
		elseif($htam->deleteHtaccess())
			echo good("Htaccess deleted successfully.");
		else
			echo bad("Error: unable to delete Htaccess.");
	}
	elseif($a == 5)
	{
		if($htam->isProtected())
			echo bad("You must disable protection to delete Htpasswd.");
		elseif($htam->deleteHtpasswd())
			echo good("Htpasswd deleted successfully.");
		else
			echo bad("Error: unable to delete Htpasswd.");
	}
	elseif($a == 6)
	{
		if($htam->setPermissions())
			echo good("File permissions (664) applied successfully. Now the script is able to work properly.");
		else
			echo bad("Error: unable to set permissions (664). You must do it manually from FTP.");
	}
}

?>
</section>

<section>
	<h3>Informations</h3>

	<ul>
		<li><strong>Directory protection: </strong> <?=$htam->isProtected() ? good("Active",1) : bad("Disabled",1) ;?></li>
		<li><strong>htaccess: </strong> <?=$htam->hasHtaccess() ? good("Yes",1) : bad("No",1) ;?></li>
		<li><strong>htpasswd: </strong> <?=$htam->hasHtpasswd() ? good("Yes",1) : bad("No",1) ;?></li>
		<li><strong>Area name: </strong> <?=$htam->getInfo()[0];?>
		<li><strong>htaccess location: </strong> <code><?=$htam->hta();?></code>
		<li><strong>htpasswd location: </strong> <code><?=$htam->htp();?></code>	
	</ul>
</section>

<section>
	<h3>Options</h3>

	<ul>
		<li>&#x1F511; <a href="?s=dir&a=1">Toggle directory protection</a></li>	
		<li>&#x1F4C1; <a href="?s=dir&a=2">Show protected sub-directories and files</a></li>
	</ul>

	<ul>
		<?php if(!$htam->hasHtaccess() || !$htam->hasHtpasswd()){ ?>
			<li><a href="?s=dir&a=3">Create htaccess and/or htpasswd</a> (if not present)</li>
		<?php } if($htam->hasHtaccess()) { ?>
		<li><a href="?s=dir&a=4" onclick="">Delete htaccess file</a> (note: delete all existent config)</li>
		<?php } if($htam->hasHtpasswd()) { ?>
		<li><a href="?s=dir&a=5">Delete htpasswd file</a> (note: delete all users)</li>
		<?php } ?>
	</ul>

<?php

	if($a == 2)
	{
		echo "<h4>Protected files and directories <small>[<a href='?s=dir&a=2'>reload</a> | <a href='?s=dir'>close</a></small>]</h4>";
		$subs = $htam->getSubDirFiles();
		if(empty($subs))
			echo "None";
		else
		{
			echo "<ul class='dir'>";
			foreach ($subs as $sub => $name) 
			{
				if(is_dir($name))
					echo "<li>&#x1F538; $name/</li>";
				else
					echo "<li>&#x1F539; $name</li>";
			}
			echo "</ul>";
		}
	}

?>
</section>