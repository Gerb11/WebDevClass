<?php 
require_once('./webroot.conf.php');

$page=process_script();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" xml:lang="en-US" lang="en-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Jeremy Winter | CS3336 HW3</title>

<link rel="stylesheet" href="./css/hw3.css" type="text/css" media="screen" />

</head>

<body>
<div id="mainBody">


<?php echo $page; ?>

 
</div>

</body>
</html>
