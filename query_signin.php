<?php
include 'mysql_access.php';

// if SESSION not exists, create session
session_start();

// The query page is split from display page for easy expansion and copying.

// By default no redirecting is needed
$ajaxRedirect = "no";

// Branching error will be triggered if there are overlooked cases.
// Generally this error should not show up in final products.
$ajaxStatus = "Error in branching";

// Type indicates the nature of response
// Success: All procedures are carried out successfully
// Validation: Input fields are invalid
// Cred: Procedure is correct but user entered wrong detail
// Database: Reading and writing DB have problem
// System: The scripting of this php is faulty

$ajaxType = "System";

$passwordValid = true;
$credValid = true;

// This boolean is used to skip remaining procedures if fatal error has occured.
// The error detail is recorded in ajaxStatus.
$forceStop = false;

// "False" value is used for debug
if(!isset($_POST['cred'])||!isset($_POST['password']))
{
	// If trepassing, force return to homepage
	$ajaxRedirect = "index.html";
	$ajaxStatus = "User trepassing";
    header("Location: index.html");
}
else
{	
		$cred = $_POST['cred'];
		$password = $_POST['password'];

	// Whenever a fatal error might happen, forceStop is checked to ensure quickest execution.

	if (!$forceStop)
	{	
		if (preg_match("/^[a-zA-Z]/", $cred)===0)
		{
			$forceStop = true;
			$ajaxStatus = "N/A";
			$ajaxType = "Validation";
			$credValid = false;
		}
		if (preg_match("/^\S/", $password)===0)
		{
			$forceStop = true;
			$ajaxStatus = "N/A";
			$ajaxType = "Validation";
			$passwordValid = false;
		}
	}	
	if (!$forceStop)
	{		
		// $sql_servername, $sql_username, $sql_password are prepared in mysql_access.php
		$link = mysqli_connect($sql_servername, $sql_username, $sql_password, $sql_dbname);
		
		if (mysqli_connect_errno()) {
			$forceStop = true;
			$ajaxStatus = "Connect failed: ".mysqli_connect_error();
			$ajaxType = "Database";
		}
	}
	if (!$forceStop)
	{
		// Check if there is any user with either: (Same email to one record) or (Same username to one record)
		$query = $link->prepare("SELECT count(*), id, password FROM user WHERE username=? OR email=?");
		$query->bind_param('ss', $cred, $cred);
		
		if (!$query->execute()) {
			$forceStop = true;
			$ajaxStatus = "Error reading collection: ".mysqli_stmt_error($query);
			$ajaxType = "Database";
		}
	}
	if (!$forceStop)
	{
		$result = $query->get_result();
		$row = $result->fetch_array(MYSQLI_NUM);
		$userexist = $row[0];
		// $row[0] as the first and only value of the one-row array, indicates the number of users which has either identical username or email address.
		// Normally this variable will only yield 0 or 1, the latter means an user with same username or email has already registered.
		// Thus we can use this variable to determine if user has already registered.
		$userid = $row[1];
		$pwhash = $row[2];
		
		if ($userexist >0)
		{
			if (password_verify($password, $pwhash))
			{
				// Duplicate record(s) found
				$ajaxStatus = "Success";
				$ajaxType = "Success";
				$ajaxRedirect = "user_main.php";
				
				// Must set id first then execute query
				$_SESSION["id"] = $userid;
				
				$query = "UPDATE user SET lastlogintime = now() WHERE id =".$_SESSION['id'];
				$link->query($query);
				
				/*
				Will do automatic improvement of hash
				
				$pw_strength = [
					'options' => ['cost' => 11],
					'algo' => PASSWORD_DEFAULT,
					'hash' => null
				];
				
				if (true === password_needs_rehash($oldHash, $pw_strength['algo'], $pw_strength['options'])) {
					//rehash/store plain-text password using new hash
					$newHash = password_hash($password, $pw_strength['algo'], $pw_strength['options']);
					echo $newHash;
				}
				*/
			}
			else
			{
				// This else argument is triggered if username is correct but pw is wrong
				$ajaxStatus = "Incorrect user or password";
				$ajaxType = "Cred";
			}			
		}
		else
		{
			// This else argument is triggered if no matching user record is found
			$ajaxStatus = "Incorrect user or password";
			$ajaxType = "Cred";
		}
	}
	// This variable is fed back as JSON to the page asking for response.
	// All echo statements and printf and print_r statements must be eventually converted into part of ajaxReturn, or be commented out to allow solid and streamline performance.
	// The variable is supposed to be evolved to a JSON file at the end of development.
	/// echo $ajaxStatus."<br>";
	
	$ajaxReturn = array('status' => $ajaxStatus,
    'type' => $ajaxType,
	
    'redirect' => $ajaxRedirect,
    'credValid' => $credValid,
    'passwordValid' => $passwordValid);
	
	echo json_encode($ajaxReturn);
}
?>