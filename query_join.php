<?php
include 'mysql_access.php';

// The query page is split from display page for easy expansion and copying.

// By default no redirecting is needed
$ajaxRedirect = "no";

// Branching error will be triggered if there are overlooked cases.
// Generally this error should not show up in final products.
$ajaxStatus = "Error in branching";

// Type indicates the nature of response
// Success: All procedures are carried out successfully
// Validation: Input fields are invalid
// Database: Reading and writing DB have problem
// System: The scripting of this php is faulty

$ajaxType = "System";

$emailValid = true;
$usernameValid = true;
$passwordValid = true;
$nicknameValid = true;

// This boolean is used to skip remaining procedures if fatal error has occured.
// The error detail is recorded in ajaxStatus.
$forceStop = false;

// "False" value is used for debug
if(!isset($_POST['email'])||!isset($_POST['username'])||!isset($_POST['password'])||!isset($_POST['nickname']))
{
	// If trepassing, force return to homepage
	$ajaxRedirect = "index.html";
	$ajaxStatus = "User trepassing";
    header("Location: index.html");
}
else
{	
		$joindate = date("Y-m-d");
		$email = $_POST['email'];
		$username = $_POST['username'];
		$password = $_POST['password'];
		$nickname = $_POST['nickname'];

	// Whenever a fatal error might happen, forceStop is checked to ensure quickest execution.	
	if (!$forceStop)
	{
		// PHP regex needs delimiters, identical sign is used to check output
		// First character must be letter, then any number of letter/number/underscore/hyphen/dot, then @ sign, then at least 1 letter/number/underscore/hyphen/dot, then dot, finally 2-3 letters.
		if (preg_match("/^[a-zA-Z]{1,}[_\-a-zA-Z0-9\.]{0,}@[_\-a-zA-Z0-9\.]{1,}\.[a-zA-Z]{2,3}$/", $email)===0)
		{
			$forceStop = true;
			$ajaxStatus = "";
			$ajaxType = "Validation";
			$emailValid = false;
		}
		if (preg_match("/^[a-zA-Z]\w{5,19}$/", $username)===0)
		{
			$forceStop = true;
			$ajaxStatus = "";
			$ajaxType = "Validation";
			$usernameValid = false;
		}
		if (preg_match("/^\S{6,20}$/", $password)===0)
		{
			$forceStop = true;
			$ajaxStatus = "";
			$ajaxType = "Validation";
			$passwordValid = false;
		}
		/*
		if (strcmp($password, $password2)!=0)
		{
			$forceStop = true;
			$ajaxStatus = "";
			$ajaxType = "Validation";
			$password2Valid = false;
		}
		*/
		if (preg_match("/^[a-zA-Z][\w_\- ]{2,19}/", $nickname)===0)
		{
			$forceStop = true;
			$ajaxStatus = "";
			$ajaxType = "Validation";
			$nicknameValid = false;
		}
	}
	if (!$forceStop)
	{		
		// $sql_servername, $sql_username, $sql_password, $sql_dbname are prepared in mysql_access.php
		$link = mysqli_connect($sql_servername, $sql_username, $sql_password, $sql_dbname);
		
		if (mysqli_connect_errno()) {
			$forceStop = true;
			$ajaxStatus = "Connect failed: ".mysqli_connect_error();
			$ajaxType = "Database";
		}
	}
	if (!$forceStop)
	{
		// Check if there is any user with username existing	
		$query = $link->prepare("SELECT count(*) FROM user WHERE username=?");
		$query->bind_param('s', $username);
		
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
		$userDup = $row[0];
		// $row[0] as the first and only value of the one-row array, indicates the number of users which has either identical username or email address.
		// Normally this variable will only yield 0 or 1, the latter means an user with same username or email has already registered.
		// Thus we can use this variable to determine if user has already registered.

		if ($userDup >0)
		{
			// Duplicate record(s) found
			$forceStop = true;
			$ajaxStatus = "Username used";
			$ajaxType = "Database";
		}
	}
	if (!$forceStop)
	{
		$query->reset();
		// Check if there is any user with identical email existing	
		$query = $link->prepare("SELECT count(*) FROM user WHERE email=?");
		$query->bind_param('s', $email);
		
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
		$emailDup = $row[0];
		// Similar to what userDup does

		if ($emailDup >0)
		{
			// Duplicate record(s) found
			$forceStop = true;
			$ajaxStatus = "Email used";
			$ajaxType = "Database";
		}
	}
	if (!$forceStop)
	{
		$query->reset();
		
		// Generates a password hash
		
		$pwhash = password_hash($password, PASSWORD_DEFAULT);
		// Insert a new user record if no duplicate record is found
		$query = $link->prepare("INSERT INTO user (username, password, nickname, email, emailverfied, banned, bantime, lastlogintime, status, joindate) VALUES (?, ?, ?, ?, '0', '0', '0000-00-00 00:00:00.000000', '0000-00-00 00:00:00.000000', 'ready', CURRENT_DATE())");
		$query->bind_param('ssss', $username, $pwhash, $nickname, $email);
		
		if (!$query->execute()) {
			$forceStop = true;
			$ajaxStatus = "Error writing collection: ".mysqli_stmt_error($query);
			$ajaxType = "Database";
		} // Else writing successful, continue
		else
		{
			$ajaxStatus = "Success";
			$ajaxType = "Success";
			$ajaxRedirect = "join_success.html";
		}
	}
	// This variable is fed back as JSON to the page asking for response.
	// All echo statements and printf and print_r statements must be eventually converted into part of ajaxReturn, or be commented out to allow solid and streamline performance.
	// The variable is supposed to be evolved to a JSON file at the end of development.
	/// echo $ajaxStatus."<br>";
	
	$ajaxReturn = array('status' => $ajaxStatus,
    'type' => $ajaxType,
	
    'redirect' => $ajaxRedirect,
    'emailValid' => $emailValid,
    'usernameValid' => $usernameValid,
    'passwordValid' => $passwordValid,
    'nicknameValid' => $nicknameValid);
	
	echo json_encode($ajaxReturn);
}
?>