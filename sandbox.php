<?php
	// Grants access to database after checking if session is valid
	include 'session_access.php';

	$link = mysqli_connect($sql_servername, $sql_username, $sql_password, $sql_dbname);
	if (mysqli_connect_errno()) {
		$forceStop = true;
		echo "Connect failed: ".mysqli_connect_error();
	}
	
	// I decided to use MYSQL clock for checking session expiration	
	$result = $link->query("SELECT now()");
	
	$timenow = strtotime($result->fetch_array(MYSQLI_NUM)[0]);
	echo $timenow."<br>";
	
	echo strtotime(fetch(getUserId(), "user", "lastlogintime"))."<br>";
	echo "Time since last accessing of login pages: ".($timenow-strtotime(fetch(getUserId(), "user", "lastlogintime")));

?>