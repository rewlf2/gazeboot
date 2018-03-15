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
$password2Valid = true;
$nicknameValid = true;
$emailValid = true;
$noInput = false;

$passwordChange = true;
$nicknameChange = true;
$emailChange = true;
$appealChange = true;

// This boolean is used to skip remaining procedures if fatal error has occured.
// The error detail is recorded in ajaxStatus.
$forceStop = false;

$sessionResult = "";

// "False" value is used for debug
if(!isset($_POST['password'])||!isset($_POST['password2'])||!isset($_POST['nickname'])||!isset($_POST['email'])||!isset($_POST['appeal']))
{
	// If trepassing, force return to homepage
	$ajaxRedirect = "index.html";
	$ajaxStatus = "User trepassing";
    // header("Location: index.html");
}
else
{	
	$password = $_POST['password'];
	$password2 = $_POST['password2'];
	$nickname = $_POST['nickname'];
	$email = $_POST['email'];
	$appeal = $_POST['appeal'];
	$sesslife = $_POST['sesslife'];
	
	// echo $password."<br>".$password2."<br>".$nickname."<br>".$email."<br>".$appeal."<br>".$sesslife."<br>";
	// Firstly, check which field is entered
	
	if (strcmp($password, "")==0&&strcmp($password2, "")==0)
		$passwordChange = false;
	if (strcmp($nickname, "")==0)
		$nicknameChange = false;
	if (strcmp($email, "")==0)
		$emailChange = false;
	if (strcmp($appeal, "")==0)
		$appealChange = false;
	
	// Whenever a fatal error might happen, forceStop is checked to ensure quickest execution.

	// Since the input range for session lifetime is always active, we can assume even if we don't change anything the session life is still "updated".
	// So this no-change test can be skipped.
	/*
	if (!$passwordChange && !$nicknameChange && !$emailChange && !$appealChange)
	{	
		// Nothing is inputted
		$forceStop = true;
		$ajaxStatus = "";
		$ajaxType = "Validation";
		$noInput = true;
	}
	*/
	if (!$forceStop)
	{	
		// PHP regex needs delimiters, identical sign is used to check output
		if (preg_match("/^\S{6,20}$/", $password)===0 && $passwordChange)
		{
			// Password invalid
			$forceStop = true;
			$ajaxStatus = "";
			$ajaxType = "Validation";
			$passwordValid = false;
		}
		if (strcmp($password, $password2)!==0 && $passwordChange)
		{
			// Passwords don't match
			$forceStop = true;
			$ajaxStatus = "";
			$ajaxType = "Validation";
			$password2Valid = false;
		}
		if (preg_match("/^[a-zA-Z][\w_\- ]{2,19}/", $nickname)===0 && $nicknameChange)
		{
			$forceStop = true;
			$ajaxStatus = "";
			$ajaxType = "Validation";
			$nicknameValid = false;
		}
		// First character must be letter, then any number of letter/number/underscore/hyphen/dot, then @ sign, then at least 1 letter/number/underscore/hyphen/dot, then dot, finally 2-3 letters.
		if (preg_match("/^[a-zA-Z]{1,}[_\-a-zA-Z0-9\.]{0,}@[_\-a-zA-Z0-9\.]{1,}\.[a-zA-Z]{2,3}$/", $email)===0 && $emailChange)
		{
			$forceStop = true;
			$ajaxStatus = "";
			$ajaxType = "Validation";
			$emailValid = false;
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
		// Determine which record to update depending on "Change" booleans
		// Separate each update of column of update to separate query
		
		if ($passwordChange){
			$pwhash = password_hash($password, PASSWORD_DEFAULT);
			$query = $link->prepare("UPDATE user SET password=? WHERE id=".$_SESSION['id']);
			$query->bind_param('s', $pwhash);
			
			if (!$query->execute()) {
				$forceStop = true;
				$ajaxStatus = "Error writing collection: ".mysqli_stmt_error($query);
				$ajaxType = "Database";
			}
			$query->reset();
		}
		if ($nicknameChange){
			$query = $link->prepare("UPDATE user SET nickname=? WHERE id=".$_SESSION['id']);
			$query->bind_param('s', $nickname);
			
			if (!$query->execute()) {
				$forceStop = true;
				$ajaxStatus = "Error writing collection: ".mysqli_stmt_error($query);
				$ajaxType = "Database";
			}
			$query->reset();
		}
		if ($emailChange){
			$query = $link->prepare("UPDATE user SET email=? WHERE id=".$_SESSION['id']);
			$query->bind_param('s', $email);
			
			if (!$query->execute()) {
				$forceStop = true;
				$ajaxStatus = "Error writing collection: ".mysqli_stmt_error($query);
				$ajaxType = "Database";
			}
			$query->reset();
		}
		if ($appealChange){
			$query = $link->prepare("UPDATE user SET password=? WHERE id=".$_SESSION['id']);
			$query->bind_param('s', $appeal);
			
			if (!$query->execute()) {
				$forceStop = true;
				$ajaxStatus = "Error writing collection: ".mysqli_stmt_error($query);
				$ajaxType = "Database";
			}
			$query->reset();
		}
		// Session lifetime will always be updated, it's better to leave it alone then finding if value is changed, because range always yields safe valid input,
		// And finding current value can instead yield more SQL queries.
			$query = $link->prepare("UPDATE user SET sessionlife=? WHERE id=".$_SESSION['id']);
			$query->bind_param('s', $sesslife);
			
			if (!$query->execute()) {
				$forceStop = true;
				$ajaxStatus = "Error writing collection: ".mysqli_stmt_error($query);
				$ajaxType = "Database";
			}
			$query->reset();
		
		if (!$forceStop)
		{
			$ajaxStatus = "Success";
			$ajaxType = "Success";
			$ajaxRedirect = "user_setting.php";
		}
	}
	// This variable is fed back as JSON to the page asking for response.
	// All echo statements and printf and print_r statements must be eventually converted into part of ajaxReturn, or be commented out to allow solid and streamline performance.
	// The variable is supposed to be evolved to a JSON file at the end of development.
	/// echo $ajaxStatus."<br>";
	
	$ajaxReturn = array('status' => $ajaxStatus,
    'type' => $ajaxType,
    'redirect' => $ajaxRedirect,
	
    'passwordValid' => $passwordValid,
    'password2Valid' => $password2Valid,
    'emailValid' => $emailValid,
    'nicknameValid' => $nicknameValid,
	'noInput' => $noInput,
	
	'sessionResult' => $sessionResult);
	
	echo json_encode($ajaxReturn);
}
?>