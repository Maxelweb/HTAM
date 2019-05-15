<?php


class HTAM
{
	private $currentDir;
	private $htaFile;
	private $htpFile;
	
	function __construct($dir)
	{
		$this->currentDir = $dir;
		$this->htaFile = ".htaccess";
		$this->htpFile = ".htpasswd";
	}

	private function searchFor($file)
	{
		return file_exists($this->currentDir.DIRECTORY_SEPARATOR.$file);
	}

	private function getInside($string)
	{
	    $pattern = "/\"(.*?)\"/";
	    preg_match_all($pattern, $string, $matches);
	    if(!empty($matches[1][0]))
	        return $matches[1][0];
	    return "";
	}

	private function getFileContent($file)
	{
		if(!$this->searchFor($file))
			return array();
		return @file($this->currentDir.DIRECTORY_SEPARATOR.$file);
	}

	function hta()
	{
		return $this->currentDir.DIRECTORY_SEPARATOR.$this->htaFile;
	}

	function htp()
	{
		return $this->currentDir.DIRECTORY_SEPARATOR.$this->htpFile;
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
		return $this->searchFor($this->htaFile);
	}

	function hasHtpasswd()
	{
		return $this->searchFor($this->htpFile);
	}

	function deleteHtaccess()
	{
		return unlink($this->hta());
	}

	function deleteHtpasswd()
	{
		return @unlink($this->htp());
	}

	function checkPermissions()
	{
		return is_readable($this->hta()) &&
			   is_readable($this->htp()) &&
			   is_writable($this->hta()) &&
			   is_writable($this->htp());
	}

	function setPermissions()
	{
		return chmod($this->hta(), 644) &&
			   chmod($this->htp(), 644);
	}

	function createHtfiles()
	{
		if(!$this->hasHtaccess())
		{
			$file = fopen($this->hta(), "w+");
			if(!$file)
				return false;
			fclose($file);
		}

		if(!$this->hasHtpasswd())
		{
			$file = fopen($this->htp(), "w+");
			if(!$file)
				return false;
			fclose($file);
		}

		return true;
	}

	function icon()
	{
		return $this->isProtected() ? "&#x1F512" : ""; 
	}

	function getInfo()
	{
		$info = array("N/D", "N/D");
		$lines = $this->getFileContent($this->htaFile);
		if(empty($lines))
			return $info;

		foreach ($lines as $line) {
			if(preg_match("/AuthName/", $line))
				$info[0] = $this->getInside($line);
			elseif(preg_match("/AuthUserFile/", $line))
				$info[1] = $this->getInside($line);
		}

		return $info;
	}

	function isProtected()
	{
		if(!$this->hasHtaccess())
			return false;

		$lines = $this->getFileContent($this->htaFile);
		if(empty($lines))
			return false;
		$i = 0;
		while($i<count($lines) && !preg_match("/HTAM - START/", $lines[$i]))
			$i++;

		return !($i >= count($lines));
	}

	function addProtection()
	{
		if($this->isProtected())
			return false;

		$fh = fopen($this->hta(), "a+");
		if(!$fh)
			return false;
		$prot = "\n# HTAM - START \n# HTAccess Manager auto-generated script \nAuthType Basic \nAuthName \"Protected area\" \nAuthUserFile \"" . $this->htp() . "\"\nrequire valid-user \n# HTAM - END\n";
		fwrite($fh, $prot);
		fclose($fh);
		return true;
	}

	function removeProtection()
	{
		$lines = $this->getFileContent($this->htaFile);
		if(empty($lines))
			return false;

		$i = 0;
		while($i<count($lines) && !preg_match("/HTAM - START/", $lines[$i]))
			$i++;

		if($i >= count($lines))
			return false;

		if($lines[$i-1] == "\n")
			unset($lines[$i-1]);

		for($j=$i; $j < $i+7; $j++)
			unset($lines[$j]);
		
		$cleaned = "";
		foreach($lines as $line)
			$cleaned .= $line;
		
		$fh = fopen($this->hta(), "w+");
		fwrite($fh, $cleaned);
		fclose($fh);

		return true; 
	}

	function getUsers()
	{
		$users = array();
		if(!$this->hasHtpasswd())
			return $users;

		$lines = $this->getFileContent($this->htpFile);
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

		if($i >= count($u))
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

		
		$fh = fopen($this->htp(), "w+");
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


class HUpdates
{
	private $version;
	private $payload;
	
	function __construct()
	{ 
		$this->version = VERSION;
		$this->payload = $this->checkNewVersion();
	}

	private function checkNewVersion()
	{
		$json = file_get_contents('http://api.debug.ovh/htam.json');
		if(!$json)
			return array();

		return json_decode($json, 1);
	}

	function currentVersion()
	{
		return $this->version;
	}

	function latestVersion()
	{
	 	if(!empty($this->payload))
	 		return ($this->payload)['version'];
	 	return "N/A";
	}

	function info()
	{
		return $this->payload;
	}
}


function generatePassword($psw)
{
	//return crypt($psw, base64_encode($psw));
	return password_hash($psw, PASSWORD_BCRYPT);
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
