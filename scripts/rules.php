<?php $loginScreen = false; ?>
<?php require_once("e.php"); ?>
<?php

if (isset($_REQUEST['xargs']) && $_REQUEST['xargs'] == "NAS2018") {
	if ($_REQUEST['action'] == "RULES") {
		$journalData = false;
		$rowArray = array();

		$cout = "<center><form action='/scripts/x.php?xargs=NAS2018&action=RULES-MODIFY' method='post' name='rules_form'><table width='725' border='0'><tr><td class='navtitle' colspan='2'>National Aviation Services Rules Engine</td></tr>";

		
		$sql = "SELECT r.rules_engine_id, CONCAT(e.FirstName, \" \", e.LastName) AS Employee, r.rule_name, r.rule_description, r.created_date FROM rules_engine r, Employees e WHERE r.employee_id = e.EmployeeID AND r.rules_engine_id = " . $_REQUEST['rid'];
	    $rs = execSQL($sql);

		foreach($rs as $r) {
			if ($r[0] == "") { continue; }
			$rowArray = $r;
		}

		$sql = "SELECT COUNT(*) AS Count FROM rules_engine_journal WHERE rules_engine_id = " . $_REQUEST['rid'];
		$rs = execSQL($sql);

		foreach($rs as $r) {
			if ($r[0] == "") { continue; }
			if (intval($r['Count']) > 0) { $journalData = true; }
		}

		$cout .= "<input id='rid' type='hidden' name='rid' value='" . $rowArray[0] . "' />";

		$cout .= "<tr><td style='padding: 7px; text-align: left;' width='95'><b>Name:</b></td><td style='padding: 7px; text-align: left;'>" . $rowArray[2] . "</td></tr>";
		$cout .= "<tr><td  colspan='2' style='padding: 7px; text-align: left;'><b>Description:</b><br /><textarea name='rule_description' rows='4' cols='100' required='required'>" . $rowArray[3] . "</textarea></td></tr>";

		$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Employee:</b></td><td style='padding: 7px; text-align: left;'>" . $rowArray[1] . "</td></tr>";

		$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Created Date:</b></td><td style='padding: 7px; text-align: left;'>" . $rowArray[4] . "</td></tr>";
		$cout .= "<tr></tr>";

		$cout .= "<tr><td colspan='2' align='right'>";

		if ($journalData) { $cout .= "<input id='rules_journal' type='button' value='JOURNAL' /> &nbsp;"; }

		$cout .="<input id='rules_modify_cancel' type='button' value='CANCEL' /> &nbsp; <input id='rules_submit' type='submit' value='SUBMIT' /></td></tr>";
		$cout .= "</table></form><p>&nbsp;</p><div id='rules_journal_div'></div></center>";
		echo $cout;

	}

	if ($_REQUEST['action'] == "JOURNAL") {

		$cout = "<center><table width='725' border='0' style='border-collapse: collapse; border: 1px solid black;'><tr><td class='navtitle' colspan='2'>National Aviation Services Rules Engine Journal</td></tr>";

		$sql = "SELECT CONCAT(e.FirstName, \" \", e.LastName) AS Employee, j.base_rule, j.delta_rule, j.modified_date FROM rules_engine_journal j, Employees e WHERE j.employee_id = e.EmployeeID AND j.rules_engine_id = " . $_REQUEST['rid'];
	    $rs = execSQL($sql);

		foreach($rs as $r) {
			if ($r[0] == "") { continue; }

			$cout .= "<tr><td style='border: 1px solid black; padding: 7px; text-align: left;' width='15%'><b>Base Rule</b></td><td style='border: 1px solid black; padding: 7px; text-align: left;'>" . $r[1] . "</td></tr>";
			$cout .= "<tr><td style='border: 1px solid black; padding: 7px; text-align: left;'><b>Delta Rule</b></td><td style='border: 1px solid black; padding: 7px; text-align: left;'>" . $r[2] . "</td></tr>";
			$cout .= "<tr><td style='border: 1px solid black; padding: 7px; text-align: left;'><b>Employee</b></td><td style='border: 1px solid black; padding: 7px; text-align: left;'>" . $r[0] . "</td></tr>";
			$cout .= "<tr><td style='border: 1px solid black; padding: 7px; text-align: left;'><b>Modified</b></td><td style='border: 1px solid black; padding: 7px; text-align: left;'>" . $r[3] . "</td></tr>";

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