
<section>
<?php

if($s == "users")
{	

	$users = $htam->getUsers();
	$edit = isset($_GET['edit']) ? $_GET['edit'] : "";

	if($a == 1)
	{
		$un = $_POST['uname'];
		$up = $_POST['upass'];
		$urp = $_POST['urpass'];

		$find = $htam->findUser($un);
		$user = $find > -1 ? $users[$find] : new HUser();
		

		if(!$user->changeUsername($un))
		{
			echo bad("The username must contain only alphanumeric characters, dash, underscore and spaces (from 3 to 15 characters long).");
		}
		elseif(!$user->changePassword($up))
		{
			echo bad("The password must be at least 4 characters and maximum 32 characters long.");
		}
		elseif($up != $urp)
		{
			echo bad("The password repeated is different.");
		}
		else
		{
			echo $find;
			if($find > -1)
				$users[$find] = $user;
			else
				array_push($users, $user);
			

			if($htam->changeUsers($users))
				echo good("User <strong>$un</strong> has been added/edited successfully. <br> 
					<code>Username: $un - Password: $up</code>");
			else
				echo bad("Error: unable to update users file. Check permissions.");
		}
	}
	elseif($a == 2 && !empty($edit))
	{

		$find = $htam->findUser($edit);
		$user = $find > -1 ? $users[$find] : "";
		if(empty($user))
			echo bad("The user selected has not been found.");
		elseif($user->name() == $me->name() && $htam->isProtected())
			echo bad("You can't delete yourself while directory protection is active.");
		else
		{
			unset($users[$find]);
			echo good("The user has been removed permanently.");
		}
	}
}

?>
</section>

<section>
	<h3>Add / edit user</h3>
	<p>Using an existent username, the user settings will be overrided.</p>
	<form action="?s=users&a=1" method="POST">
		<input type="text" name="uname" placeholder="Username" value="<?=isset($edit) && $a==0?$edit:'';?>"><br>
		<input type="password" name="upass" placeholder="Password">
		<input type="password" name="urpass" placeholder="Repeat Password"><br>
		<input type="submit">
	</form>
</section>

<section>

	<h3>Users list</h3>

	<?php
		if(empty($users))
			echo bad("No user found.",1);
		else
			foreach ($users as $user) 
			{
				echo "<li>{$user->name()}, ";
				echo ($user->isAdmin() ? bad("administrator",1) : good("user",1));
				echo " [<a href='?s=users&edit={$user->name()}'>edit password</a>]"; 
				if(!$user->isAdmin())
					echo " [<a href='?s=users&edit={$user->name()}&a=3'>set admin</a>]";
				else
					echo " [<a href='?s=users&edit={$user->name()}&a=3'>set user</a>]";
				echo " [<a href='?s=users&delete={$user->name()}'>delete</a>]";
				echo "</li>";
			}
	?>
</section>