<?php
	
	
	header('content-type: image/png');
	$defaultUrl = 'https://chart.googleapis.com/chart?cht=bvs&chbh=a&chs=700x408';
	if(!empty($_GET["numbers"]) && !empty($_GET["occurrences"])) {
		
		//making sure data is correct
		$barWidth = "a";
		if(preg_match_all("#\d+(,\d+)*#", $_GET["numbers"], $result)) {
			$_GET["numbers"] = implode(",", $result[0]);
			if (sizeof(explode(",", $_GET["numbers"])) <= 8) { //set the bar width if there's not enough numbers to fill the graph
				$barWidth = 80;
			}
		} else {
			readfile($defaultUrl);//display default graph if parameters are not correct
			exit();
		}
		if(preg_match_all("#\d+(,\d+)*#", $_GET["occurrences"], $result)) {
			$_GET["occurrences"] = implode(",", $result[0]);
		} else {
			readfile($defaultUrl);//display default graph if parameters are not correct
			exit();
		}
		
		$numbers = utf8HTML($_GET["numbers"]);
		$numBarSplit = implode("|", explode(",", $numbers));	
		$occurrences = utf8HTML($_GET["occurrences"]);
		$arr = Array("FF0000", "FF7F00", "4B0082", "FFFF00", "00FF00", "0000FF", "8F00FF"); //colours of the rainbow
			
		$url = 'https://chart.googleapis.com/chart?cht=bvs&chs=700x408&chbh='.$barWidth.'&chds=a&chm=N,000000,0,-1,7&chxt=x,y&chxl=0:|'.$numBarSplit.'|&chd=t:'. $occurrences.'&chco='. implode("|", $arr);
		readfile($url);
	} else {
		readfile($defaultUrl);//display default graph if parameters are not correct
	}
	
	function utf8HTML ($str='') {
  	   	return htmlentities($str, ENT_QUOTES, 'UTF-8', false); 
	}
?>