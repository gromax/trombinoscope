<?php
	if(file_exists('./config.php')) include './config.php';
	else include './php/config.php';
	session_start();

	if(!isset($_SESSION['last_access']) || !isset($_SESSION['ipaddr']) || !isset($_SESSION['IDtrombi'])) $session_on=false;
	elseif ((time()-$_SESSION['last_access']>TIME_OUT) || ($_SERVER['REMOTE_ADDR']!=$_SESSION['ipaddr'])||isset($_GET['deco'])) {
		if(file_exists('./logout.php')) include './logout.php';
		else include './php/logout.php';
		define("RANK",0);
	} else 	{
		$_SESSION['last_access']=time();
		define("RANK",$_SESSION['RANKtrombi']);
	}

	function author($action,$params){
		if (isset($_SESSION['mySugs'])) $mySugs=$_SESSION['mySugs']; else $mySugs=array();
		switch($action){
			case "delEvent" :
				if (RANK>=RANG_ADMIN) return true;
				break;
			case "modEvent" :
				if (RANK>=RANG_ADMIN) return true;
				break;
			case "validNewPerson" :
				if (RANK>=RANG_ADMIN) return true;
				break;
			case "addLink" :
				if ( (RANK>=RANG_ADMIN) || ( (RANK==RANG_ANONYME_CONTRIBUTOR) && isset($params['IDP']) && isset($mySugs[$params['IDP']]) ) ) return true;
				break;
			case "removeLink" :
				if ( (RANK>=RANG_ADMIN) || ( (RANK==RANG_ANONYME_CONTRIBUTOR) && isset($params['IDP']) && isset($mySugs[$params['IDP']]) ) ) return true;
				break;
			case "addNewPerson" :
				if ((RANK>=RANG_ADMIN)||(RANK==RANG_ANONYME_CONTRIBUTOR)) return true;
				break;
			case "delPerson" :
				if ( (RANK>=RANG_ADMIN) || ( (RANK==RANG_ANONYME_CONTRIBUTOR) && isset($params['ID']) && isset($mySugs[$params['ID']]) ) ) return true;
				break;
			case "modPerson" :
				if ( (RANK>=RANG_ADMIN) || ( (RANK==RANG_ANONYME_CONTRIBUTOR) && isset($params['ID']) && isset($mySugs[$params['ID']]) ) ) return true;
				break;
			case "modMyAccount" :
				if ((RANK>=RANG_ADMIN)||(RANK==RANG_USER)) return true;
				break;
			case "personsWidthPhoto" :
				if (RANK>0) return true;
				break;
			case "getUsersList" :
				if (RANK>RANG_ADMIN) return true;
				break;
			case "addModUser" :
				if (RANK>RANG_ADMIN) return true;
				break;
			case "delUser" :
				if (RANK>RANG_ADMIN) return true;
				break;
		}
		return false;
	}

?>
