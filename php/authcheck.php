<?php
	$session_timeout=5000;
	session_start();

	if(!isset($_SESSION['last_access']) || !isset($_SESSION['ipaddr']) || !isset($_SESSION['IDtrombi'])) $session_on=false;
	elseif ((time()-$_SESSION['last_access']>$session_timeout) || ($_SERVER['REMOTE_ADDR']!=$_SESSION['ipaddr'])||isset($_GET['deco'])) {
		include './logout.php';
	} else 	{
		$_SESSION['last_access']=time();
	}

?>
