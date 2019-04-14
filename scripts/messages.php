<?php $loginScreen = false; ?>
<?php require_once("e.php"); ?>
<?php

if (isset($_REQUEST['xargs']) && $_REQUEST['xargs'] == "NAS2018") {
	if ($_REQUEST['action'] == "UNLOCK") {
		$sql = "UPDATE wf_processes SET locked = 0, started_date = NULL WHERE wf_request_id = " . $_REQUEST['r'];
		execSQL($sql,true);

		$sql = "UPDATE wf_processes SET employee_id = NULL WHERE wf_process_id = " . $_REQUEST['p'];
		execSQL($sql);
	}

	if ($_REQUEST['action'] == "LOCK") {
		$sql = "UPDATE wf_processes SET locked = 1, started_date = NOW() WHERE wf_request_id = " . $_REQUEST['r'];
		execSQL($sql,true);

		$sql = "UPDATE wf_processes SET employee_id = '" . $_SESSION['user']->getEmployeeID() . "' WHERE wf_process_id = " . $_REQUEST['p'];
		execSQL($sql);
	}

	if ($_REQUEST['action'] == "MESSAGES") {
		$rowArray = array();
		$wfProcessID = null;
		$updateLock = false;
		$actionMode = false;
		$unlocked = false;
		$pk = null;

		$sql = "SELECT wf_process_id, employee_id, closed_date FROM wf_processes WHERE wf_request_id = " . $_REQUEST['messageID'];
		$rs = execSQL($sql);

		foreach ($rs as $r) {
			if ($r[0] == "") { continue; }
			if ($r['closed_date'] == "") { $unlocked = true; }
			if ($r['employee_id'] == $_SESSION['user']->getEmployeeID()) { $wfProcessID = $r['wf_process_id']; }
		}

		if ($unlocked) {
			if (securityCheck("edit_hr_messages", false, true)) {
				$actionMode = true;
				$sql = "UPDATE wf_processes SET employee_id = '" . $_SESSION['user']->getEmployeeID() . "' WHERE wf_request_id = " . $_REQUEST['messageID'] . " AND hr_action = 1";
				execSQL($sql, true);

				$sql = "UPDATE wf_processes SET locked = 1, started_date = NOW() WHERE wf_request_id = " . $_REQUEST['messageID'];
				execSQL($sql, true);
			}
		} else {
			if (securityCheck("edit_hr_messages", false, true)) {
				$actionMode = true;
				$sql = "SELECT employee_id FROM wf_processes WHERE wf_request_id = " . $_REQUEST['messageID'] . " AND hr_action = 1";
				$rs = execSQL($sql);

				foreach ($rs as $r) {
					if ($r[0] == "") { continue; }
					if ($r['employee_id'] == $_SESSION['user']->getEmployeeID()) { $updateLock = true; }
				}
			}
		}

		$cout = "<center><form action='/scripts/x.php?xargs=NAS2018&action=MESSAGES-MODIFY' method='post' name='messages_form'><table border='0' width='1067'><tr><td class='navtitle' colspan='2'>National Aviation Services Workflow Request</td></tr>";

		$sql = "SELECT wc.description AS class_description, CONCAT(e.FirstName, \" \", e.LastName) AS employee, w.description, w.created_date, w.table_name, w.table_id, w.location, w.effective_date, w.notes FROM wf_requests w, Employees e, wf_classes wc WHERE w.wf_request_id = " . $_REQUEST['messageID'] . " AND wc.wf_class_id = w.wf_class_id AND e.EmployeeID = w.employee_id";
	    $rs = execSQL($sql);

		foreach($rs as $r) {
			if ($r[0] == "") { continue; }
			$rowArray = $r;
		}

		$sql = "SHOW KEYS FROM " . $rowArray['table_name'] . " WHERE Key_name = 'PRIMARY'";
		$rs = execSQL($sql);

		foreach ($rs as $r) {
			if ($r[0] == "") { continue; }
			$pk = $r['Column_name'];
		}

		$cout .= "<tr><td colspan='2' bgcolor='#ffc300' style='text-align: center;'><b>REQUESTING EMPLOYEE INFORMATION</b></td></tr>";
		$cout .= "<tr><td width='170' style='padding: 7px; text-align: left;'><b>Request type:</b></td><td style='padding: 7px; text-align: left;'>" . $rowArray['class_description'] . "</td></tr>";
		$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Employee:</b></td><td style='padding: 7px; text-align: left;'>" . $rowArray['employee'] . "</td></tr>";
		$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Created date:</b></td><td style='padding: 7px; text-align: left;'>" . $rowArray['created_date'] . "</td></tr>";


		//#################################################################
		//#################################################################
		// TIME OFF REQUESTS
		//#################################################################
		//#################################################################

		if ($rowArray['table_name'] == "time_off_requests") {

			$sql = "SELECT Date(start_date) AS start_date, Date(end_date) AS end_date, Hours, Notes FROM time_off_requests WHERE " . $pk . " = " . $rowArray['table_id'];
			$rs = execSQL($sql);

			foreach ($rs as $r) {
				if ($r[0] == "") { continue; }
				$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Start date:</b></td><td style='padding: 7px; text-align: left;'>" . $r['start_date'] . "</td></tr>";
				$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>End date:</b></td><td style='padding: 7px; text-align: left;'>" . $r['end_date'] . "</td></tr>";
				$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Hours:</b></td><td style='padding: 7px; text-align: left;'>" . $r['Hours'] . "</td></tr>";
			}

			$cout .= "<tr><td style='padding: 7px; text-align: left;' colspan='2'><b>Notes:</b><br /><textarea rows='4' cols='150' name='notes' id='notes'>" . $rowArray['notes'] . "</textarea></td></tr>";

			$cout .= "<tr></tr><tr><td colspan='2' align='right'><input id='messages_cancel' type='button' value='RESET' style='height:30px;width:150px;display:inline;margin:0 auto;' /> &nbsp; ";
			if ($actionMode) { $cout .= "<input id='messages_submit' type='submit' value='APPROVE' style='height:30px;width:150px;display:inline;margin:0 auto;' /></td></tr>"; }


		//#################################################################
		//#################################################################
		// EMPLOYEE WARNINGS
		//#################################################################
		//#################################################################

		} else if ($rowArray['table_name'] == "Warnings") {
			$sql = "SELECT w.WarningsID, w.EmployeeID, CONCAT(e.FirstName, \" \", e.LastName) AS EmployeeName, w.DateOfRecord, w.DateRecvd, w.WarningType_Primary, w.WarningType_Secondary, w.WarningType_Tertiary, w.WarningStyleID, w.Attachments, w.Notes, w.Reply FROM Warnings w, Employees e WHERE w.EmployeeID = e.EmployeeID AND w.WarningsID = " . $rowArray['table_id'];
			$rs = execSQL($sql);

			foreach ($rs as $r) {
				if ($r[0] == "") { continue; }
				if ($r['EmployeeID'] == $_SESSION['user']->getEmployeeID()) {
					$actionMode = true;
					$sql = "UPDATE wf_processes SET started_date = CURDATE(), closed_date = CURDATE(), approved = 1 WHERE wf_request_id = " . $_REQUEST['messageID'];
					execSQL($sql,true);

					$sql = "INSERT INTO wf_actions (wf_process_id, employee_id, action, action_date) VALUES (" . $wfProcessID. ", '" . $_SESSION['user']->getEmployeeID() . "', 'COMPLETED', NOW())";
					execSQL($sql,true);
				}

				$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Employee name:</b></td><td style='padding: 7px; text-align: left;'>" . $r['EmployeeName'] . "</td></tr>";
				$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Date of record:</b></td><td style='padding: 7px; text-align: left;'>" . explode(" ", $r['DateOfRecord'])[0] . "</td></tr>";
				$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Date received:</b></td><td style='padding: 7px; text-align: left;'>" . explode(" ", $r['DateRecvd'])[0] . "</td></tr>";

				$cout .= "<tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'><b>Warning Style:</b></font></td><td style='padding: 7px; text-align: left;'>";

			  	$sql = "SELECT WarningStyleID, WarningStyle FROM WarningStyles";
			  	$rs1 = execSQL($sql);

			  	foreach ($rs1 as $r1) {
			    	if ($r1[0] == "") { continue; }
			    	if ($r1['WarningStyleID'] == $r['WarningStyleID']) { $cout .= $r1['WarningStyle']; break; }
			  	}

			 	$cout .= "</td></tr><tr><tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'><b>Primary Warning Type:</b></font></td><td style='padding: 7px; text-align: left;'>";

			  	$sql = "SELECT WarningTypeID, WarningType FROM WarningTypes WHERE ActiveFlag = 1";
			  	$rs1 = execSQL($sql);

			  	foreach ($rs1 as $r1) {
			    	if ($r1[0] == "") { continue; }
			    	if ($r1['WarningTypeID'] == $r['WarningType_Primary']) { $cout .= $r1['WarningType']; break; }
			  	}

  				$cout .= "</td></tr><tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'><b>Secondary Warning Type:</b></font></td><td style='padding: 7px; text-align: left;'>";

			  	foreach ($rs1 as $r1) {
			    	if ($r1[0] == "") { continue; }
			    	if ($r1['WarningTypeID'] == $r['WarningType_Secondary']) { $cout .= $r1['WarningType']; break; }
			  	}

			  	$cout .= "</td></tr><tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'><b>Tertiary Warning Type:</b></font></td><td style='padding: 7px; text-align: left;'>";

				foreach ($rs1 as $r1) {
				    if ($r1[0] == "") { continue; }
				    if ($r1['WarningTypeID'] == $r['WarningType_Tertiary']) { $cout .= $r1['WarningTypeID']; break; }
				}

				$cout .= "</td></tr><tr><td style='padding: 7px; text-align: left;'><b>Warning notes:</b></td><td style='padding: 7px; text-align: left;'>" . $r['Notes'] . "</td></tr>";

				if ($actionMode) {
					$cout .= "<tr><td style='padding: 7px; text-align: left;' colspan='2'><font size='2' face='Arial'><b>Employee response:</b><br /><textarea rows='5' cols='150' name='Reply' required>" . $r['Reply'] . "</textarea></td></tr>";
				} else {
					$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Employee response:</b></td><td style='padding: 7px; text-align: left;'>" . $r['Reply'] . "</td></tr>";
				}

				$cout .= "<tr><td colspan='2' align='right'><input id='messages_cancel' type='button' value='RESET' style='height:30px;width:150px;display:inline;margin:0 auto;' /> &nbsp; ";
				if ($actionMode) {
					$cout .= "<input id='messages_submit' type='submit' value='UPDATE' style='height:30px;width:150px;display:inline;margin:0 auto;' />";
					$cout .= "<input type='hidden' name='WarningsID' value='" . $r['WarningsID'] . "' />";
				}
				$cout .= "</td></tr>";
			}


		//#################################################################
		//#################################################################
		// EMPLOYEE TERMINATIONS
		//#################################################################
		//#################################################################

		} else if ($rowArray['table_name'] == "terminations") {
			$sql = "SELECT (SELECT CONCAT(FirstName, \" \", LastName) FROM Employees WHERE EmployeeID = employee_id) AS EmployeeName, reason, (SELECT TerminationType FROM TerminationTypes where TerminationTypeID = type_id) AS TerminationType, type_id, termination_date, description, hr_reason, (SELECT TerminationType FROM TerminationTypes where TerminationTypeID = hr_type_id) AS HRTerminationType, hr_type_id, hr_description, trinet FROM terminations WHERE termination_id = " . $rowArray['table_id'];
			$rs = execSQL($sql);

			$cout .= "<tr><td colpsan='2'><p>&nbsp;</p></td></tr><tr><td colspan='2' bgcolor='#ffc300' style='text-align: center;'><b>TERMINATED EMPLOYEE INFORMATION</b></td></tr>";

			foreach ($rs as $r) {
				if ($r[0] == "") { continue; }
				$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Employee name:</b></td><td style='padding: 7px; text-align: left;'>" . $r['EmployeeName'] . "</td></tr>";
				$cout .= "<tr><td style='padding: 7px; text-align; left;'><b>TriNet updated</b></td><td style='padding: 7px; text-align: left;'><input type='checkbox' name='trinet'";

				if ($r['trinet'] == 1) { $cout .= " checked"; }
				$cout .= " /></td></tr>";

				$reason = $r['hr_reason'];
				if ($reason == "") { $reason = $r['reason']; }

				$typeID = $r['hr_type_id'];
				if ($typeID == "") { $typeID = $r['type_id']; }

				$description = $r['hr_description'];
				if ($description == "") { $description = $r['description']; }

				$cout .= "<tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'><b>Termination reason:</b></font></td><td style='padding: 7px; text-align: left;'><select id='reason' name='reason'><option value='voluntary'";

				if ($reason == "voluntary") { $cout .= " selected"; }
				$cout .= ">Voluntary</option><option value='involuntary'";

				if ($reason == "involuntary") { $cout .= " selected"; }
				$cout .= ">Involuntary</option></select></td></tr>";

				$cout .= "<tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'><b>Termination type:</b></font></td><td style='padding: 7px; text-align: left;'><div id='type_id_div'><select id='type_id' name='type_id'>";

				$sql = "SELECT TerminationTypeID, TerminationType, Voluntary FROM TerminationTypes ORDER BY TerminationType";
				$rs1 = execSQL($sql);

				foreach ($rs1 as $r1) {
				    if ($r1[0] == "") { continue; }
				    if ($r1['Voluntary'] == 0 && $reason == "voluntary") { continue; }
				    $cout .= "<option value='" . $r1['TerminationTypeID'] . "'";

				    if ($typeID == $r1['TerminationTypeID']) { $cout .= " selected"; }
				    $cout .= ">" . $r1['TerminationType'] . "</option>";
				}

				$cout .= "</select></div></td></tr><tr id='termination_description_tr'><td style='padding: 7px; text-align: left;' colspan='2'><font size='2' face='Arial'><b>Termination description:</b><br /><textarea rows='5' cols='150' name='description' required>" . $description . "</textarea></td></tr>";

				$cout .= "<tr></tr><tr><td colspan='2' align='right'>";

				if ($unlocked && $actionMode) { $cout .= "<input id='messages_unlock' type='button' value='UNLOCK' style='height:30px;width:150px;display:inline;margin:0 auto;' /> &nbsp; "; }
				$cout .= "<input id='messages_cancel' type='button' value='RESET' style='height:30px;width:150px;display:inline;margin:0 auto;' /> &nbsp; ";
				if ($updateLock && $actionMode) { $cout .= "<input id='messages_submit' type='submit' value='UPDATE' style='height:30px;width:150px;display:inline;margin:0 auto;' />"; }

				$sql = "SELECT wf_process_id FROM wf_processes WHERE wf_request_id = " . $_REQUEST['messageID'] . " AND hr_action = 1";
				$rs = execSQL($sql);

				foreach ($rs as $r) {
					if ($r[0] == "") { continue; }
					$cout .= "<input type='hidden' name='wf_process_id' id='wf_process_id' value='" . $r[0] . "' />";
				}

				$cout .= "<input type='hidden' name='termination_id' value='" . $rowArray['table_id'] . "' /><input type='hidden' value='" . $_REQUEST['messageID'] . "' name='id' id='id' /></td></tr>";
			}


		//#################################################################
		//#################################################################
		// SHIFT REPORT OVERRIDES
		//#################################################################
		//#################################################################
		} else if ($rowArray['table_name'] == "shift_report") {
			if ($_SESSION['user']->getEmployeeID() == $operationsEmployeeID) { $actionMode = true; } else { $actionMode = false; }
			$l = null;
			$lc = null;

			$sql = "SELECT DATE(shift_date) as shift_date, location FROM shift_report WHERE shift_report_id = " . $rowArray['table_id'];
			$rs = execSQL($sql);

			foreach ($rs as $r) {
				if ($r[0] == "") { continue; }
				$l = $r['location'];
				$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Shift date:</b></td><td style='padding: 7px; text-align: left;'>" . $r['shift_date'] . "</td></tr>";
				$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Airport:</b></td><td style='padding: 7px; text-align: left;'>" . $l . "</td></tr></table><center>";
			}

			$sql = "SELECT LocationID, LocationCode FROM Locations WHERE SUBSTRING_INDEX(LocationCode, \" \", 1) = '" . $l . "' LIMIT 1";
			$rs = execSQL($sql);

			foreach ($rs as $r) {
				if ($r[0] == "") { continue; }
				$lc = $r['LocationID'];
			}

			$cout .= generateAttendance($lc, false, true, $l);


		//#################################################################
		//#################################################################
		// SHIFT SCHEDULE OVERRIDES
		//#################################################################
		//#################################################################

		} else if (strPos($rowArray['table_name'], "shift_schedule") >= 0) {
			if ($_SESSION['user']->getEmployeeID() == $operationsEmployeeID) { $actionMode = true; } else { $actionMode = false; }

			if ($rowArray['table_name'] == "shift_schedule_future") {
				$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Effective date:</b></td><td style='padding: 7px; text-align: left;'>" . explode(" ", $rowArray['effective_date'])[0] . "</td></tr>";
			} else {
				$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Effective date:</b></td><td style='padding: 7px; text-align: left;'>" . $currentDate . "</td></tr>";
			}

			$cout .= "<tr><td style='padding: 7px; text-align: left;' colspan='2'><b>Notes:</b><br /><textarea rows='4' cols='150' name='notes' id='notes'>" . $rowArray['notes'] . "</textarea></td></tr>";

			$cout .= "<tr></tr><tr><td colspan='2' align='right'><div id='schedule_control_div' style='display:none;'>";

			if ($actionMode) {
				$cout .= "<input id='schedule_approve' type='submit' value='APPROVE' style='height:30px;width:150px;display:inline;margin:0 auto;' /> &nbsp; <input id='schedule_reject' type='button' value='REJECT' style='height:30px;width:150px;display:inline;margin:0 auto;' /> &nbsp; ";
			} else {
				$cout .= "<input id='schedule_update' type='button' value='UPDATE' style='height:30px;width:150px;display:inline;margin:0 auto;' /> &nbsp; ";
			}

			$cout .= "</div><div id='schedule_details_button_div' style='display: inline;'>";
			if ($unlocked) { $cout .= "<input id='schedule_details' type='button' value='DETAILS' style='height:30px;width:150px;display:inline;margin:0 auto;' /> &nbsp; "; }
			$cout .= "</div><input id='messages_cancel' type='button' value='RESET' style='height:30px;width:150px;display:inline;margin:0 auto;' /></td></tr><tr><td colspan='2'><p>&nbsp;</p></td></tr></table><center>";

			$cout .= "<input type='hidden' value='" . $rowArray['location'] . "' id='l' name='l' />";
			$cout .= "<input type='hidden' value='" . explode(" ", $rowArray['effective_date'])[0] . "' id='s' name='s' />";
			$cout .= "<input type='hidden' value='" . $rowArray['table_name'] . "' id='table_name' name='table_name' />";
			$cout .= "<table border='0' width='100%'><tr><td align='center'><div id='schedule_details_div'></div></td></tr>";
		}

		$cout .= "</table></center><input type='hidden' value='" . $_REQUEST['messageID'] . "' name='id' id='id' /></form></center>";
		echo $cout;

	}
	
	//######################################
	//######################################
	clean();
	//######################################
	//######################################
}

?>