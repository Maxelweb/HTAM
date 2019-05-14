<?php


class HTAM
{
	private $currentDir;
	
	function __construct($dir)
	{
		$this->currentDir = $dir;
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
		if(!$this->searchFor($file))
			return array();

		return @file($this->currentDir.'/'.$file);
	}

	function dir()
	{
		return $this->currentDir;
	}

	function changeDir($newdir)
	{
		$this->currentDir = $newdir;
	}

	function hasHtaccess()
	{
		return $this->searchFor(".htaccess");
	}

	function hasHtpasswd()
	{
		return $this->searchFor(".htpasswd");
	}

	function deleteHtaccess()
	{
		return @unlink($this->currentDir."/.htaccess");
	}

	function deleteHtpasswd()
	{
		return @unlink($this->currentDir."/.htpasswd");
	}

	function checkPermissions()
	{
		return is_readable($this->currentDir."/.htaccess") &&
			   is_readable($this->currentDir."/.htpasswd") &&
			   is_writable($this->currentDir."/.htaccess") &&
			   is_writable($this->currentDir."/.htpasswd");
	}

	function setPermissions()
	{
		return chmod($this->currentDir."/.htaccess", 644) &&
			   chmod($this->currentDir."/.htpasswd", 644);
	}

	function createHtfiles()
	{
		if(!$this->hasHtaccess())
		{
			$file = fopen($this->currentDir."/.htaccess", "w+");
			if(!$file)
				return false;
			fclose($file);
		}

		if(!$this->hasHtpasswd())
		{
			$file = fopen($this->currentDir."/.htpasswd", "w+");
			if(!$file)
				return false;
			fclose($file);
		}

		return true;
	}

	function icon()
	{
		return $this->isProtected() ? "&#x1F510" : ""; 
	}

	function getInfo()
	{
		$info = array("N/D", "N/D");
		$lines = $this->getFileContent(".htaccess");
		if(empty($lines))
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

		$lines = $this->getFileContent(".htaccess");
		if(empty($lines))
			return false;
		$i = 0;
		while($i<count($lines) && !preg_match("/HTAM - START/", $lines[$i]))
			$i++;

		return !($i >= count($lines));
	}

	function addProtection()
	{
		$fh = fopen($this->currentDir."/.htaccess", "w+");
		if(!$fh)
			return false;
		$prot = "# HTAM - START \n # HTAccess Manager Auto-generated script \nAuthType Basic \nAuthName \"Protected area\" \nAuthUserFile \"$this->currentDir/" . ".htpasswd" . "\"\nrequire valid-user \n# HTAM - END\n";
		fwrite($fh, $prot);
		fclose($fh);
		return true;
	}

	function removeProtection()
	{
		$lines = $this->getFileContent(".htaccess");
		if(empty($lines))
			return false;

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

		$lines = $this->getFileContent(".htpasswd");
		if(empty($lines))
			return $users;

		foreach ($lines as $line) 
		{
			$item = explode(":", $line);
			array_push($users, new HUser($item[0], $item[1], trim($item[2])));
		}

		return $users;
	}

	function countAdmin()
	{
		$u = $this->getUsers();
		$n = 0;
		if(!empty($u))
			foreach ($u as $user) 
				if($user->isAdmin())
					$n++;

		return $n;
	}

	function findUser($name)
	{
		$u = $this->getUsers();
		$i = 0;
		if(!empty($u))
			while($i<count($u))
			{
				if(($u[$i])->name() == $name)	
					break;
				$i++;
			}

		if(count($u) >= $i)
			return -1;

		return $i;
	}

	function changeUsers($users)
	{
		$checkOneAdmin = false;
		$cleaned = "";
		$i = count($users);
		foreach($users as $user)
		{
			$cleaned .= $user->name().':'.$user->psw().':'.$user->isAdmin();
			if($i-1 != 0) 
				$cleaned .= "\n";
			if(!$checkOneAdmin && $user->isAdmin()) 
				$checkOneAdmin = true;
			$i--;
		}

		if(!$checkOneAdmin && $this->isProtected())
			return false;

		
		$fh = fopen($this->currentDir."/.htpasswd", "w+");
		fwrite($fh, $cleaned);
		fclose($fh);
		return true;
	}

	function getSubDirFiles()
	{
		return array_diff(scandir($this->currentDir, 1), array('..', '.'));
	}
}



class HUser
{	
	private $username;
	private $password;
	private $admin;

	function __construct($n="none", $p="", $a=0)
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

	public function toggleAdmin($set)
	{
		$this->admin = $set ? 1 : 0;
	}
}


class HUpdate
{
	private $version;
	
	function __construct()
	{ 
		$version = VERSION;
	}

	private function checkNewVersion()
	{
		ini_set("allow_url_fopen", 1);
		$json = file_get_contents('http://api.debug.ovh/htam.json');
		if(!$json)
			return (object) array();

		return json_decode($json);
	}

	function currentVersion()
	{
		return $version;
	}

	function latestVersion()
	{
		$v = $this->checkNewVersion();
		if(!empty($v))
			return $v->version;
		return $version;
	}

	function info()
	{
		return checkNewVersion();
	}
}


function generatePassword($psw)
{
	return crypt($psw, base64_encode($psw));
}

function good($msg, $simple=0)
{
	$cl = !$simple ? "good box" : "good";
	return '<span class="'.$cl.'">'.$msg.'</span>';
}

function bad($msg, $simple=0)
{
	$cl = !$simple ? "bad box" : "bad";
	return '<span class="'.$cl.'">'.$msg.'</span>';
}
