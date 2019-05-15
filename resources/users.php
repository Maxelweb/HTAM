
<section>
	<h3>&#x1F465; User management</h3>

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
		elseif(isset($me) && $user->name() == $me->name() && $htam->isProtected())
			echo bad("You can't delete yourself while directory protection is active.");
		else
		{
			unset($users[$find]);
			if($htam->changeUsers($users))
				echo good("The user has been removed permanently.");
			else
				echo bad("Error: unable to update users file. Check permissions.");
			
		}
	}
	elseif($a == 3 && !empty($edit))
	{
		$find = $htam->findUser($edit);
		$user = $find > -1 ? $users[$find] : "";
		if(empty($user))
			echo bad("The user selected has not been found.");
		elseif( ($htam->countAdmin() == 1 || (isset($me) && $user->name() == $me->name()) )
					&& $user->isAdmin() && $htam->isProtected())
			echo bad("You can't remove the last administrator or yourself as administrator while protection is enabled.");
		else
		{
			$users[$find]->toggleAdmin($user->isAdmin() ? 0 : 1); 
			if($htam->changeUsers($users))
				echo good("The role of the user <strong>{$user->name()}</strong> has been changed.");
			else
				echo bad("Error: unable to update users file. Check permissions.");
			
		}
	}
}

?>
</section>

<script>
	var names = [<?php foreach($users as $u) echo "'".$u->name()."',";?>];
	function checkUsername()
	{
		var i = document.getElementById("ncheck");
		var j = document.getElementById("checker");

		if(i.value === "" || i.value.length < 3)
			j.innerHTML = "";
		else if(names.indexOf(i.value) > -1)
			j.innerHTML = "<span class='bad'>&#x2716; Username in use! The password will be overrided.</span>"; 
		else
			j.innerHTML = "<span class='good'>&#x2714; Username not used!</span>";
	}

	function setName(name)
	{
		document.getElementById("ncheck").value = name;
	}
</script>

<section>
	<h3>Add / edit user</h3>
	<p>If the username is already in use, the password will be overrided with the new one.</p>
	<form action="?s=users&a=1" method="POST">
		<input type="text" name="uname" id="ncheck" onkeyup="checkUsername();" placeholder="Username" value="<?=$a==1?$un:'';?>">
		<span id="checker"></span><br>
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
		{
			echo "<span class='box'><strong>Total users:</strong> ".count($users).", <strong>Administrators:</strong> ".$htam->countAdmin()." </span>";
			echo "<div class='responsive tdcenter'><table>";
			foreach ($users as $user) 
			{
				echo "<tr>";
				echo "<td><strong>{$user->name()}</strong></td>";
				echo "<td>".($user->isAdmin() ? bad("&#x1F920; administrator",1) : good("&#x1F603; user",1))."</td>";
				echo "<td><span class='box'>&#x270F; <a href='#' onclick='setName(\"{$user->name()}\"); checkUsername();'>edit password</a></span></td>"; 
				if(!$user->isAdmin())
					echo "<td><span class='box'>&#x2B06; <a href='?s=users&edit={$user->name()}&a=3'>set admin</a></span></td>";
				else
					echo "<td><span class='box'>&#x2B07; <a href='?s=users&edit={$user->name()}&a=3'>set user</a></span></td>";
				echo "<td><span class='box'>&#x274C; <a href='?s=users&a=2&edit={$user->name()}'>delete</a></span></td>";
				echo "</tr>";
			}
			echo "</table></div>";
		}

	?>
</section>