<?php $loginScreen = false; ?>
<?php require_once("e.php"); ?>
<?php

if (isset($_REQUEST['xargs']) && $_REQUEST['xargs'] == "NAS2018") {
	if ($_REQUEST['action'] == "LOCATION-PERMISSIONS") {
		$locationArray = array();
		$rowArray = array();

		$cout = "<input type='hidden' name='LOCATION-ACTION' value='LOCATION-PERMISSIONS-MODIFY' />";

		$sql = "SELECT LocationID, LocationCode FROM Locations";
		$rs = execSQL($sql);

		foreach($rs as $r) {
			if ($r[0] == "") { continue; }
			array_push($locationArray, $r);
		}
		
		$sql = "SELECT location_id FROM location_permissions WHERE permissions_id = " . $_REQUEST['pid'];
	    $rs = execSQL($sql);

		foreach($rs as $r) {
			if ($r[0] == "") { continue; }
			array_push($rowArray, $r[0]);
		}

		$cout .= "<table><tr><td style='padding: 7px; text-align: left;' colspan='2'><b>Location<font color='#efefef'>---</font></b> &nbsp; <select id='location' name='location'>";

		foreach ($locationArray as $p) {
			if (!in_array($p[0], $rowArray)) { $cout .= "<option value='" . $p[0] . "'>" . $p[1] . "</option>"; }
		}

		$cout .= "</select> &nbsp; <input type='button' name='location_permissions_add' id='location_permissions_add' value='ADD' /></td></tr>";



		$cout .= "</table>";


		echo $cout;

	}

	//######################################
	//######################################
	clean();
	//######################################
	//######################################
}


?>