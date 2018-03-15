<html>
	<head>
		<title>GazeStore</title>
	</head>
	<body>
		<?php
			session_start();
			session_unset(); 
			session_destroy();
            header("Location: session_expired.html");
		?>

	</body>
</html>