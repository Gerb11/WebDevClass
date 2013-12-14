<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" xml:lang="en-US" lang="en-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Jeremy Winter | CS3336 Final Project</title>

<link rel="stylesheet" href="./css/project.css" type="text/css" media="screen" />

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script type="text/javascript" src="./js/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="./js/project.js"></script>
</head>

<body>
	<div id="mainbody">
		<h3>Lotto 6/49 Combinations Finder</h3>
		<div id="leftColumn">
			<ul id="numbers" class="connectedSortable">
				<?php
					for($i = 1; $i <= 49; $i++) {
						echo "<li>$i</li>";
					}
				?>
			</ul>
			<div id="chosen">
				<span>Pick your numbers:</span>
				<ul class="connectedSortable">
					<li></li> 
				</ul>
			</div>
			<span id="date"></span>
		</div>
		<div id="rightColumn">
			<img id="loader" src="./images/loading.gif" alt=""/>
			<img id="graph" src="./google-bar-graph.php" alt=""/>
			<p id="graphTitle">Numbers Chosen VS Winning Occurrences</p>
			<div id="graphInput">
				<p>Start Date: (YYYY-MM-DD)</p><input type="text" name="startDate"/>
				<p>End Date: (YYYY-MM-DD)</p><input type="text" name="endDate"/>
				<button type="button">New Graph!</button>
			</div>
		</div>
	</div>
</body>
</html>
