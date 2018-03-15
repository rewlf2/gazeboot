<?php
	// Grants access to database after checking if session is valid
	include 'session_access.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>GazeStore</title>

    <!-- Bootstrap core CSS -->
    <link href="dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/cover.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="assets/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	
		<!-- Will check which parameter is changed and change user settings according to input -->
		<!-- Also has check function if password is changed -->
		
	    <script type='text/javascript' src='scripts/jquery-3.2.0.js'></script>
        <script src="scripts/jquery-3.2.0.js"></script>

        <script type="text/javascript" language="javascript">
        var xmlHttp;
		
		function submitChange() {
			sbutton.disabled = true;
			rbutton.disabled = true;
			
			try{
				xmlHttp = new XMLHttpRequest();
				} catch(e){
				try{
				xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
				} catch(e){
				try{
				xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
				} catch(e){
				alert("Error!");
				return;
				}
				}
			}
			xmlHttp.onreadystatechange = function() {
				if(xmlHttp.readyState == 4){
					error.innerHTML = xmlHttp.responseText;
					var duce = jQuery.parseJSON(xmlHttp.responseText);
					var statusText = duce.status;
					var redirectText = duce.redirect;
					var typeText = duce.type;
					
					if (typeText == "Success")
					{
						// Runs when form is correct and data is successfully inserted
						// This page should redirect users if inserting is successful
						passwordError.innerHTML = "";
						password2Error.innerHTML = "";
						emailError.innerHTML = "";
						nicknameError.innerHTML = "";
						error.innerHTML = "Change succcessful!";
						
						inputPassword.disabled = true;
						inputPassword2.disabled = true;
						inputNickname.disabled = true;
						inputEmail.disabled = true;
						inputAppeal.disabled = true;
					}
					else if (typeText == "Validation")
					{
						// Runs when form is invalid
						var passwordValid = duce.passwordValid;
						var password2Valid = duce.password2Valid;
						var emailValid = duce.emailValid;
						var nicknameValid = duce.nicknameValid;
						var noInput = duce.noInput;
							
						if (!passwordValid)
							passwordError.innerHTML = "<br>6-20 characters long";
						else
							passwordError.innerHTML = "";
							
						if (!password2Valid)
							password2Error.innerHTML = "<br>Passwords don't match";
						else
							password2Error.innerHTML = "";
						
						if (!emailValid)
							emailError.innerHTML = "<br>Invalid";
						else
							emailError.innerHTML = "";
							
						if (!nicknameValid)
							nicknameError.innerHTML = "<br>3-20 letter, number, underscores, hyphens or whitespaces<br>Must start with letter";
						else
							nicknameError.innerHTML = "";
						
						if (noInput)
							error.innerHTML = "Nothing to change";
						else
							error.innerHTML = "";
					}
					else if (typeText == "Database")
					{
						// Runs when database operation has error
						passwordError.innerHTML = "";
						password2Error.innerHTML = "";
						emailError.innerHTML = "";
						nicknameError.innerHTML = "";
						
						error.innerHTML = statusText;
					}
					else if (typeText == "System")
					{
						// Runs when systematic error has occured
						passwordError.innerHTML = "";
						password2Error.innerHTML = "";
						emailError.innerHTML = "";
						nicknameError.innerHTML = "";
						
						error.innerHTML = statusText;
					}
					// Validation variables
					// An error text can be used for real-timefeedback to users and for debugging
					// error.innerHTML = xmlHttp.responseText;
					if (redirectText != "no")
					{
						 window.setTimeout(window.location = redirectText,3000);
					}
				}
			}
			xmlHttp.open("POST", "query_setting.php", true);
			xmlHttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			xmlHttp.send("password=" + change.inputPassword.value + "&password2=" + change.inputPassword2.value + "&nickname=" + change.inputNickname.value + "&email=" + change.inputEmail.value + "&appeal=" + change.inputAppeal.value + "&sesslife=" + change.inputSess.value);
			sbutton.disabled = false;
			rbutton.disabled = false;
		}
		function sessionLifeChg()
		{
			var v = change.inputSess.value;
			var str = (v/60).toString().concat(" minutes");
			
			sessViewer.innerHTML = str;
		}
		function sessionLifeRst()
		{
			var v = <?php echo getInfo(getUserId(), "user", "sessionlife"); ?>;
			var str = (v/60).toString().concat(" minutes");
			
			sessViewer.innerHTML = str;
		}
		</script>
	
  </head>

  <body>

    <div class="site-wrapper">
      <div class="site-wrapper-inner">
        <div class="cover-container">

          <div class="masthead clearfix">
            <div class="inner">
              <h3 class="masthead-brand">GazeStore<small><?php 
				echo getInfo(getUserId(), "user", "nickname")
			  ?></small></h3>
              <nav>
                <ul class="nav masthead-nav">
                  <li><a href="user_main.php">Dashboard</a></li>
                  <li><a href="#">TBA</a></li>
                  <li><a href="#">TBA</a></li>
                  <li class="active"><a href="#">Setting</a></li>
                  <li><a href="signoff.php">Sign off</a></li>
                </ul>
              </nav>
            </div>
          </div>

			<div class="panel panel-default">
			  <!-- Default panel contents -->
			  <div class="panel-body">
				<h3><p style="color:black">Your account settings</p></h3>
			  </div>
			  <!-- Table -->
			  <form id="change">
			  <table class="table" style="color:#404040">
				  <tr>
					<th width="16%" style="vertical-align:middle">Unique id</th>
					<td width="42%" style="vertical-align:middle"><?php echo getUserId(); ?></td>
					<td width="42%" style="vertical-align:middle"></td>
				  </tr>
				  <tr>
					<th width="16%" style="vertical-align:middle">User name</th>
					<td width="42%" style="vertical-align:middle"><?php echo getInfo(getUserId(), "user", "username"); ?></td>
					<td width="42%" style="vertical-align:middle"></td>
				  </tr>
				  <tr>
					<th width="16%" style="vertical-align:middle">Password</th>
					<td width="42%" style="vertical-align:middle">(Undisclosed)</td>
					<td width="42%" style="vertical-align:middle"><input type="password" class="form-control" placeholder="Change?" name="password" id="inputPassword">
					<h5><div class="error" name="error" id="passwordError" style="font-size: 12px; color:#900000;"></div></h5></td>
				  </tr>
				  <tr>
					<th width="16%" style="vertical-align:middle">Confirm password</th>
					<td width="42%" style="vertical-align:middle"></td>
					<td width="42%" style="vertical-align:middle"><input type="password" class="form-control" placeholder="Repeat" name="password2" id="inputPassword2">
					<h5><div class="error" name="error" id="password2Error" style="font-size: 12px; color:#900000;"></div></h5></td>
				  </tr>
				  <tr>
					<th width="16%" style="vertical-align:middle">Nickname</th>
					<td width="42%" style="vertical-align:middle"><?php echo getInfo(getUserId(), "user", "nickname"); ?></td>
					<td width="42%" style="vertical-align:middle"><input type="text" class="form-control" placeholder="Change?" name="nickname" id="inputNickname">
					<h5><div class="error" name="error" id="nicknameError" style="font-size: 12px; color:#900000;"></div></h5></td>
				  </tr>
				  <tr>
					<th width="16%" style="vertical-align:middle">Email</th>
					<td width="42%" style="vertical-align:middle"><?php echo getInfo(getUserId(), "user", "email"); ?></td>
					<td width="42%" style="vertical-align:middle"><input type="email" class="form-control" placeholder="Change?" name="email" id="inputEmail">
					<h5><div class="error" name="error" id="emailError" style="font-size: 12px; color:#900000;"></div></h5></td>
				  </tr>
				  <tr>
					<th width="16%" style="vertical-align:middle">Join Date</th>
					<td width="42%" style="vertical-align:middle"><?php echo getInfo(getUserId(), "user", "joindate"); ?></td>
					<td width="42%" style="vertical-align:middle"></td>
				  </tr>
				  <tr>
					<th width="16%" style="vertical-align:middle">Permission</th>
					<td width="42%" style="vertical-align:middle"><?php echo getSelfPermission(); ?></td>
					<td width="42%" style="vertical-align:middle;text-align: left"><a href="user_permission.php"><div style="color:#0000C0;">Manage (leaves this page)</div></a></td>
				  </tr>
				  <tr>
					<th width="16%" style="vertical-align:middle">Warn Level</th>
					<td width="42%" style="vertical-align:middle"><?php echo getSelfWarn(); ?></td>
					<td width="42%" style="vertical-align:middle"><input type="email" class="form-control"  name="appealmsg" id="inputAppeal" 
					<?php
						// Only if a user is warned and admin allows an appeal, will this field be enabled
						if (getInfo(getUserId(), "user", "appealable")==0)
							echo "disabled";
						else
							echo 'placeholder="Enter appeal message here"';
					?>
					</td>
				  </tr>
				  <tr>
					<th width="16%" style="vertical-align:middle">Session Lifetime</th>
					<td width="42%" style="vertical-align:middle"><div id="sessViewer"><?php echo (getInfo(getUserId(), "user", "sessionlife")/60)." minutes"; ?></div></td>
					<td width="42%" style="vertical-align:middle;text-align: left"><input type="range" class="form-control" name="sess" id="inputSess"
						value="<?php echo getInfo(getUserId(), "user", "sessionlife"); ?>" min="900" max="10800" step="900" onChange="sessionLifeChg()"></td>
				  </tr>
			  </table>
				<h5><div class="error" name="error" id="error" style="font-size: 12px; color:#900000;"></div></h5>
				<div class="btn-group btn-block" role="group">
					<button type="button" class="btn btn-primary" style="width:50%" id="sbutton" onclick="submitChange()">Submit</button>
					<button type="reset" class="btn btn-primary" style="width:50%" id="rbutton" onClick="sessionLifeRst()">Reset</button>
				</div>
			  </form>
			</div>	
		  
          <div class="mastfoot">
            <div class="inner">
              <p>Cover template for <a href="http://getbootstrap.com">Bootstrap</a>, by <a href="https://twitter.com/mdo">@mdo</a>.</p>
            </div>
          </div>

        </div>

      </div>

    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="../../dist/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
