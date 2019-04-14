<?php $loginScreen = false; ?>
<?php require_once("e.php"); ?>
<?php

if (isset($_REQUEST['xargs']) && $_REQUEST['xargs'] == "NAS2018") {
	if ($_REQUEST['action'] == "CLOCK") {
		$sql = "INSERT INTO shift_clock (location_id, shift_date, ssn_nid, clock_in) VALUES (" . $_REQUEST['l'] . ", " . $_SESSION['user']->getShiftDate() . ", '" . $_SESSION['user']->getSSN() . "', NOW())";
		execSQL($sql, true);
	}

	if ($_REQUEST['action'] == "INITIALIZE") {
		$cout = "";

		$sql = "SELECT shift_clock_id FROM shift_clock WHERE ssn_nid = '" . $_SESSION['user']->getSSN() . "' AND DATE(shift_date) = " . $_SESSION['user']->getShiftDate() . " AND clock_out IS NULL";
		$rs = execSQL($sql);

		if (sizeof($rs) == 1) {
			/*$cout = "<select style='display:inline;' name='clock_location' id='clock_location'>";
			$sql = "SELECT LocationID, LocationCode FROM Locations WHERE LocationCode LIKE '" . explode(" ", $_SESSION['user']->getLocation())[0] . "%'";
			$rs = execSQL($sql);

			foreach ($rs as $r) {
				if ($r[0] == "") { continue; }
				$cout .= "<option value='" . $r[0] . "'>" . $r[1] . "</option>";
			}
			$cout .= "</select> &nbsp; <input type='button' value='SUBMIT' id='punch_clock_location_submit' />";*/

			$sql = "INSERT INTO shift_clock (location_id, shift_date, ssn_nid, clock_in) VALUES (" . $_SESSION['user']->getLocationID() . ", " . $_SESSION['user']->getShiftDate() . ", '" . $_SESSION['user']->getSSN() . "', NOW())";
			execSQL($sql, true);

		} else {
			foreach ($rs as $r) {
				if ($r[0] == "") { continue; }
				$sql = "UPDATE shift_clock SET clock_out = NOW() WHERE shift_clock_id = " . $r[0];
				execSQL($sql, true);
				break;
			}
		}
		echo $cout;
	}

	//######################################
	//######################################
	clean();
	//######################################
	//######################################
}


?>