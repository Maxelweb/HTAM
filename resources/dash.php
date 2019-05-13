
<p>Welcome to the HTAccess Manager. From here, you can manage users and directory protection.</p>

<ul>
	<li><strong>Directory protection: </strong> <?=$htam->isProtected() ? good("Active") : bad("Disabled") ;?></li>
	<li><strong>htaccess found: </strong> <?=$htam->hasHtaccess() ? good("Yes") : bad("No") ;?></li>
	<li><strong>htpasswd found: </strong> <?=$htam->hasHtpasswd() ? good("Yes") : bad("No") ;?></li>
	<li><strong>Current Login Username: </strong> <em><?=isset($me) ? $me->name : "None";?></em></li>
	<li><strong>Total users: </strong> <?=!$htam->getUsers() ? 0 : count($htam->getUsers());?></li>
</ul>