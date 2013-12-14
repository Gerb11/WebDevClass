<?php
	define ('HTML_FILE', 'hw2.html');
	define ('CSV_FILE',  'hw2.csv');
	define ('ERROR', 'Script Error');
	define ('NOT_FOUND', 'Combination not found');

	//Add sleep() below after testing
	sleep(1);
	
	//only create the file if it doesn't already exist, or if it doesn't have any content in it. This way it isn't created everytime, and is a lot faster
	if(!file_exists(CSV_FILE)) { //file doesn't exist
		createCsvFile();
		findMatch();
	} else {
		$fileText = file_get_contents(CSV_FILE);
		if(strlen($fileText) === 0) { //empty file
			createCsvFile();
		}
		findMatch();
	}

	/*
	* createCsvFile will create a new csv file in the current directory named hw2.csv and fill it with rows of dates followed
	* by winning lottery numbers for those dates. It gets the html file in the current directory using regex to get the values from 
	* the table directories located in the html. Once the values are extracted, the csv file is written to. 
	*/
	function createCsvFile() {
		if(file_exists(HTML_FILE)) { 
			$htmlStr = file_get_contents(HTML_FILE);
			$csvFile = fopen (CSV_FILE, "w"); // no need to check if exists, because it's writing over if it does due to the w flag.
			
			preg_match("#id=\"lottery-numbers-table\".*?</table>#is", $htmlStr, $tableResult); //gets the table with the lottery numbers
			$htmlStr = implode($tableResult);
			
			preg_match_all("#<tr.*?>.*?<td.*?>(.*?)</td>.*?</tr>#is", $htmlStr, $parsedNums); //returns table records with dates and numbers in html format, and dates in string form
			$htmlStr = implode($parsedNums[0]);
			$winningDates = $parsedNums[1];
				
			preg_match_all("#<td.*?><b>(.*?)</b></td>#is", $htmlStr, $winners); //gets the winning numbers
			$winners = $winners[1];
			
			for($i = 0; $i < sizeof($winningDates); $i++) {
				$arrayTemp = array($winningDates[$i], $winners[($i * 7) + 0], $winners[($i * 7) + 1], $winners[($i * 7) + 2], $winners[($i * 7) + 3], $winners[($i * 7) + 4], $winners[($i * 7) + 5], $winners[($i * 7) + 6]);
				fputcsv($csvFile, $arrayTemp, ",");
			}
		} else {
			echo ERROR;
		}
	}

	/*
	* find match is used once there is a csv file to search through for the winning dates. 
	* it takes the numbers parameter of the get request and uses those numbers (after it makes sure they're in the right format)
	* to determine if they match any row of numbers in the csv file.
	*/
	function findMatch() {
		//scrubbing content to make sure in good form
		if(isset($_GET["numbers"])) {
				preg_match_all("#\d+#", $_GET["numbers"], $valuesToCompare); //parses numbers out
			$valuesToCompare = $valuesToCompare[0];
			$valueLength = sizeof($valuesToCompare);
			
			if(file_exists(CSV_FILE) && $valueLength > 0) { //has to be a file and parameter has to have at least one number 
				$fileLottoNums = fopen(CSV_FILE, 'r');
				$dateFound = false;
				while(!feof($fileLottoNums)) {
					$csvLines = fgetcsv($fileLottoNums, 8);
					$csvLines[7] = -1; // bonus number doesn't count
					if($csvLines == "") { //last line
						break;
					}		
					if(sizeof(array_intersect($valuesToCompare, $csvLines)) === $valueLength) {			
						echo "Date: " . $csvLines[0]; // the date
						$dateFound = true; //date is found
						break;
					} 	
				}
				
				if(!$dateFound) {
					echo NOT_FOUND; 
				}
			} else {
				echo ERROR;
			}
		} else {
			echo ERROR;
		}
		
	}
?>