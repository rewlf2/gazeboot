<?php

// db settings
// Include this file for unified database access.
$sql_servername = "localhost";
$sql_username = "root";
$sql_password = "123456";

// This line will be referred in querying php files. Specifies which database to write.
$sql_dbname= "gazestore";

// Create connection
$conn = mysqli_connect($sql_servername, $sql_username, $sql_password);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// echo "Connected successfully";

$conn->set_charset("utf8");
//printf("Initial character set: %s\n", $conn->character_set_name());

?>