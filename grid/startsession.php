<?php
	session_start();
	unset($_SESSION['pass']);
	unset($_SESSION['pid']);
	$pass = (isset($_REQUEST['Password-ID']) ? $_REQUEST['Password-ID'] : "" );
	$pid = (isset($_REQUEST['Process-ID']) ? $_REQUEST['Process-ID'] : "" );
	$pidArray = [1,2,3,4,5,6];
	if (($pass != "") and ($pid != "") and ($pass == "AfekaGK") ) {
		$_SESSION['pid'] = $pid;
		header ('location: main.php');
		die();
	} else {
		unset($_SESSION['pass']);
		unset($_SESSION['pid']);
		header ('location: start.php');
		die();
	}
?>