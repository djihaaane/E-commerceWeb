<?php

define('DB_USER', "root"); // db user
define('DB_PASSWORD', ""); // db password 
define('DB_DATABASE', "ecommweb"); // database name
define('DB_SERVER', "127.0.0.1"); // db server
$con = mysqli_connect(DB_SERVER,DB_USER,DB_PASSWORD,DB_DATABASE);
?>
