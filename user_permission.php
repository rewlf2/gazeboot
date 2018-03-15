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
	
		<!-- On loading page, wil redirect user automatically after showing info -->
		
	    <script type='text/javascript' src='scripts/jquery-3.2.0.js'></script>
        <script src="scripts/jquery-3.2.0.js"></script>

        <script type="text/javascript" language="javascript">
        var xmlHttp;
		</script>
	
  </head>

  <body>
	<?php
		// Grants access to database after checking if session is valid
		include 'session_access.php';
	?>

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
                  <li class="active"><a href="#">Dashboard</a></li>
                  <li><a href="#">TBA</a></li>
                  <li><a href="#">TBA</a></li>
                  <li><a href="user_setting.php">Setting</a></li>
                  <li><a href="signoff.php">Sign off</a></li>
                </ul>
              </nav>
            </div>
          </div>
		  


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
                  <li><a href="user_setting.php">Setting</a></li>
                  <li><a href="signoff.php">Sign off</a></li>
                </ul>
              </nav>
            </div>
          </div>

			<div class="panel panel-default">
			  <!-- Default panel contents -->
			  <div class="panel-body">
				<h3><p style="color:black">Select your account request</p></h3>
			  </div>
			  <!-- Table -->
			  <form id="change">
			  <table  class="table" style="color:#404040">
				  <tr>
					<th width="50%" style="vertical-align:middle">
						<span class="input-group-addon">
						Promote <input type="radio">
						Demote <input type="radio">
						Deletion <input type="radio">
						</span>
					</th>
				  </tr>
				  <tr>
					<td width="50%" style="vertical-align:middle"><input type="text" class="form-control" placeholder="Reason (optional)" name="nickname" id="inputNickname"></td>
				  </tr>
			  </table>
				<h5><div class="error" name="error" id="error" style="font-size: 12px; color:#900000;"></div></h5>
				<div class="btn-group btn-block" role="group">
					<button type="button" class="btn btn-primary" style="width:50%" id="sbutton" onclick="submitChange()">Submit</button>
					<button type="reset" class="btn btn-primary" style="width:50%" id="rbutton">Reset</button>
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
