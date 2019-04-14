<?php $loginScreen = false; ?>
<?php require_once("e.php"); ?>
<?php

if (isset($_REQUEST['xargs']) && $_REQUEST['xargs'] == "NAS2018") {
	if ($_REQUEST['action'] == "POSITION-PERMISSIONS") {
		$positionArray = array();
		$rowArray = array();

		$cout = "<input type='hidden' name='POSITION-ACTION' value='POSITION-PERMISSIONS-MODIFY' />";

		$sql = "SELECT PositionID, Description FROM Positions ORDER BY Description";
		$rs = execSQL($sql);

		foreach($rs as $r) {
			if ($r[0] == "") { continue; }
			array_push($positionArray, $r);
		}
		
		$sql = "SELECT position_id FROM position_permissions WHERE permissions_id = " . $_REQUEST['pid'];
	    $rs = execSQL($sql);

		foreach($rs as $r) {
			if ($r[0] == "") { continue; }
			array_push($rowArray, $r[0]);
		}

		$cout .= "<table><tr><td style='padding: 7px; text-align: left;' colspan='2'><b>Position<font color='#efefef'>--</font></b> &nbsp;&nbsp;&nbsp; <select id='position' name='position'>";

		foreach ($positionArray as $p) {
			if (!in_array($p[0], $rowArray)) { $cout .= "<option value='" . $p[0] . "'>" . $p[1] . "</option>"; }
		}

		$cout .= "</select> &nbsp; <input type='button' name='position_permissions_add' id='position_permissions_add' value='ADD' /></td></tr>";



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