  <?php
/* Builds a WHERE string to use for SQL based on POST variables. */
  	function buildWhere() {
	global $default_quarter;
		$where = "";
		//check if we got here via submit... (only have to check this once because if one POST is set, they all will be, even if values are "")
		if(isset($_POST['program'])) {
			//by quarter
			if($_POST['quarter'] != -1) {
				$where .= "WHERE yrq LIKE '" . $_POST['quarter'] . "' ";
				$where .= "AND override != '-1'";
			}
			//search by program
			if($_POST['program'] != -1) {
				if($where != "") {
					$where .= "AND ";
				} else {
					$where = "WHERE ";
				}
				//if searching for I-BEST (999), display that. if not, display requested program id
				if($_POST['program'] == "999") {
					$where .= " ibest = 1 ";
				} else if($_POST['program'] == "998") {
					$where .= " ibest2 = 1 ";
				} else if($_POST['program'] == "997") {
					$where .= " ibest3 = 1 ";
				} else {
					$where .= " override = '" . $_POST['program'] . "' ";
				}
			}
			//search by instructor
			if($_POST['instructor'] != "") {
				if($where != "") {
					$where .= "AND ";
				} else {
					$where = "WHERE ";
				}
				$where .= "instr_name LIKE '%" . $_POST['instructor'] . "%' ";
			}
			//search by keyword (in description)
			if($_POST['keyword'] != "") {
				if($where != "") {
					$where .= "AND ";
				} else {
					$where = "WHERE ";
				}
				$where .= "MATCH(cdescription, ctitle, course_id, course_title) AGAINST ('" . $_POST['keyword'] . "' IN BOOLEAN MODE) ";
			}
			//starting after a time
			if($_POST['after'] != -1) {
				if($where != "") {
					$where .= "AND ";
				} else {
					$where = "WHERE ";
				}
				$where .= "start_24 >= " . $_POST['after'] . " AND start_24 != -1 ";
			}
			//ending before a time
			if($_POST['before'] != -1) {
				if($where != "") {
					$where .= "AND ";
				} else {
					$where = "WHERE ";
				}
				$where .= "end_24 <= " . $_POST['before'] . " AND end_24 != -1 ";
			}
			//ending before a time
			if($_POST['credits'] != -1) {
				if($where != "") {
					$where .= "AND ";
				} else {
					$where = "WHERE ";
				}
				$where .= "cr = " . $_POST['credits'] . " ";
			}
			//location
			if(isset($_POST['mode']) && count($_POST['mode']) < 5) {
				if($where != "") {
					$where .= "AND ";
				} else {
					$where = "WHERE ";
				}
				$where .= '(';
				for($i = 0; $i < count($_POST['mode']); $i++) {
					if($i > 0) {
						//if not the first checked, add an "or" to the statement
						$where .= ' OR ';
					}
					$where .= 'mode = ' . $_POST['mode'][$i] . ' ';
				}
				$where .= ') ';
			}
			//not full
			if(isset($_POST['notFull'])) {
				if($where != "") {
					$where .= "AND ";
				} else {
					$where = "WHERE ";
				}
				$where .= 'enr != class_cap ';
			}
		} else {
			$where = "WHERE yrq LIKE '" . $default_quarter . "'";
		}
		//return where string
		echo "<span style='display:none'>";
		echo $where;
		echo "</span>";
		return $where;

	
	}

  ?>