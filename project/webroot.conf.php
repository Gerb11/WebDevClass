<?php

define('MYSQL_SERVER', 'localhost:3306');
define('MYSQL_USER', 'root');
define('MYSQL_DB', 'project');
define('MYSQL_PASSWORD', '');
	
$GLOBALS['DB']= @mysql_connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD) or die ('Cannot connect to the MySQL server');
mysql_select_db(MYSQL_DB, $GLOBALS['DB']) or die ('Cannot select MySQL database');

require_once("./process.php");

?>