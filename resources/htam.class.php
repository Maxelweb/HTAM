<?php

class HTAM
{
	private $currentDir;
	
	function __construct()
	{
		$this->currentDir = dirname(__FILE__);
	}

	private function searchFor($file)
	{
		return file_exists($this->currentDir.'/'.$file);
	}

	private function getInside($string)
	{
	    $pattern = "/\"(.*?)\"/";
	    preg_match_all($pattern, $string, $matches);
	    if(!empty($matches[1]))
	        return $matches[1];
	    return array();
	}

	private function getFileContent($file)
	{
		if($this->searchFor($file))
			return false;

		return file($this->currentDir.'/'.$file);
	}

	function changeDir($newdir)
	{
		$this->currentDir = $newdir;
	}

	function hasHtaccess()
	{
		return $this->searchFor($this->currentDir."/.htaccess");
	}

	function hasHtpasswd()
	{
		return $this->searchFor($this->currentDir."/.htpasswd");
	}

	function createHtfiles()
	{
		if(!hasHtaccess())
		{
			$file = fopen($this->currentDir."/.htaccess", "w+");
			fclose($file);
		}

		if(!hasHtpasswd())
		{
			$file = fopen($this->currentDir."/.htpasswd", "w+");
			fclose($file);
		}
	}

	function getInfo()
	{
		$info = array();
		$lines = $this->getFileContent($this->currentDir."/.htaccess");
		if(!$lines)
			return $info;

		foreach ($lines as $line) {
			if(preg_match("/AuthName/"))
				$info[0] = getInside($line);
			elseif(preg_match("/AuthUserFile/"))
				$info[1] = getInside($line);
		}

		return $info;
	}

	function isProtected()
	{
		if(!$this->hasHtaccess())
			return false;

		$lines = $this->getFileContent($this->currentDir."/.htaccess");
		$i = 0;
		while($i<count($lines) && !preg_match("/HTAM - START/", $lines[$i]))
			$i++;

		return !($i >= count($lines));
	}

	function addProtection()
	{
		$fh = fopen($this->currentDir."/.htaccess", "w+");
		$prot = "# HTAM - START \n # HTAccess Manager Auto-generated script \nAuthType Basic \nAuthName \"Protected area\" \nAuthUserFile \"$this->currentDir/" . ".htpasswd" . "\"\nrequire valid-user \n# HTAM - END\n";
		fwrite($fh, $prot);
		fclose($fh);
	}

	function removeProtection()
	{
		$lines = $this->getFileContent($this->currentDir."/.htaccess");
		$i = 0;
		while($i<count($lines) && !preg_match("/HTAM - START/", $line[$i]))
			$i++;

		if($i >= count($lines))
			return false;

		for($j=$i; $j < $i+7; $j++)
			unset($lines[$x]);
		
		$cleaned = "";
		foreach($lines as $line)
			$cleaned .= $line;
		
		$fh = fopen($this->currentDir."/.htaccess", "a");
		fwrite($fh, $cleaned);
		fclose($fh);

		return true; 
	}

	function getUsers()
	{
		$users = array();
		if(!$this->hasHtpasswd())
			return $users;

		$lines = getFileContent($this->currentDir."/.htpasswd");
		foreach ($lines as $line) {
			$item = explode(":", $lines);
			array_push($users, new HUser($item[0], $item[1], $item[2]));
		}

		return $users;
	}

	function changeUsers($users)
	{
		if($this->isProtected() && empty($users))
			return false;

		if($this->isProtected())

		$checkOneAdmin = false;
		$cleaned = "";
		foreach($users as $user)
		{
			$cleaned .= $user->name().':'.$user->psw().':'.$user->isAdmin();
			if(!$checkOneAdmin && $user->isAdmin()) 
				$checkOneAdmin = true;
		}

		if(!$checkOneAdmin && $this->isProtected())
			return false;

		
		$fh = fopen($this->currentDir."/.htpasswd", "w+");
		fwrite($fh, $cleaned);
		fclose($fh);
	}
}



class HUser
{	
	private $username;
	private $password;
	private $admin;

	function __construct($n, $p, $a)
	{
		$this->username = $n;
		$this->password = $p;
		$this->admin = $a;
	}

	public function name()
	{
		return $this->username;
	}

	public function psw()
	{
		return $this->password;
	}

	public function isAdmin()
	{
		return $this->admin;
	}

	public function changeUsername($name)
	{
		if(strlen($name) > 15 || strlen($name) < 3)
			return false;

		if(!preg_match("/^[a-zA-Z0-9\s_-]+$/", $name))
			return false;

		$this->username = $name;
		return true;
	}

	public function changePassword($psw)
	{
		if(strlen($psw) > 32 || strlen($psw) < 4)
			return false;

		$pass = generatePassword($psw);

		$this->password = $pass;
		return true;
	}
}


function generatePassword($psw)
{
	return crypt($psw, base64_encode($psw));
}

function good($msg)
{
	return '<span class="good">'.$msg.'</span>';
}

function bad($msg)
{
	return '<span class="bad">'.$msg.'</span>';
}