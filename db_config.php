<?php
// db_config.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'bankking');
$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
?>