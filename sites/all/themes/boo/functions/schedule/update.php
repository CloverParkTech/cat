<?php
	/************************
	 * update.php
	 * This file is used to update the MySQL database with the most current information from the SMS database via JSON through a web service.
	 * This file should be ran on 30 minute intervals (as the SMS database only updates every 30 minutes).
	 * Each update will check for course data for the next x amount of quarters (set below in the variable $look_at).
	 * Each quarter checked will delete all current quarter data, then insert the current data. This way, deleted courses will be removed
	 *	and every case will be handled, where certain cases could be missed via update instead.
	 * In addition to deleted course removed, each update will update course enrollment information.
	 * NOTE: Enrollment information may be up to an hour delayed, so in certain cases, a class may show enrollment room on here, but be full when
	 *	registration is attempted.
	 *
	 *************************/
	 
	//error reporting for dev stage
	ini_set('display_errors', 'On');
	error_reporting(E_ALL);
	
	//start timer
	$time_start = microtime(true);
	
	//default isn't set
	date_default_timezone_set('America/Los_Angeles');
	
	//get config file
	require('config.php');

	
	
	//GLOBALS
	$client = new SoapClient("https://tredstone.cptc.edu/CourseData/CPTC_Courses.asmx?wsdl");
	$dbh = null;
	//how many quarters to look ahead for
	$look_at = 8;
	//when finding new quarters, this checks for YRQ every x ammount of days. If a quarter isn't found, try a smaller number
	$daysSkipped = 15;
	//attempt to connect to DB
	try {
		$dbh = new PDO("mysql:host=localhost;dbname=$scheduledbname", $scheduledbuser, $scheduledbpass);
	} catch(PDOException $e) {
		echo "Access denied: " . $e->getMessage();
	}
	
	getQuarters('150601');
	
	//close database and web service connections
	$dbh = null;
	$client = null;
	
	//print time taken
	$time_end = microtime(true);
	$time = $time_end - $time_start;
	echo  "<br>Update complete in: " . round($time,2) . " s";
	
	//     *************************
	//     ******* FUNCTIONS *******
	//     *************************
	





	/**
	 * Gets json feed from web service, truncates database, then inserts new data into database
	 */
	function getQuarters($date) {
		global $look_at, $daysSkipped;
		if(isset($date)) {
			$y = substr($date, 0, 2);
			$m = substr($date, 2, 2);
			$d = substr($date, 4, 2);
			
			$yrqs = array();
			$names = array();
			//find the next quarters as specified by $look_at
			while(count($yrqs) < $look_at) {
				$yrq = getYRQ($y . $m . $d);
				//echo $y . $m . $d . ' got: ' . $yrq . '<br>';
				//if it's the first YRQ found or a new one from the last found, add it to the list
				if(strlen($yrq) > 3 && (count($yrqs) == 0 || $yrqs[count($yrqs) - 1] != $yrq)) {
					$yrqs[] = $yrq;
					$names[] = getQtr($yrq);
				}
				//update date to check the next week
				$d += $daysSkipped;
				if($d > 28) {
					$m++;
					$d = 0;
					if($m > 12) {
						$m = 0;
						$y++;
					}
				}
				//add zeros to numbers if single digits
				if($d < 10) {
					$d = '0' . $d;
				}
				if(strlen($d) > 2) {
					$d = substr($d, -2);
				}
				if($m < 10) {
					$m = '0' . $m;
				}
				if(strlen($m) > 2) {
					$m = substr($m, -2);
				}
			}
			processYrqs($yrqs, $names);
		}
	}
	
	/**
	 * Given a date in YYMMDD form, returns the YRQ for the following quarter
	 * Returns YRQ for next quarter
	 */
	function getYRQ($date) {
		global $client;
		$yrq = $client -> __soapCall("GetYRQ", array(array("now" => $date))) -> GetYRQResult;
		return $yrq;
	}
	
	/**
	 * Converts a YRQ (ie 'B452'), to a quarter name (ie 'Spring 2014')
	 * Returns quarter name
	 */
	function getQtr($yrq) {
		global $client;
		$qtr = $client -> __soapCall("GetQtrNameFromYRQ", array(array("yrq" => $yrq))) -> GetQtrNameFromYRQResult;
		//format quarter titles
		$qtr = str_replace('SPRNG', 'Spring', $qtr);
		$qtr = str_replace('SUMMR', 'Summer', $qtr);
		$qtr = str_replace('FALL', 'Fall', $qtr);
		$qtr = str_replace('WINTR', 'Winter', $qtr);
		//return quarter name
		return trim($qtr);
	}
	
	/**
	 * Given two arrays with quarters (YRQ and quarter name), processes each yrq to check for needed updates
	 */
	function processYrqs($yrqs, $names) {
		global $dbh;
		//get list of courses needed to force admin_unit (as set by xml)
		$force = getForced();
		for($i = 0; $i < count($yrqs); $i++) {
			$active = updateCourses($yrqs[$i], $force);
			$time = getTimeStamp($names[$i]);
			$stmt = $dbh -> prepare("INSERT quarters (yrq, name, time_stamp, active) VALUES (:yrq, :name, :time_stamp, :active)
										ON DUPLICATE KEY UPDATE active = :active");
			$stmt->bindParam(':yrq', $yrqs[$i]);
			$stmt->bindParam(':name', $names[$i]);
			$stmt->bindParam(':time_stamp', $time);
			$stmt->bindParam(':active', $active);
			$stmt->execute();
		}
		
	}
	
	/*
	 * Given a quarter name (Spring 2014) format, generates a timeStamp
	 * Returns timestamp of quarter
	 */
	function getTimeStamp($quarter) {
		//get year value and space by 4 (since there are 4 quarters
		$time = intval(substr($quarter, -4)) * 4;
		//add one for each quarter into the year it is (ie, summer is 3rd, so add 2)
		switch(substr($quarter, 0, strpos($quarter, ' '))) {
			case 'Fall':
				$time++;
			case 'Summer':
				$time++;
			case 'Spring':
				$time++;
			default:
				return $time;
		}
	}
	
	/**
	 * Gets json feed from web service, truncates database, then inserts new data into database
	 * Returns 1 if courses were found for the YRQ, or 0 if not
	 */
	function updateCourses($yrq, $force) {
		global $dbh;
		global $client;
		//in case script needs to break
		$go = true;
		//error reporting for dev stage
		ini_set('display_errors', 'On');
		error_reporting(E_ALL);

		//build call to webservice to get json feed
		$params = array(
			"yrq" => $yrq,
		);
		$response = $client->__soapCall("GetClassDataYRQ", array($params));

		//create json string
		$json = json_decode($response->GetClassDataYRQResult);
		//empty old database
		$stmt = $dbh->prepare('DELETE FROM courses WHERE yrq = \'' . $yrq . '\'');
		$stmt->execute();
		
		// write json output to json file. for dev purposes only
		echo "<pre>";
		print_r($json);
		echo "</pre>";
		
		//insert each item into the database
		foreach($json as $item) {
			insertViaJSON($item, $force);
		}
		
		if(count($json) > 5) {
			return 1;
		} else {
			return 0;
		}
	}


	
	/**
	 * Given an item of json, inserts data into database
	 * $item = row of json
	 * $dbh = opened connection to database
	 */
	function insertViaJSON($item, $force) {
		global $dbh, $force_item;
		$sql = "INSERT INTO courses (admin_unit, class_cap, class_fee1, class_id, course_id, course_title, cr, day_cd, end_date, end_time, enr, instr_name, org_indx, prg_indx, room_loc, sect, sect_stat, strt_date, strt_time, yrq, start_24, end_24, mode, sbctc_misc_1, class_fee, class_fee_summer)
			VALUES (:admin_unit, :class_cap, :class_fee1, :class_id, :course_id, :course_title, :cr, :day_cd, :end_date, :end_time, :enr, :instr_name, :org_indx, :prg_indx, :room_loc, :sect, :sect_stat, :strt_date, :strt_time, :yrq, :start_24, :end_24, :mode, :sbctc_misc_1, :class_fee, :class_fee_summer)";
		
		//format all the data
		formatItems($item);

		
		//if forced admin_unit set, change to that admin_unit
		if(isset($force[trim($item->COURSE_ID)]) && $force[trim($item->COURSE_ID)] != -2) {
			$item->ADMIN_UNIT = $force[trim($item->COURSE_ID)];
			//echo "Forced '" . trim($item->COURSE_ID) . "' to admin_unit: " . $force[trim($item->COURSE_ID)] . "<br>";
		}
		
		//force admin_unit based on item number (from config.php)
		if(isset($force_item[$item->CLASS_ID])) {
			$item->ADMIN_UNIT = $force_item[$item->CLASS_ID];
		}
		
		//checking if MQ makes it to this point
		if($item->ADMIN_UNIT == "MQ") {
			echo "MQ Found<br>";
		}
		
		//insert if admin_unit isn't -1. -1 means unwanted course.
		if($item->ADMIN_UNIT != -1) {
			$stmt = $dbh->prepare($sql);
			bindParams($stmt, $item);
			$stmt->execute();
		}
	}
	
	/**
	 * Get and return a list of courses that have a forced admin_unit set via XML.
	 * Forced courses are pulled from course_description table and are rows where force_admin != -2
	 */
	function getForced() {
		global $dbh;
		$forced = array();
		$sql = "SELECT cnumber, force_admin FROM course_description WHERE force_admin != -2";
		$stmt = $dbh->prepare($sql);
		$stmt->execute();
		while ($row = $stmt->fetchObject()) {
			$forced[$row->cnumber] = $row->force_admin;
		}
		return $forced;
	}
	
	/**
	 * Formats SMS data into usable style for CPTC.
	 */
	function formatItems($item) {
		//get timestamp before time is formatted.
		$item->END_24 = timeTo24($item->END_TIME);
		$item->START_24 = timeTo24($item->STRT_TIME);
		//done after time used.
		mergeAdminUnit($item);
		$item->CLASS_ID = formatClassID($item->CLASS_ID);
		$item->COURSE_ID = formatCourseID($item->COURSE_ID);
		$item->COURSE_TITLE = formatCourseTitle($item->COURSE_TITLE);
		$item->CR = formatCredits($item->CR);
		$item->END_TIME = formatTime($item->END_TIME);
		$item->INSTR_NAME = formatName($item->INSTR_NAME);
		$item->MODE = findMode($item->ROOM_LOC);
		$item->ROOM_LOC = formatLocation($item->ROOM_LOC);
		$item->SECT_STAT = trim($item->SECT_STAT);
		$item->STRT_TIME = formatTime($item->STRT_TIME);
		$item->SBCTC_MISC_1 = formatHybrid($item->SBCTC_MISC_1);
		$item->CLASS_FEE = formatClassfeefall($item->CLASS_FEE_CD1, $item->CLASS_FEE_CD2);
		$item->CLASS_FEE_SUMMER = formatClassfeesummer($item->CLASS_FEE_CD1, $item->CLASS_FEE_CD2);
		//remove unwanted courses
		filterCourses($item);
	}

	
	/**
	 * Will bind all the parameters to their respective values.
	 * Call this after data has been formatted.
	 */
	 function bindParams($stmt, $item) {
		$stmt->bindParam(':admin_unit', $item->ADMIN_UNIT);
		$stmt->bindParam(':class_cap', $item->CLASS_CAP);
		$stmt->bindParam(':class_fee1', $item->CLASS_FEE1);
		$stmt->bindParam(':class_id', $item->CLASS_ID);
		$stmt->bindParam(':course_id', $item->COURSE_ID);
		$stmt->bindParam(':course_title', $item->COURSE_TITLE);
		$stmt->bindParam(':cr', $item->CR);
		$stmt->bindParam(':day_cd', $item->DAY_CD);
		$stmt->bindParam(':end_date', $item->END_DATE);
		$stmt->bindParam(':end_time', $item->END_TIME);
		$stmt->bindParam(':enr', $item->ENR);
		$stmt->bindParam(':instr_name', $item->INSTR_NAME);
		$stmt->bindParam(':org_indx', $item->ORG_INDX);
		$stmt->bindParam(':prg_indx', $item->PRG_INDX);
		$stmt->bindParam(':room_loc', $item->ROOM_LOC);
		$stmt->bindParam(':sect', $item->SECT);
		$stmt->bindParam(':sect_stat', $item->SECT_STAT);
		$stmt->bindParam(':strt_date', $item->STRT_DATE);
		$stmt->bindParam(':strt_time', $item->STRT_TIME);
		$stmt->bindParam(':yrq', $item->YRQ);
		$stmt->bindParam(':start_24', $item->START_24);
		$stmt->bindParam(':end_24', $item->END_24);
		$stmt->bindParam(':mode', $item->MODE);
		$stmt->bindParam(':sbctc_misc_1', $item->SBCTC_MISC_1);
		$stmt->bindParam(':class_fee', $item->CLASS_FEE);
		$stmt->bindParam(':class_fee_summer', $item->CLASS_FEE_SUMMER);
	 }
	
	/**
	 * Takes a string in form of 0720P and returns 24 hour representation (ie 0720P becomes 1920)
	 */
	function timeTo24($str) {
		$str = trim($str);
		//if string not in correct format, return -1
		//this is the case for online classes or ARR times
		if(strlen($str) < 5) {
			return -1;
		}
		//get int out of string
		$nums = intval(substr($str, 0, 4));
		//check if AM or PM, since PM needs to add 1200 to time (unless 12PM)
		if(strpos($str, "A") !== false) {
			//if AM
			if(($nums / 100) == 12) {
				$nums += 1200;
			}
		} else {
			//if PM
			$nums += 1200;
			if(($nums / 100) >= 24) {
				$nums -= 1200;
			}
		}
		//return final value
		return $nums;
	}
	
	/**
	 * Returns the class mode number based on the room location.
	 * 1 = Lakewood
	 * 2 = South Hill
	 * 3 = Online
	 * 4 = Arranged
	 * 5 = Off Campus
	 */
	function findMode($str) {
		$mode;
		if(strpos($str, "LINE") !== false) {
			//class is online: (3)
			$mode = 3;
		} else if(strpos($str, "SHC") !== false) {
			//class is at South Hill (2)
			$mode = 2;
		} else if(strpos($str, "ARR") !== false) {
			//class is listed as Arranged Campus(4)
			$mode = 4;
		} else if(strpos($str, "OFFCAMP") !== false) {
			//class is listed as Off Campus(5)
			$mode = 5;
		} else {
			//class is at Lakewood (by default) (1)
			$mode = 1;
		}
		return $mode;
	}
	
	/**
	 * Merges select programs into a single admin ID.
	 * Merges Daycare Coordinators (13) with Early Care & Education (41)
	 * Merges Computer Application (20) with General Education (5)
	 * Merges RN Option (25) with Nursing (80)
	 * Merges Cosmotology-Purdy (52) with Cosmotology (53)
	 * Merges Medical Esthetics (72) with Esthetic Sciences (62)
	 * Merges Dental Assistant (77) with Dental (4)
	 * Merges COLL 101 (2) with General Ed (5), removes other courses in 2
	 */
	function mergeAdminUnit($item) {
		switch($item->ADMIN_UNIT) {
			case 13:
				$item->ADMIN_UNIT = 41;
				break;
			case 20:
				$item->ADMIN_UNIT = 5;
				break;
			case 25:
				$item->ADMIN_UNIT = 80;
				break;
			case 52:
				$item->ADMIN_UNIT = 53;
				break;
			case 72:
				$item->ADMIN_UNIT = 62;
				break;
			case 77:
				$item->ADMIN_UNIT = 4;
				break;
				// some of the "4" admin units are listed as "04"
			case 04:
				$item->ADMIN_UNIT = 4;
				break;
			case 2:
				//admin_unit 2 contains coll 101 (which goes with gened) and junk courses
				//remove junk courses by setting admin_unit to -1
				if(strpos(formatCourseID($item->COURSE_ID), "COLL 101") !== false) {
					$item->ADMIN_UNIT = 5;
				} else {
					$item->ADMIN_UNIT = -1;
				}
				break;
		}
	}
	
	/**
	 * Removes following unwanted courses from the list...
	 * Removes all ADHS courses.
	 * Removes MDP 212, MDP 210, MDP 231 and MDP 239, since program is being phased out.
	 * Removes anything with admin_unit of 0 (misc classes)
	 */
	function filterCourses($item) {
		if(strpos($item->COURSE_ID, "ADHS") !== false){
			$item->ADMIN_UNIT = 212;
		} else if(strpos($item->COURSE_ID, "MDP") !== false) {
			//if MDP found, check for 212, 210, 231 or 239, and remove
			if(strpos($item->COURSE_ID, "212") !== false) {
				$item->ADMIN_UNIT = -1;
			} else if(strpos($item->COURSE_ID, "210") !== false) {
				$item->ADMIN_UNIT = -1;
			} else if(strpos($item->COURSE_ID, "231") !== false) {
				$item->ADMIN_UNIT = -1;
			} else if(strpos($item->COURSE_ID, "239") !== false) {
				$item->ADMIN_UNIT = -1;
			}
		} else if($item->ADMIN_UNIT == 0 && strlen($item->ADMIN_UNIT) < 2) {
			$item->ADMIN_UNIT = -1;
		}
	}
	
	/**
	 * Converts removes extra spaces, leaving, at most, one space. Trims extra spaces from front and back.
	 */
	function formatCourseID($str) {

		// insert space before the digits
		$length = strlen($string);
		$position = $length - 5;

		$str = substr_replace($str, ' ', $position, 0);



		while(strpos($str, '  ') !== false) {
			$str = str_replace('  ', ' ', $str);
		}



		return $str;
	}

	/**
	 * Formats time to school standard
	 * School standard is: "5 p.m." or "7:30 a.m."
	 * Stored in database as "0115P" or 
	 * Returns String of date in correct format
	 */
	function formatTime($str) {
		//if ARR or blank, change to "Arranged"
		if(strpos($str, "R") || (!strpos($str, "P") && !strpos($str, "A"))) {
			return "Arranged";
		}
		//get A or P
		$end = $str{4};
		//convert A or P to " a.m." or " p.m."
		if($end == 'P') {
			$end = " p.m.";
		} else if($end == 'A') {
			$end = " a.m.";
		} else {
			$end = "";
		}
		//get hour and minute from original time
		$hr = substr($str, 0, 2);
		$min = substr($str, 2, 2);
		//remove leading zero from hour if needed
		if($hr{0} == '0') {
			$hr = $hr{1};
		}
		//clear minute if double zero, or not, add semicolon to front
		if($min == "00") {
			$min = "";
		} else {
			$min = ":" . $min;
		}
		
		return $hr . $min . $end;
	}

	/**
	 * Converts "ON LINE" to "Online"
	 * Converts "OFFCAMP" to "Off Campus"
	 * Converts "ARR" to "Arranged"
	 * Adds building and room where needed (ie changes "02 234" to "Building 02 Room 234")
	 * Returns formatted string
	 */
	function formatLocation($str) {
		//if at south hill campus
		if(strpos($str, "SHC") !== false) {
			if(strlen(trim($str)) == 3) {
				//if no room info, simply return "South Hill Campus"
				return "South Hill Campus";
			} else {
				//if it contains room info...
				//return "South Hill Campus Room #"
				return "South Hill Campus Room " . substr($str, 3);
			}
		}
		//change "ON LINE" to "Online"
		if(strpos($str, "ON LINE") !== false) {
			return "Online";
		}
		//change "OFFCAMPUS" to "Off Campus"
		if(strpos($str, "OFFCAMP") !== false) {
			return "Off Campus";
		}
		//change "ARR" to "Arranged"
		if(strpos($str, "ARR") !== false) {
			return "Arranged";
		}
		//change "'TBD '" to "TBD"
		if(strpos($str, "TBD") !== false) {
			return "TBD";
		}
		//add the words "Building" and "Room" where needed
		if($str{2} == ' ') {
			$full = "Bldg. " . substr($str, 0, 2);
			//if no room into, leave rm. part off
			if(strlen(trim($str)) > 2) {
				$full .= ", Rm. " . substr($str, 3);
			}
			return $full;
		}
		//default quotes around unhandled cases
		return "'" . $str . "'";
	}



	/**
	 * Formats names.
	 * Checks if space missing after comma, if so, adds space
	 * Caps last and first name
	 * Returns formatted string
	 */
	function formatName($str) {
		global $name_swap;
		$str = trim($str);

		if(isset($name_swap[$str])) {
			return $name_swap[$str];
		} else if(strpos($str, "STAFF") !== false) {
			return "Staff";
		} else if(strpos($str, "WAOL") !== false) {
			return "WAOL";
		} 
		else if(strpos($str, ",") !== false) {
			$last = substr($str, 0, strpos($str, ","));
			$first = trim(substr($str, strpos($str, ",") + 1));
		} else if(strpos($str, " ") !== false) {
			$last = substr($str, 0, strpos($str, " "));
			$first = trim(substr($str, strpos($str, " ") + 1));
			//if course is Educa To Go...
			if(strpos($first, "TO GO") !== false) {
				return "Arranged";
			}
		} else {
			echo "Conflict for: '" . $str . "' Use:<br>"; 
			echo '"' . $str . '" => "New, Name",<hr>';
			return "Arranged";
		}
		$str = $last . ", " . $first{0} . ".";
		return ucwords(strtolower($str));
	}
	
	/**
	 * Extracts line number from class ID
	 * Class_id stored as: "5474B344" where first 4 is line number and last 4 is quarter YRQ (key for quarter name)
	 */
	function formatClassID($str) {
		if($str && (strlen($str) > 3)) {
			return substr($str, 0, 4);
		} else {
			return "Err";
		}
	}
	
	/**
	 * Credits stored multiplied by 10 (ie a 5 credit class is stored as 50 cr)
	 */
	function formatCredits($int) {
		return $int / 10.0;
	}
	
	/**
	 * Changes from all caps to just caps on first letter of each word
	 * Fixes roman numeral caps (Vii to VII)
	 * Caps first letter after "/" (One/two to One/Two)
	 * Caps first letter after "," if not a space
	 * Returns formated string
	 */
	function formatCourseTitle($str) {
		//make first letter of words upper-case and rest lower-case
		$str = ucwords(strtolower($str));
		//make trailing roman numerals upper-case
		$lower = array("Viii", "Vii", "Vi", "Ix", "Xiii", "Xii", "Xi", "Iv", "Iii", "Ii");
		$upper = array("VIII", "VII", "VI", "IX", "XIII", "XII", "XI", "IV", "III", "II");
		for($i = 0; $i < count($lower); $i++) {
			if(strpos($str, $lower[$i])) {
				$str = substr($str, 0, strpos($str, $lower[$i])) . $upper[$i];
			}
		}

		//make words after a backslash upper-case
		for ($i = 0; $i < strlen($str); $i++)  { 
			$cr = $str{$i};
			if($cr == "/") {
				$str{$i + 1} = strtoupper($str{$i + 1});
			}
		}  
		
		//make words after a comma upper-case
		for ($i = 0; $i < strlen($str); $i++)  { 
			if($str{$i} == ",") {
				if($str{$i + 1} != " ") {
					$str{$i + 1} = strtoupper($str{$i + 1});
				}
			}
		}
		
		//make words after a period (with no space) upper-case
		for ($i = 0; $i < strlen($str); $i++)  { 
			if($str{$i} == ".") {
				if($str{$i + 1} != " ") {
					$str{$i + 1} = strtoupper($str{$i + 1});
				}
			}
		}
		
		//make words after a - upper-case
		for ($i = 0; $i < strlen($str); $i++)  { 
			if($str{$i} == "-") {
				if($str{$i + 1} != " ") {
					$str{$i + 1} = strtoupper($str{$i + 1});
				}
			}
		}
		
		//make words after a : upper-case
		for ($i = 0; $i < strlen($str); $i++)  { 
			if($str{$i} == ":") {
				if($str{$i + 1} != " ") {
					$str{$i + 1} = strtoupper($str{$i + 1});
				}
			}
		}
		
		return $str;
	}



	function formatHybrid($str) {
		if ($str == 3) {
			$str = "Online";
		}

		elseif ($str == 8) {
			$str = "Hybrid";
		}

		elseif ($str == 9) {
			$str = "Web-Enhanced";
		}
		else {
			$str = "In-Person";
		}
		return $str;

	}

	function formatClassfeefall($str1, $str2) {
	
	$classFeeFall = 0.00;	
	$feeArray = array( $str1, $str2);


	foreach($feeArray as $item) {


	switch($item) {
case"#I":$classFeeFall += 234.87; break;
case"#J":$classFeeFall += 587.84; break;
case"#L":$classFeeFall += 234.87; break;
case"#M":$classFeeFall += 587.84; break;
case"+G":$classFeeFall += 46.84; break;
case"+H":$classFeeFall += 51.85; break;
case"+Q":$classFeeFall += 31.53; break;
case"-1":$classFeeFall += 3.73; break;
case"-2":$classFeeFall += 4.42; break;
case"0H":$classFeeFall += 31.53; break;
case"10":$classFeeFall += 1.00; break;
case"51":$classFeeFall += 2500.00; break;
case"5D":$classFeeFall += 50.00; break;
case"95":$classFeeFall += 83.52; break;
case"A1":$classFeeFall += 2.44; break;
case"A2":$classFeeFall += 119.83; break;
case"A3":$classFeeFall += 7.72; break;
case"A4":$classFeeFall += 4.73; break;
case"A5":$classFeeFall += 2.71; break;
case"A9":$classFeeFall += 4.64; break;
case"AC":$classFeeFall += 30.00; break;
case"AE":$classFeeFall += 26.00; break;
case"AF":$classFeeFall += 30.00; break;
case"AM":$classFeeFall += 50.00; break;
case"B2":$classFeeFall += 4.73; break;
case"B4":$classFeeFall += 31.53; break;
case"BC":$classFeeFall += 95.00; break;
case"BI":$classFeeFall += 30.00; break;
case"BP":$classFeeFall += 63.00; break;
case"CA":$classFeeFall += 31.60; break;
case"CB":$classFeeFall += 30.00; break;
case"CC":$classFeeFall += 1.00; break;
case"CF":$classFeeFall += 31.60; break;
case"CH":$classFeeFall += 35.00; break;
case"CM":$classFeeFall += 4.75; break;
case"CN":$classFeeFall += 50.00; break;
case"CO":$classFeeFall += 75.00; break;
case"CP":$classFeeFall += 50.00; break;
case"CS":$classFeeFall += 0.98; break;
case"CT":$classFeeFall += 19.00; break;
case"CU":$classFeeFall += 125.00; break;
case"D4":$classFeeFall += 5.00; break;
case"D8":$classFeeFall += 3.49; break;
case"DA":$classFeeFall += 53.00; break;
case"EB":$classFeeFall += 100.00; break;
case"EC":$classFeeFall += 10.00; break;
case"ED":$classFeeFall += 2.00; break;
case"EE":$classFeeFall += 3.00; break;
case"EL":$classFeeFall += 26.00; break;
case"ER":$classFeeFall += 99999.00; break;
case"ES":$classFeeFall += 75.00; break;
case"ET":$classFeeFall += 65.00; break;
case"F4":$classFeeFall += 1.00; break;
case"FD":$classFeeFall += 44.00; break;
case"FM":$classFeeFall += 150.00; break;
case"FS":$classFeeFall += 23.00; break;
case"GD":$classFeeFall += 120.00; break;
case"GP":$classFeeFall += 64.00; break;
case"GV":$classFeeFall += 10.00; break;
case"H1":$classFeeFall += 93.65; break;
case"H3":$classFeeFall += 192.75; break;
case"HE":$classFeeFall += 29.00; break;
case"HL":$classFeeFall += 40.00; break;
case"HR":$classFeeFall += 73.00; break;
case"HV":$classFeeFall += 40.00; break;
case"ID":$classFeeFall += 5.00; break;
case"IF":$classFeeFall += 40.00; break;
case"IN":$classFeeFall += 50.00; break;
case"IS":$classFeeFall += 14.00; break;
case"IT":$classFeeFall += 20.00; break;
case"J1":$classFeeFall += 67.52; break;
case"J3":$classFeeFall += 189.85; break;
case"L1":$classFeeFall += 65.45; break;
case"L3":$classFeeFall += 187.71; break;
case"LF":$classFeeFall += 30.00; break;
case"LN":$classFeeFall += 0.00; break;
case"LP":$classFeeFall += 88.00; break;
case"LS":$classFeeFall += 0.00; break;
case"LV":$classFeeFall += 25.00; break;
case"M5":$classFeeFall += 40.00; break;
case"M6":$classFeeFall += 20.00; break;
case"M7":$classFeeFall += 50.00; break;
case"M8":$classFeeFall += 150.00; break;
case"M9":$classFeeFall += 366.00; break;
case"MA":$classFeeFall += 25.00; break;
case"MD":$classFeeFall += 25.00; break;
case"ME":$classFeeFall += 250.00; break;
case"MG":$classFeeFall += 70.00; break;
case"MK":$classFeeFall += 26.00; break;
case"MN":$classFeeFall += 39.00; break;
case"MP":$classFeeFall += 9.97; break;
case"MS":$classFeeFall += 45.00; break;
case"MT":$classFeeFall += 7.25; break;
case"MW":$classFeeFall += 40.00; break;
case"N1":$classFeeFall += 125.00; break;
case"NA":$classFeeFall += 40.00; break;
case"NC":$classFeeFall += 75.00; break;
case"NG":$classFeeFall += 20.00; break;
case"NL":$classFeeFall += 25.00; break;
case"NS":$classFeeFall += 35.00; break;
case"P1":$classFeeFall += 10.00; break;
case"P2":$classFeeFall += 7.00; break;
case"P4":$classFeeFall += 0.04; break;
case"P5":$classFeeFall += 5.00; break;
case"P6":$classFeeFall += 1.25; break;
case"PA":$classFeeFall += 100.00; break;
case"PB":$classFeeFall += 0.00; break;
case"PC":$classFeeFall += 0.00; break;
case"PD":$classFeeFall += 0.00; break;
case"PF":$classFeeFall += 5.00; break;
case"PG":$classFeeFall += 20.00; break;
case"PH":$classFeeFall += 0.00; break;
case"PK":$classFeeFall += 0.71; break;
case"PL":$classFeeFall += 0.33; break;
case"PM":$classFeeFall += 0.28; break;
case"PN":$classFeeFall += 0.61; break;
case"PO":$classFeeFall += 85.00; break;
case"PP":$classFeeFall += 0.31; break;
case"PQ":$classFeeFall += 20.00; break;
case"PR":$classFeeFall += 20.00; break;
case"PW":$classFeeFall += 50.00; break;
case"PY":$classFeeFall += 1.00; break;
case"RB":$classFeeFall += 100.00; break;
case"RC":$classFeeFall += 0.00; break;
case"RD":$classFeeFall += 0.00; break;
case"RE":$classFeeFall += 50.00; break;
case"RN":$classFeeFall += 115.00; break;
case"SA":$classFeeFall += 7.50; break;
case"SB":$classFeeFall += 0.14; break;
case"SC":$classFeeFall += 3.75; break;
case"SM":$classFeeFall += 0.22; break;
case"T9":$classFeeFall += 31.53; break;
case"TA":$classFeeFall += 53.00; break;
case"TB":$classFeeFall += 25.00; break;
case"TL":$classFeeFall += 14.00; break;
case"TN":$classFeeFall += 268.26; break;
case"TP":$classFeeFall += 96.26; break;
case"TQ":$classFeeFall += 0.00; break;
case"TR":$classFeeFall += 96.26; break;
case"TX":$classFeeFall += 268.26; break;
case"WA":$classFeeFall += 212.00; break;
case"WB":$classFeeFall += 35.00; break;
case"WC":$classFeeFall += 265.00; break;
case"WE":$classFeeFall += 50.00; break;
case"WM":$classFeeFall += 105.00; break;
case"WS":$classFeeFall += 10.00; break;
case"WT":$classFeeFall += 150.00; break;
		}
	}

	return $classFeeFall;
}


	function formatClassfeesummer($str1, $str2) {
	
	$classFeeSummer = 0.00;	
	$feeArray = array( $str1, $str2);


	foreach($feeArray as $item) {


	switch($item) {
			case"#I":$classFeeSummer += 234.87; break;
case"#J":$classFeeSummer += 587.84; break;
case"#L":$classFeeSummer += 234.87; break;
case"#M":$classFeeSummer += 587.84; break;
case"+G":$classFeeSummer += 46.84; break;
case"+H":$classFeeSummer += 51.85; break;
case"+Q":$classFeeSummer += 31.53; break;
case"-1":$classFeeSummer += 3.73; break;
case"-2":$classFeeSummer += 4.42; break;
case"0H":$classFeeSummer += 31.53; break;
case"10":$classFeeSummer += 1.00; break;
case"51":$classFeeSummer += 2500.00; break;
case"5D":$classFeeSummer += 50.00; break;
case"95":$classFeeSummer += 83.52; break;
case"A1":$classFeeSummer += 2.44; break;
case"A2":$classFeeSummer += 119.83; break;
case"A3":$classFeeSummer += 7.72; break;
case"A4":$classFeeSummer += 4.73; break;
case"A5":$classFeeSummer += 2.71; break;
case"A9":$classFeeSummer += 4.64; break;
case"AC":$classFeeSummer += 30.00; break;
case"AE":$classFeeSummer += 10.00; break;
case"AF":$classFeeSummer += 30.00; break;
case"AM":$classFeeSummer += 50.00; break;
case"B2":$classFeeSummer += 4.73; break;
case"B4":$classFeeSummer += 31.53; break;
case"BC":$classFeeSummer += 95.00; break;
case"BI":$classFeeSummer += 11.00; break;
case"BP":$classFeeSummer += 63.00; break;
case"CA":$classFeeSummer += 31.60; break;
case"CB":$classFeeSummer += 30.00; break;
case"CC":$classFeeSummer += 1.00; break;
case"CF":$classFeeSummer += 31.60; break;
case"CH":$classFeeSummer += 20.00; break;
case"CM":$classFeeSummer += 4.75; break;
case"CN":$classFeeSummer += 50.00; break;
case"CO":$classFeeSummer += 75.00; break;
case"CP":$classFeeSummer += 50.00; break;
case"CS":$classFeeSummer += 0.98; break;
case"CT":$classFeeSummer += 19.00; break;
case"CU":$classFeeSummer += 75.00; break;
case"D4":$classFeeSummer += 5.00; break;
case"D8":$classFeeSummer += 3.49; break;
case"DA":$classFeeSummer += 53.00; break;
case"EB":$classFeeSummer += 100.00; break;
case"EC":$classFeeSummer += 10.00; break;
case"ED":$classFeeSummer += 2.00; break;
case"EE":$classFeeSummer += 3.00; break;
case"EL":$classFeeSummer += 26.00; break;
case"ER":$classFeeSummer += 99999.00; break;
case"ES":$classFeeSummer += 75.00; break;
case"ET":$classFeeSummer += 50.00; break;
case"F4":$classFeeSummer += 1.00; break;
case"FD":$classFeeSummer += 44.00; break;
case"FM":$classFeeSummer += 150.00; break;
case"FS":$classFeeSummer += 23.00; break;
case"GD":$classFeeSummer += 120.00; break;
case"GP":$classFeeSummer += 50.00; break;
case"GV":$classFeeSummer += 10.00; break;
case"H1":$classFeeSummer += 93.65; break;
case"H3":$classFeeSummer += 192.75; break;
case"HE":$classFeeSummer += 29.00; break;
case"HL":$classFeeSummer += 30.00; break;
case"HR":$classFeeSummer += 73.00; break;
case"HV":$classFeeSummer += 20.00; break;
case"ID":$classFeeSummer += 5.00; break;
case"IF":$classFeeSummer += 40.00; break;
case"IN":$classFeeSummer += 50.00; break;
case"IS":$classFeeSummer += 14.00; break;
case"IT":$classFeeSummer += 20.00; break;
case"J1":$classFeeSummer += 67.52; break;
case"J3":$classFeeSummer += 189.85; break;
case"L1":$classFeeSummer += 65.45; break;
case"L3":$classFeeSummer += 187.71; break;
case"LF":$classFeeSummer += 30.00; break;
case"LN":$classFeeSummer += 60.00; break;
case"LP":$classFeeSummer += 88.00; break;
case"LS":$classFeeSummer += 50.00; break;
case"LV":$classFeeSummer += 5.00; break;
case"M5":$classFeeSummer += 40.00; break;
case"M6":$classFeeSummer += 20.00; break;
case"M7":$classFeeSummer += 50.00; break;
case"M8":$classFeeSummer += 150.00; break;
case"M9":$classFeeSummer += 366.00; break;
case"MA":$classFeeSummer += 25.00; break;
case"MD":$classFeeSummer += 25.00; break;
case"ME":$classFeeSummer += 250.00; break;
case"MG":$classFeeSummer += 50.00; break;
case"MK":$classFeeSummer += 25.00; break;
case"MN":$classFeeSummer += 30.00; break;
case"MP":$classFeeSummer += 9.97; break;
case"MS":$classFeeSummer += 45.00; break;
case"MT":$classFeeSummer += 7.25; break;
case"MW":$classFeeSummer += 40.00; break;
case"N1":$classFeeSummer += 125.00; break;
case"NA":$classFeeSummer += 40.00; break;
case"NC":$classFeeSummer += 12.50; break;
case"NG":$classFeeSummer += 20.00; break;
case"NL":$classFeeSummer += 25.00; break;
case"NS":$classFeeSummer += 35.00; break;
case"P1":$classFeeSummer += 10.00; break;
case"P2":$classFeeSummer += 7.00; break;
case"P4":$classFeeSummer += 0.04; break;
case"P5":$classFeeSummer += 5.00; break;
case"P6":$classFeeSummer += 1.25; break;
case"PA":$classFeeSummer += 50.00; break;
case"PB":$classFeeSummer += 185.00; break;
case"PC":$classFeeSummer += 150.00; break;
case"PD":$classFeeSummer += 75.00; break;
case"PF":$classFeeSummer += 5.00; break;
case"PG":$classFeeSummer += 20.00; break;
case"PH":$classFeeSummer += 0.00; break;
case"PK":$classFeeSummer += 0.71; break;
case"PL":$classFeeSummer += 0.33; break;
case"PM":$classFeeSummer += 0.28; break;
case"PN":$classFeeSummer += 0.61; break;
case"PO":$classFeeSummer += 55.00; break;
case"PP":$classFeeSummer += 0.31; break;
case"PQ":$classFeeSummer += 20.00; break;
case"PR":$classFeeSummer += 20.00; break;
case"PW":$classFeeSummer += 50.00; break;
case"PY":$classFeeSummer += 1.00; break;
case"RB":$classFeeSummer += 160.00; break;
case"RC":$classFeeSummer += 160.00; break;
case"RD":$classFeeSummer += 187.00; break;
case"RE":$classFeeSummer += 50.00; break;
case"RN":$classFeeSummer += 115.00; break;
case"SA":$classFeeSummer += 7.50; break;
case"SB":$classFeeSummer += 0.14; break;
case"SC":$classFeeSummer += 3.75; break;
case"SM":$classFeeSummer += 0.22; break;
case"T9":$classFeeSummer += 31.53; break;
case"TA":$classFeeSummer += 53.00; break;
case"TB":$classFeeSummer += 25.00; break;
case"TL":$classFeeSummer += 14.00; break;
case"TN":$classFeeSummer += 268.26; break;
case"TP":$classFeeSummer += 96.26; break;
case"TQ":$classFeeSummer += 0.00; break;
case"TR":$classFeeSummer += 96.26; break;
case"TX":$classFeeSummer += 268.26; break;
case"WA":$classFeeSummer += 212.00; break;
case"WB":$classFeeSummer += 35.00; break;
case"WC":$classFeeSummer += 265.00; break;
case"WE":$classFeeSummer += 50.00; break;
case"WM":$classFeeSummer += 80.00; break;
case"WS":$classFeeSummer += 10.00; break;
case"WT":$classFeeSummer += 150.00; break;
		}
	}

	return $classFeeSummer;
}



?>