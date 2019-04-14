<?php $loginScreen = false; ?>
<?php require_once("e.php"); ?>
<?php

if (isset($_REQUEST['xargs']) && $_REQUEST['xargs'] == "NAS2018") {

	if ($_REQUEST['action'] == "LOCATIONS") {
		$nsLocationCodeArray = array();
		$locationCodeArray = array();
		$directorArray = array();
		$managerArray = array();
		$stateArray = array();
		$rowArray = array();

		$cout = "<center><form action='/scripts/x.php?xargs=NAS2018&action=LOCATIONS-MODIFY' method='post' name='location_mapping_form'><table width='725' border='0'><tr><td class='navtitle' colspan='2'>National Aviation Services Location Mapping</td></tr>";

		$sql = "SELECT a.ArptRsrvCntrID, a.Name, a.AirportCode, tl.TopLevelID, tl.Manager AS 'TLManager', e.LastName AS 'TLLastName', e.FirstName AS 'TLFirstName', s.StateID, s.StateAbbrv, l.LocationID, l.Manager AS 'LManager', (SELECT CONCAT(e.FirstName, \" \", e.LastName) AS LManagerName FROM Employees e WHERE EmployeeID = l.Manager) AS 'LManagerName', l.Director AS 'Director', (SELECT CONCAT(e.FirstName, \" \", e.LastName) AS DirectorName FROM Employees e WHERE EmployeeID = l.Director) AS 'DirectorName', l.LocationCode, l.DTLastModified, (SELECT ns_location_id FROM ns_locations WHERE ns_location_id = (SELECT ns_location_id FROM location_mapping WHERE LocationID = l.LocationID)) AS 'NSLocationID', l.Burden FROM ArptRsrvCntr a, TopLevelLocation tl, States s, Locations l, Employees e WHERE a.ArptRsrvCntrID = tl.ArptRsrvCntrID AND tl.TopLevelID = l.TopLevelID AND s.StateID = tl.StateID AND tl.Manager = e.EmployeeID AND l.LocationCode = '" . formatQuotes($_REQUEST['locationCode']) . "';";
	    $rs = execSQL($sql);

		foreach($rs as $r) {
			if ($r[0] == "") { continue; }
			$rowArray = $r;
		}

		$sql = "SELECT ns_location_id, name FROM ns_locations";
		$nsLocationCodeArray = execSQL($sql);

		$sql = "SELECT EmployeeID, FirstName, LastName FROM Employees WHERE EmployeeID IN (SELECT EmployeeID From Employments WHERE EmploymentID IN (SELECT EmploymentID FROM EmploymentStatus WHERE PositionID IN (SELECT PositionID FROM Positions WHERE IsRegionalMgr = 1 OR IsManager = 1)))";
		$managerArray = execSQL($sql);

		$sql = "SELECT EmployeeID, FirstName, LastName FROM Employees WHERE EmployeeID IN (SELECT EmployeeID FROM Employments WHERE EmploymentID IN (SELECT EmploymentID From EmploymentStatus Where Active = 1 AND PositionID IN ('16', '49', '70', '71')));";;
		$directorArray = execSQL($sql);

		$sql = "SELECT StateID, StateAbbrv FROM States ORDER BY StateAbbrv";
		$stateArray = execSQL($sql);

		$cout .= "<input id='ArptRsrvCntrID' type='hidden' name='ArptRsrvCntrID' value='" . $rowArray[0] . "' />";
		$cout .= "<input id='LocationID' type='hidden' name='LocationID' value='" . $rowArray[9] . "' />";
		$cout .= "<input id='TopLevelID' type='hidden' name='TopLevelID' value='" . $rowArray[3] . "' />";

		$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Airport Name</b></td><td style='padding: 7px; text-align: left;'><input id='Name' name='Name' type='text' size='62' value='" . $rowArray[1] . "' /></td></tr>";
		$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Airport Code</b></td><td style='padding: 7px; text-align: left;'><input id='AirportCode' name='AirportCode' type='text' size='7' value='" . $rowArray[2] . "' /></td></tr>";

		$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Director</b></td><td style='padding: 7px; text-align: left;'><select id='Director' name='Director'>";
		foreach ($directorArray as $m) {
			$selected = "";
			if ($m[0] == $rowArray['Director']) { $selected = " selected"; }
			$cout .= "<option" . $selected . " value='" . $m[0] . "'>" . $m[1] . " " . $m[2] . "</option>";
		}
		$cout .= "</select></td></tr>";

		$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Top Level Manager</b></td><td style='padding: 7px; text-align: left;'><select id='TLManager' name='TLManager'>";
		foreach ($managerArray as $m) {
			$selected = "";
			if ($m[0] == $rowArray[4]) { $selected = " selected"; }
			$cout .= "<option" . $selected . " value='" . $m[0] . "'>" . $m[1] . " " . $m[2] . "</option>";
		}
		$cout .= "</select></td></tr>";

		$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Station Manager</b></td><td style='padding: 7px; text-align: left;'><select id='LManager' name='LManager'>";
		foreach ($managerArray as $m) {
			$selected = "";
			if ($m[0] == $rowArray['LManager']) { $selected = " selected"; }
			$cout .= "<option" . $selected . " value='" . $m[0] . "'>" . $m[1] . " " . $m[2] . "</option>";
		}
		$cout .= "</select></td></tr>";

		$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>State</b></td><td style='padding: 7px; text-align: left;'><select id='StateID' name='StateID'>";
		foreach ($stateArray as $s) {
			$selected = "";
			if ($s[0] == $rowArray[7]) { $selected = " selected"; }
			$cout .= "<option" . $selected . " value='" . $s[0] . "'>" . $s[1] . "</option>";
		}
		$cout .= "</select></td></tr>";

		$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Location Code</b></td><td style='padding: 7px; text-align: left;'><input id='LocationCode' name='LocationCode' type='text' size='7' value='" . $rowArray['LocationCode'] . "' /></td></tr>";


		$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>NetSuite Location Code</b></td><td style='padding: 7px; text-align: left;'><select id='ns_location_id' name='ns_location_id'>";
		foreach ($nsLocationCodeArray as $n) {
			$selected = "";
			if ($n[0] == $rowArray['NSLocationID']) { $selected = " selected"; }
			$cout .= "<option" . $selected . " value='" . $n[0] . "'>" . $n[1] . "</option>";
		}
		$cout .= "</select></td></tr>";

		$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Burden %</b></td><td style='padding: 7px; text-align: left;'><input id='Burden' name='Burden' type='text' size='10' value='" . $rowArray['Burden'] . "' />%</td></tr>";

		$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Last Modified</b></td><td style='padding: 7px; text-align: left;'>" . $rowArray['DTLastModified'] . "</td></tr>";
		$cout .= "<tr></tr>";

		$cout .= "<tr><td colspan='2' align='right'><input id='location_cancel' type='button' value='RESET' /> &nbsp; <input id='location_submit' type='submit' value='SUBMIT' /></td></tr>";

		$cout .= "<input type='hidden' name='DTLastModified' id='DTLastModified' value='" . $rowArray['DTLastModified'] . "' />";
		$cout .= "</table></form></center>";
		echo $cout;

	}
	
	//######################################
	//######################################
	clean();
	//######################################
	//######################################
}

?>