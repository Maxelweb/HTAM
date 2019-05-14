
<section>
	<p>Welcome to the <em>HTAccess Manager</em>. From here, you can manage users and directory protection.</p>

	<ul>
		<li><strong>Directory protection: </strong> <?=$htam->isProtected() ? good("Active",1) : bad("Disabled",1) ;?></li>
		<li><strong>Current Login Username: </strong> <em><?=isset($me) ? $me->name : "None";?></em></li>
		<li><strong>Total users: </strong> <?=!$htam->getUsers() ? 0 : count($htam->getUsers());?></li>
	</ul>
</section>