<?php
// HAS TO BE IN THE ROOT DIRECTORY OF THE SITE!!!
@set_time_limit(30); 
@error_reporting(E_ALL);
@ini_set('session.use_cookies',1);
@ini_set('session.use_only_cookies',1);
@ini_set('session.use_trans_sid',0);
@ini_set('session.gc_maxlifetime',7200); // 2 hour session!
@ini_set('default_charset', 'UTF-8');

@mb_internal_encoding('UTF-8');
@mb_http_output('UTF-8');

@set_magic_quotes_runtime(0);

// Session should start here!
session_start();


// So templates can be included
define ('HIDDEN_PW', "do_not_change");
define ('TMPL_DIR', './templates');
define ('SECURE_KEY', 'G#%$Ano0'); 

// You need to set your own parameters!!
define('MYSQL_SERVER', 'localhost:3306');
define('MYSQL_USER', 'jwinte66148_db');
define('MYSQL_DB', 'jwinte66148_db');
define('MYSQL_PASSWORD', 'rYLEl705');

// You'd need to activate code below once you have proper database setup.
 $GLOBALS['DB']= @mysql_connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD) or die ('Cannot connect to the MySQL server');
 mysql_select_db(MYSQL_DB, $GLOBALS['DB']) or die ('Cannot select MySQL database');

require_once('./includes/functions.inc.php');
?>