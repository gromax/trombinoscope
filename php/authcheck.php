<?php
	if(file_exists('./config.php')) include './config.php';
	else include './php/config.php';
	session_start();

	if(!isset($_SESSION['last_access']) || !isset($_SESSION['ipaddr']) || !isset($_SESSION['IDtrombi'])) $session_on=false;
	elseif ((time()-$_SESSION['last_access']>TIME_OUT) || ($_SERVER['REMOTE_ADDR']!=$_SESSION['ipaddr'])||isset($_GET['deco'])) {
		if(file_exists('./logout.php')) include './logout.php';
		else include './php/logout.php';
	} else 	{
		$_SESSION['last_access']=time();
	}

	function author($action,$params){
		if (isset($_SESSION['RANKtrombi'])) $rank=$_SESSION['RANKtrombi']; else $rank=0;
		if (isset($_SESSION['mySugs'])) $mySugs=$_SESSION['mySugs']; else $mySugs=array();
		switch($action){
			case "delEvent" :
				if ($rank>=RANG_ADMIN) return true;
				break;
			case "modEvent" :
				if ($rank>=RANG_ADMIN) return true;
				break;
			case "validNewPerson" :
				if ($rank>=RANG_ADMIN) return true;
				break;
			case "addLink" :
				if ($rank>=RANG_ADMIN) return true;
				break;
			case "removeLink" :
				if ($rank>=RANG_ADMIN) return true;
				break;
			case "addNewPerson" :
				if (($rank>=RANG_ADMIN)||($rank==RANG_ANONYME_CONTRIBUTOR)) return true;
				break;
			case "delPerson" :
				if ( ($rank>=RANG_ADMIN) || ( ($rank==RANG_ANONYME_CONTRIBUTOR) && isset($params['ID']) && isset($mySugs[$params['ID']]) ) ) return true;
				break;
			case "modPerson" :
				if ( ($rank>=RANG_ADMIN) || ( ($rank==RANG_ANONYME_CONTRIBUTOR) && isset($params['ID']) && isset($mySugs[$params['ID']]) ) ) return true;
				break;
			case "modMyAccount" :
				if (($rank>=RANG_ADMIN)||($rank==RANG_USER)) return true;
				break;
		}
		return false;
	}

?>
