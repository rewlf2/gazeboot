<?php

// if SESSION not exists, create session
// Will run regardless of if you access variables in session
if(!isset($_SESSION))
	session_start();

// This php will force session to be destroyed whenever it finds the credital details to be corrupted
// (To be implemented)
// If session is not valid (including not existing) this php will also force return to index.html

// Will add trepass tracking
if (!isset($_SESSION['id']))
	header("Location: signoff_expired.php");
else
{
	// Gain access to database
	include 'mysql_access.php';
	
	// Writes current date to last login date of current account
	// This is an example of passive monitoring the activities of user
	
	$link = mysqli_connect($sql_servername, $sql_username, $sql_password, $sql_dbname);
	if (mysqli_connect_errno()) {
		echo "Connect failed: ".mysqli_connect_error();
	}
	
	// I decided to use MYSQL clock for checking session expiration	
	$result = $link->query("SELECT now(), lastlogintime, sessionlife FROM user where id=".getUserId());	
	$arr = $result->fetch_array(MYSQLI_NUM);
	$timenow = strtotime($arr[0]);
	$timelast = strtotime($arr[1]);
	$sessionlife = $arr[2];
	$timediff = $timenow-$timelast;
	
	// If we parse timestamps in MySQL into php variables, we cannot echo them,
	// We can instead echo them after math.calculation.
	// echo $timediff."<br>".$sessionlife."<br>";
	
	if ($timediff>$sessionlife)
	{
		// Forces a signoff by expiration
		header("Location: signoff_expired.php");
		
		//echo $timediff."<br>".$sessionlife;
	}
	else
	{
		$query = "UPDATE user SET lastlogintime = now() WHERE id =".$_SESSION['id'];
		// echo $query."<br>";
		
		if ($link->query($query) === FALSE)
			echo "Login monitor has failed!";
	}
	$result->free();
}


function getUserId()
{
	return $_SESSION['id'];
}

function getInfo($id, $table, $parameter)
{
	// Gain access to database
	include 'mysql_access.php';
	
	// $sql_servername, $sql_username, $sql_password, $sql_dbname are prepared in mysql_access.php
	$link = mysqli_connect($sql_servername, $sql_username, $sql_password, $sql_dbname);
	if (mysqli_connect_errno()) {
		$forceStop = true;
		echo "Connect failed: ".mysqli_connect_error();
	}
	
	// Since mysqli_prepare disallows putting identifier into the SQL statement,
	// We use the $table variable to select the string statement to be prepared instead.
	// This should still be injection-safe.
		
	// Also it is strictly prohibited to concatenate the prepared string to prevent SQL injection.
		
	if (strcmp($table,"user")==0)
		$prepared = "SELECT * FROM user WHERE id=?";
	else
		$prepared = "SELECT * FROM user WHERE id=?";
	
	// The default table to be used is "user"
	// echo $prepared."<br>";
		
	$query = $link->prepare($prepared);
	$query->bind_param('i', $id);
		
	if (!$query->execute()) {
		$forceStop = true;
		echo "Error reading collection: ".mysqli_stmt_error($query);
	}
		
	$result = $query->get_result();
	$row = $result->fetch_array(MYSQLI_ASSOC);
	//var_dump($row);
	return $row[$parameter];
	// $Parameter cannot be used as marker in the prepared SQL query,
	//  but we can use Select * to get all items first, then use $Parameter to filter the result. 	
}

function getPermission($id)
{
	$permission = getInfo($id, "user", "permission");
	
	switch ($permission) {
    case 0:
        return "Normal user";
        break;
    case 1:
        return "Adminstrator";
        break;
	default:
		return "Permission not defined!";
	}
}
function getSelfPermission()
{
	return getPermission($_SESSION['id']);
}

function getWarn($id)
{
	$permission = getInfo($id, "user", "warnlevel");
	
	switch ($permission) {
    case 0:
        return "Safe";
        break;
    case 1:
        return "Warning";
        break;
    case 2:
        return "Muted";
        break;
    case 3:
        return "Banned";
        break;
	default:
		return "Rank not defined!";
	}
}
function getSelfWarn()
{
	return getWarn($_SESSION['id']);
}


// Shield functions:

function report($type, $descr)
{
	// Will automatically report name of calling page and attacker IP
	$caller = basename(__FILE__);
	$ip = getUserIP();
	
		$query = $link->prepare("INSERT INTO shield (date, type, nickname, email, emailverfied, banned, bantime, lastlogintime, status, joindate) VALUES (?, ?, ?, ?, '0', '0', '0000-00-00 00:00:00.000000', '0000-00-00 00:00:00.000000', 'ready', CURRENT_DATE())");
	
}

function getUserIP() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

// Report invalid attempts of signin
?>