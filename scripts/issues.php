<?php $loginScreen = false; ?>
<?php require_once("e.php"); ?>
<?php

if (isset($_REQUEST['xargs']) && $_REQUEST['xargs'] == "NAS2018") {
	if ($_REQUEST['action'] == "ISSUES") {
		$journalData = false;
		$rowArray = array();

		$cout = "<center><form action='/scripts/x.php?xargs=NAS2018&action=ISSUES-MODIFY' method='post' name='issues_form'><table border='0' width='725'><tr><td class='navtitle' colspan='2'>National Aviation Services Issues Tracker</td></tr>";

		
		$sql = "SELECT i.issues_id, CONCAT(e.FirstName, \" \", e.LastName) AS Employee, i.description, i.created_date, i.active FROM issues i, Employees e WHERE i.employee_id = e.EmployeeID AND i.issues_id = " . $_REQUEST['iid'];
	    $rs = execSQL($sql);

		foreach($rs as $r) {
			if ($r[0] == "") { continue; }
			$rowArray = $r;
		}

		$sql = "SELECT COUNT(*) AS Count FROM issues_journal WHERE issues_id = " . $_REQUEST['iid'];
		$rs = execSQL($sql);

		foreach($rs as $r) {
			if ($r[0] == "") { continue; }
			if (intval($r['Count']) > 0) { $journalData = true; }
		}

		$cout .= "<input id='iid' type='hidden' name='iid' value='" . $rowArray[0] . "' />";

		$cout .= "<tr><td style='padding: 7px; text-align: left;' width='95'><b>Description:</b></td><td style='padding: 7px; text-align: left;'>" . $rowArray[2] . "</td></tr>";

		$cout .= "<tr><td  colspan='2' style='padding: 7px; text-align: left;'><b>Update:</b><br /><textarea name='description' rows='4' cols='100' required='required'>" . $rowArray[2] . "</textarea></td></tr>";

		if ($rowArray[4] == "0") {
			$cout .= "<tr><td style='padding: 7px; text-align: left;' width='95'><b>Closed:</b></td><td style='padding: 7px; text-align: left;'>Yes</td></tr>";
		} else {
			$cout .= "<tr><td style='padding: 7px; text-align: left;' width='95'><b>Closed:</b></td><td style='padding: 7px; text-align: left;'><input type='checkbox' name='active' /></td></tr>";
		}

		$cout .= "<tr><td style='padding: 7px; text-align: left;' width='95'><b>Employee:</b></td><td style='padding: 7px; text-align: left;'>" . $rowArray[1] . "</td></tr>";

		$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Created Date:</b></td><td style='padding: 7px; text-align: left;'>" . $rowArray[3] . "</td></tr>";
		$cout .= "<tr></tr><tr><td colspan='2' align='right'>";

		if ($journalData) { $cout .= "<input id='issues_journal' type='button' value='JOURNAL' /> &nbsp;"; }

		$cout .="<input id='issues_modify_cancel' type='button' value='CANCEL' /> &nbsp; ";
		if ($rowArray[4] == "1") { $cout .= "<input id='issues_submit' type='submit' value='SUBMIT' />"; }

		$cout .= "</td></tr></table></form><p>&nbsp;</p><div id='issues_journal_div'></div></center>";
		echo $cout;

	}

	if ($_REQUEST['action'] == "JOURNAL") {

		$cout = "<center><table width='725' border='0' style='border-collapse: collapse; border: 1px solid black;'><tr><td class='navtitle' colspan='2'>National Aviation Services Issues Tracker Journal</td></tr>";

		$sql = "SELECT CONCAT(e.FirstName, \" \", e.LastName) AS Employee, j.description, j.modified_date FROM issues_journal j, Employees e WHERE j.employee_id = e.EmployeeID AND j.issues_id = " . $_REQUEST['iid'];
	    $rs = execSQL($sql);

		foreach($rs as $r) {
			if ($r[0] == "") { continue; }

			$cout .= "<tr><td style='border: 1px solid black; padding: 7px; text-align: left;' width='15%'><b>Description</b></td><td style='border: 1px solid black; padding: 7px; text-align: left;'>" . $r[1] . "</td></tr>";
			$cout .= "<tr><td style='border: 1px solid black; padding: 7px; text-align: left;'><b>Employee</b></td><td style='border: 1px solid black; padding: 7px; text-align: left;'>" . $r[0] . "</td></tr>";
			$cout .= "<tr><td style='border: 1px solid black; padding: 7px; text-align: left;'><b>Modified</b></td><td style='border: 1px solid black; padding: 7px; text-align: left;'>" . $r[2] . "</td></tr>";

			$cout .= "<tr bgcolor='#e6f2ff'><td colspan='2'><p>&nbsp;</p></tr>";
		}

		$cout .= "</table></center>";
		echo $cout;
	}
	
	//######################################
	//######################################
	clean();
	//######################################
	//######################################
}

?>