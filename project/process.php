<?php

	sleep(1);

	ini_set('max_execution_time', 300);
	define ('HTML_FILE', 'project.html');
	define ('CSV_FILE',  'project.csv');
	define ('ERROR', 'Script Error');
	define ('NOT_FOUND', 'Combination not found');
	
	define('MYSQL_SERVER', 'localhost:3306');
	define('MYSQL_USER', 'jwinte66148_db');
	define('MYSQL_DB', 'jwinte66148_db');
	define('MYSQL_PASSWORD', 'rYLEl705');
	
	$GLOBALS['DB']= @mysql_connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD) or die ('Cannot connect to the MySQL server');
	mysql_select_db(MYSQL_DB, $GLOBALS['DB']) or die ('Cannot select MySQL database');
	
	$count = mysql_query("select count(*) as ammount from project");
	$row = mysql_fetch_array($count);
	
	if($row{'ammount'} == 0) { //add data to db if it's empty
		createDBTable();
	}
	mysql_free_result($count);
	getOccurances();
	
	/*
	* creates the data for the data base. Will use curl to get the a string of the html from the lotto website and parse it using regex from there.
	*/
	function createDBTable() {
		
		date_default_timezone_set('Canada/Eastern');
		$getFields = array( //fields for the url which will be used to get the data
			'viewType' => 1,
			'fromDate' => '1982-01-01',
			'toDate' => date('Y-m-d', time()),
		);
			
		$url = "http://portalseven.com/lottery/canada_lotto_649_winning_numbers.jsp?";
		$url .= http_build_query($getFields);
			
			
			
		$ch = curl_init();
			
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
		$htmlStr = curl_exec($ch);
		curl_close($ch);
		
		//write html file to the server locally
		$htmlFile = fopen(HTML_FILE, "w");
		fwrite($htmlFile, $htmlStr);
		fclose($htmlFile);
			
		preg_match("#id=\"lottery-numbers-table\">.*</table>.*?class=\"text_align_center\"#is", $htmlStr, $tableResult); //gets the table with the lottery numbers
		$htmlStr = implode($tableResult);
			
		preg_match_all("#<tr.*?>.*?<td.*?>(.*?)</td>.*?</tr>#is", $htmlStr, $parsedNums); //returns table records with dates and numbers in html format, and dates in string form
		$htmlStr = implode($parsedNums[0]);
		$winningDates = $parsedNums[1];
				
		preg_match_all("#<td.*?><b>(.*?)</b></td>#is", $htmlStr, $winners); //gets the winning numbers
		$winners = $winners[1];
		$winners = array_map("formatNumbers", $winners); //maps the winning numbers to make sure they don't have html characters or sql characters
		
		for($i = 0; $i < sizeof($winningDates); $i++) {
			$date = date('ymd', strtotime($winningDates[$i]));
			$date = trim(utf8HTML(mysql_real_escape_string($date)));
				
			$query = sprintf("INSERT INTO `project`(`Date`, `Num1`, `Num2`, `Num3`, `Num4`, `Num5`, `Num6`, `Num7`) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)", $date, $winners[($i * 7) + 0], $winners[($i * 7) + 1], $winners[($i * 7) + 2], $winners[($i * 7) + 3], $winners[($i * 7) + 4], $winners[($i * 7) + 5], $winners[($i * 7) + 6]);
			$result = mysql_query($query);
			checkResult($result);
		}
	} 
	
	/*
	* Make sure all of the numbers don't have html characters or sql characters
	*/
	function formatNumbers($a) {
		$a = trim(utf8HTML(mysql_real_escape_string($a)));
		return !empty($a) ? $a : "0";
	}
	
	/*
	* Called after the data base is populated, and will return the occurrences of the numbers that are sent it between the dates that are also sent in.
	* If not dates are sent in, it assumes the entire range
	*/
	function getOccurances() {
		if(!isset($_GET["numbers"])) {
			echo "Numbers not set.";
			exit();
		}
		
		$numbers = $_GET["numbers"];
		$numsArray = explode(",", $numbers);
		$startDate = empty($_GET["startDate"]) ? "1900-01-01" : utf8HTML(mysql_real_escape_string($_GET["startDate"]));//if start date is empty, set to start period
		$endDate = empty($_GET["endDate"]) ? date('Y-m-d', time()) : utf8HTML(mysql_real_escape_string($_GET["endDate"]));//if end date is empty, set to current date
		
		//making sure data is correct
		if(preg_match_all("#\d+(,\d+)*#", $numbers, $result)) {
			$numbers = implode(",", $result[0]);
		} else {
			echo "Numbers parameter is incorrect";
			exit();
		}
		if(!preg_match("#\d{4}-\d{2}-\d{2}#i", $startDate, $result) || !preg_match("#\d{4}-\d{2}-\d{2}#i", $endDate, $result)) {
			echo "Date needs to be of form year-month-day";
			exit();
		}
		
		$arr = array_fill(0, 50, 0);
		foreach($numsArray as $value) {
			$arr[$value]++; //adds a dummy value to make sure that numbers with 0 occurrences are kept, so the graph can show it
		}
		
		//loops through the 6 columns and adds each of the occurrences of a given number up
		$col1Occ = mysql_query("SELECT num1, COUNT( num1 ) AS total FROM project where num1 in (".$numbers.") and date <= \"".$endDate."\" and date >= \"".$startDate."\" GROUP BY num1");
		checkResult($col1Occ);
		while($row = mysql_fetch_array($col1Occ)) {
			$arr[$row{'num1'}] += $row{'total'};
		}
		$col2Occ = mysql_query("SELECT num2, COUNT( num2 ) AS total FROM project where num2 in (".$numbers.") and date <= \"".$endDate."\" and date >= \"".$startDate."\" GROUP BY num2");
		checkResult($col2Occ);
		while($row = mysql_fetch_array($col2Occ)) {
			$arr[$row{'num2'}] += $row{'total'};
		}
		$col3Occ = mysql_query("SELECT num3, COUNT( num3 ) AS total FROM project where num3 in (".$numbers.") and date <= \"".$endDate."\" and date >= \"".$startDate."\" GROUP BY num3");
		checkResult($col3Occ);
		while($row = mysql_fetch_array($col3Occ)) {
			$arr[$row{'num3'}] += $row{'total'};
		}
		$col4Occ = mysql_query("SELECT num4, COUNT( num4 ) AS total FROM project where num4 in (".$numbers.") and date <= \"".$endDate."\" and date >= \"".$startDate."\" GROUP BY num4");
		checkResult($col4Occ);
		while($row = mysql_fetch_array($col4Occ)) {
			$arr[$row{'num4'}] += $row{'total'};
		}
		$col5Occ = mysql_query("SELECT num5, COUNT( num5 ) AS total FROM project where num5 in (".$numbers.") and date <= \"".$endDate."\" and date >= \"".$startDate."\" GROUP BY num5");
		checkResult($col5Occ);
		while($row = mysql_fetch_array($col5Occ)) {
			$arr[$row{'num5'}] += $row{'total'};
		}
		$col6Occ = mysql_query("SELECT num6, COUNT( num6 ) AS total FROM project where num6 in (".$numbers.") and date <= \"".$endDate."\" and date >= \"".$startDate."\" GROUP BY num6");
		checkResult($col6Occ);
		while($row = mysql_fetch_array($col6Occ)) {
			$arr[$row{'num6'}] += $row{'total'};
		}
		$arrTemp = array_fill(0, 50, 0);//to use with array diff and get rid of zeros that aren't needed
		$arr = array_diff($arr, $arrTemp);//all of the occurrences
		foreach($numsArray as $value) {
			$arr[$value]--; //removes the dummy number that was added for making sure 0 occurrences are kept.
		}
		echo implode(",", $arr);
	}
	
	/*
	* Given a database result, checks whether or not it had an error or not and reports the error if so.
	*/
	function checkResult($result) {
		if(!$result) {
			echo "db error";
			exit();
		}
	}
	
	function utf8HTML ($str='') {
  	   	return htmlentities($str, ENT_QUOTES, 'UTF-8', false); 
	}
?>