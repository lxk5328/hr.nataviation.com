<?php require_once("e.php"); ?>
<?php sessionCheck(); ?>
<?php

require '../include/class.phpmailer.php';
require '../include/class.smtp.php';

//var_dump($_REQUEST);
//die();

if (isset($_REQUEST['xargs']) && $_REQUEST['xargs'] == "NAS2018") {
	if ($_REQUEST['action'] == "LOGIN") {
		$redirectURL = $domainRoot . "index.php?xlogin=" . mt_rand();
		$loginSuccess = false;
		$initialLogin = false;
		$impersonate = false;
		$password = null;
		$step2 = false;
		$userID = null;

		if (isset($_REQUEST['impersonate']) && $_REQUEST['impersonate'] != "") { $impersonate = true; }
    	if (isset($_SESSION['user'])) { unset($_SESSION['user']); }
    	if (isset($_SESSION['passwd'])) { unset($_SESSION['passwd']); }
    	if (isset($_SESSION['initpwd'])) { unset($_SESSION['initpwd']); }
    	if (isset($_SESSION['employee_id'])) { unset($_SESSION['employee_id']); }

    	if (isset($_REQUEST['ONBOARDING']) && intval($_REQUEST['ONBOARDING']) == 1) {
			$employeeID = mysqli_real_escape_string($dbconn,$_REQUEST['employee_id2']);
	    	$password = mysqli_real_escape_string($dbconn,$_REQUEST['passwordt2']);			
		} else {
	    	$employeeID = mysqli_real_escape_string($dbconn,$_REQUEST['employee_id']);
		    $password = mysqli_real_escape_string($dbconn,$_REQUEST['password']);
		}

	    if (is_null($password)) {
	    	$password = mysqli_real_escape_string($dbconn,$_REQUEST['passwordt2']);
	    	$_SESSION['user'] = null;
	    } else {
	    	$_SESSION['user'] = loadUser($employeeID, $password, $impersonate);
	    }

	    if ($_SESSION['user'] == null) {
	    	if (($password == "123456" && !isset($_SESSION['passwd'])) || (isset($_REQUEST['ONBOARDING']) && intval($_REQUEST['ONBOARDING']) == 1)) {
	    		$sql = "SELECT c.employee_name, c.ssn_nid, e.EmployeeID FROM census c, Employees e WHERE LPAD(c.ssn_nid,9,0) = REPLACE(e.SSN,'-','') AND c.ssn_nid = '" . formatSSN($employeeID, true) . "' AND c.status = 'Active';";
	    		$rs = execSQL($sql);

    			if (sizeof($rs) > 1) {
				    foreach($rs as $r) {
						if ($r['EmployeeID'] == "") { continue; }
						$_SESSION['employee_id'] = $r['EmployeeID'];
						$_SESSION['ssn'] = $r['ssn_nid'];

						if (isset($_REQUEST['ONBOARDING']) && intval($_REQUEST['ONBOARDING']) == 1) {
							$secret = password_hash($password, PASSWORD_BCRYPT);
							$sql = "INSERT INTO Users (EmployeeID, Password, LastAccess, ActiveFlag) VALUES ('" . $_SESSION['employee_id'] . "', '" . $secret . "', NOW(), 1);";
							$userID = execSQL($sql, true);

							for ($z = 1; $z < 4; $z++) {
								$secret = password_hash($_REQUEST['a' . $z], PASSWORD_BCRYPT);
								$sql = "INSERT INTO passwd_reset (UserID, q, a) VALUES (" . $userID . ", '" . $_REQUEST['q' . $z] . "', '" . $_REQUEST['a' . $z] . "~~" . $secret . "')";
								execSQL($sql, true);
							}

							$_SESSION['user'] = new User($_SESSION['ssn'], $password);
							$step2 = true;
						} else {
							$sql = "SELECT e.EmployeeID FROM Users u, Employees e, census c WHERE LPAD(c.ssn_nid,9,0) = REPLACE(e.SSN,'-','') AND c.ssn_nid = '" . $employeeID . "' AND u.EmployeeID = e.EmployeeID";
							$rs = execSQL($sql);
							if (sizeof($rs) == 1) { $initialLogin = true; }
						}
					}
				}
			}
		} else {
			$loginSuccess = true;
		}

		if ($loginSuccess) {
			$redirectURL = $domainRoot . "index.php";
			$sql = "SELECT suspension_id FROM suspensions WHERE employee_id = '" . $_SESSION['user']->getEmployeeID() . "'";
			$rs = execSQL($sql);
			if (sizeof($rs) > 1) {
				$sql = "INSERT INTO security_log (employee_id, ip_address, action, result) VALUES ('" . $_SESSION['user']->getEmployeeID() . "', '" . $_SERVER['REMOTE_ADDR'] . "', 'suspension', 'SYSTEM LOGOUT')";
		      	execSQL($sql, true);
			} else {
				$redirectURL = $domainRoot . "default.php";
				loadSecurityMapping();

				$sql = "UPDATE Users SET LastAccess = NOW() WHERE EmployeeID = '" . $_SESSION['user']->getEmployeeID() . "'";
				execSQL($sql,true);
			}
		}

		if ($initialLogin) { $redirectURL = $domainRoot . "index.php?step=1"; }
		if ($step2) { $redirectURL = $domainRoot . "index.php?step=2"; }

	}

	if ($_REQUEST['action'] == "INIT") {
		$password = mysqli_real_escape_string($dbconn,$_REQUEST['password']);
		$secret = password_hash($password, PASSWORD_BCRYPT);
		$sql = "UPDATE Users SET Password = '" . $secret . "', ActiveFlag = 1 WHERE EmployeeID = '" . $_SESSION['employee_id'] . "'";
		execSQL($sql, true);

		for ($z = 1; $z < 4; $z++) {
			$secret = password_hash($_REQUEST['a' . $z], PASSWORD_BCRYPT);
			$sql = "INSERT INTO passwd_reset (UserID, q, a) VALUES ((SELECT UserID FROM Users WHERE EmployeeID = '" . $_SESSION['employee_id'] . "'), '" . $_REQUEST['q' . $z] . "', '" . $_REQUEST['a' . $z] . "~~" . $secret . "')";
			execSQL($sql, true);
		}
		
		$_SESSION['user'] = new User($_SESSION['ssn'], $password);
		$redirectURL = $domainRoot . "default.php";
	}

	if ($_REQUEST['action'] == "ONBOARDING" && intval($_REQUEST['ONBOARDING']) == 2) {
		$redirectURL = $domainRoot . "default.php";
	}

	if ($_REQUEST['action'] == "STATION-LOG") {
		$sql = "SELECT station_log_id FROM station_log WHERE location = '" . $_REQUEST['l'] . "'";
		$rs = execSQL($sql);

		if (sizeof($rs) == 1) {
			$sql = "INSERT INTO station_log (employee_id, location, message, modified_date) VALUES ('" . $_SESSION['user']->getEmployeeID() . "', '" . $_REQUEST['l'] . "', '" . formatQuotes($_REQUEST['message']) . "', NOW())";
			execSQL($sql);
		} else {
			$sql = "UPDATE station_log SET employee_id = '" . $_SESSION['user']->getEmployeeID() . "', message = '" . formatQuotes($_REQUEST['message']) . "', modified_date = NOW() WHERE location = '" . $_REQUEST['l'] . "'";
			execSQL($sql, true);
		}
	}

	if ($_REQUEST['action'] == "SHIFT-REPORT") {
		$redirectURL = $domainRoot . "default.php?xapp=SHIFT-REPORT";
		$l = $_SESSION['user']->getLocation();
		$wfClass = "Shift Override";
		$shiftReportID = null;
		$updateMode = false;
		$override = false;
		$director = null;
		$rowIndex = 0;
		$id = null;

		if (isset($_REQUEST['update_mode'])) { $updateMode = true; }
		$sql = "SELECT shift_report_id FROM shift_report WHERE location = '" . $l . "' AND shift_date = (CURDATE() - 1)";
		$rs = execSQL($sql);

		foreach ($rs as $r) {
			if ($r[0] == "") { continue; }
			$shiftReportID = $r[0];
		}

		if (is_null($shiftReportID)) {
			$sql = "INSERT INTO shift_report (shift_date, created_date, employee_id, location, override, override_description, reported) VALUES ((CURDATE() - 1), NOW(), '" . $_SESSION['user']->getEmployeeID() . "', '" . $l . "', ";

			if (isset($_REQUEST['override_description']) && $_REQUEST['override_description'] != "") {
				$override = true;
				$sql .= "1, '" . formatQuotes($_REQUEST['override_description']) . "', ";
			} else {
				$sql .= "0, NULL, ";
			}

			if (isset($_REQUEST['save'])) { $sql .= "0)"; } else { $sql .= "1)"; }
			$shiftReportID = execSQL($sql, true);
		} else {
			if (isset($_REQUEST['submit'])) {
				$sql = "UPDATE shift_report SET reported = 1 WHERE shift_report_id = " . $shiftReportID;
				execSQL($sql, true);
			}
		}

		if ($updateMode) {
			$sql = "DELETE FROM shift_budget WHERE shift_report_id = " . $shiftReportID;
			execSQL($sql, true);
		}

		if ($_REQUEST['mapping'] == "0") {
			foreach ($_REQUEST['tail_number'] as $t) {
				if (trim($t) == "") { continue; }
				$sql = "INSERT INTO shift_budget (shift_report_id, ns_customer_id, service_id, location, aircraft) VALUES (" . $shiftReportID . ", " . $_REQUEST['customer'][$rowIndex] . ", " . $_REQUEST['service'][$rowIndex] . ", '" . $l . "', '" . $t . "')";
				execSQL($sql, true);
				$rowIndex++;
			}
		} else {
			foreach ($_REQUEST['tail_number'] as $t) {
				if (trim($t) == "") { continue; }
				$sql = "INSERT INTO shift_budget (shift_report_id, ns_customer_id, service_id, location, aircraft) VALUES (" . $shiftReportID . ", 25, 207, '" . $l . "', '" . $t . "')";
				execSQL($sql, true);
			}
		}

		if ($updateMode || isset($_REQUEST['save'])) {
			$sql = "DELETE FROM shift_hours WHERE shift_report_id = " . $shiftReportID;
			execSQL($sql, true);
		}

		$rowIndex = 0;
		if (isset($_REQUEST['staff'])) {
			foreach ($_REQUEST['staff'] as $s) {
				$sql = "INSERT INTO shift_hours (shift_report_id, location_id, ssn_nid, initial_hours, approved_hours, created_date) VALUES (" . $shiftReportID . ", " . $_REQUEST['location_code'][$rowIndex] . ", '" . $s . "', '" . $_REQUEST['actual'][$rowIndex] . "', '" . $_REQUEST['actual'][$rowIndex] . "', NOW())";
				execSQL($sql, true);

				if (checkClockLimit($_REQUEST['actual'][$rowIndex])) { $override = true; }
				$rowIndex++;
			}

			if (isset($_REQUEST['submit'])) {
				$sql = "UPDATE shift_clock SET reported = 1, clock_out = IF(clock_out IS NULL, NOW(), clock_out) WHERE reported = 0 AND shift_date = (CURDATE() - 1) AND location_id IN (SELECT LocationID FROM Locations WHERE SUBSTRING_INDEX(LocationCode, \" \", 1) LIKE '" . $l . "%')";
				execSQL($sql, true);
			}

			if (isset($_REQUEST['override'])) { $override = true; }
			if ($updateMode) { $override = false; }

			if ($override) {
				$sql = "INSERT INTO wf_requests (wf_class_id, employee_id, description, created_date, table_name, table_id) VALUES ((SELECT wf_class_id FROM wf_classes WHERE description = '" . $wfClass . "'), '" . $_SESSION['user']->getEmployeeID() . "', 'Shift Override', NOW(), 'shift_report', " . $shiftReportID . ")";
				$id = execSQL($sql, true);

				$sql = "SELECT Director FROM Locations WHERE LocationCode LIKE '" . $l . "%'";
				$rs = execSQL($sql);
				foreach ($rs as $r) {
					if ($r[0] == "") { continue; }
					$director = $r[0];
				}

				$sql = "INSERT INTO wf_processes(wf_request_id, employee_id, required_action, notification, created_date) VALUES (" . $id . ", '" . $director . "', 'Shift override: (" . $l . ")', 1, NOW())";
				execSQL($sql, true);

				if ($_SESSION['user']->getSupervisorID() != $director) {
					$sql = "INSERT INTO wf_processes(wf_request_id, employee_id, required_action, notification, created_date) VALUES (" . $id . ", '" . $_SESSION['user']->getSupervisorID() . "', 'Shift override: (" . $l . ")', 1, NOW())";
					execSQL($sql, true);
				}

				$sql = "INSERT INTO wf_processes (wf_request_id, employee_id, required_action, notification, created_date) VALUES (" . $id . ", '" . $operationsEmployeeID . "', 'Shift override: (" . $l . ")', 1, NOW())";
				execSQL($sql, true);

				$sql = "INSERT INTO wf_processes(wf_request_id, employee_id, required_action, notification, created_date) VALUES (" . $id . ", '" . $_SESSION['user']->getEmployeeID() . "', 'Shift override: (" . $l . ")', 1, NOW())";
				execSQL($sql, true);
			}
		}
	}

	if ($_REQUEST['action'] == "INTERACTIONS") {
		$redirectURL = $domainRoot . "interactions.php?xapp-db=" . mt_rand() . "&xapp=" . $_REQUEST['interaction_type'];
		$sql = "INSERT INTO employee_interactions (interaction_type_id, employee_id, description, created_date) VALUES (";

		if ($_REQUEST['interaction_type'] == "SUGGESTION") {
			$sql .= "1, ";
		} else if ($_REQUEST['interaction_type'] == "IT") {
			$sql .= "2, ";
		} else {
			$sql .= "3, ";
		}

		$sql .= "'" . $_SESSION['user']->getEmployeeID() . "', '" . formatQuotes($_REQUEST['description']) . "', NOW())";
		execSQL($sql, true);
	}

	if ($_REQUEST['action'] == "TERMINATE") {
		$redirectURL = "/default.php?xapp=TERMINATION";
		$wfClass = "Employee Separation";
		$employeeName = null;
		$id = null;

		$sql = "SELECT CONCAT(FirstName, \" \", LastName) AS EmployeeName FROM Employees WHERE EmployeeID = '" . $_REQUEST['employee_id'] . "'";
		$rs = execSQL($sql);

		foreach ($rs as $r) {
			if ($r[0] == "") { continue; }
			$employeeName = $r['EmployeeName'];
		}

		$sql = "INSERT INTO terminations (employee_id, manager_id, reason, type_id, description, termination_date) VALUES ('" . $_REQUEST['employee_id'] . "', '" . $_SESSION['user']->getEmployeeID() . "', '" . $_REQUEST['reason'] . "', " . $_REQUEST['type_id'] . ", '" . formatQuotes($_REQUEST['description']) . "', NOW())";
		$id = execSQL($sql, true);

		suspendEmployee($_REQUEST['employee_id']);
		$sql = "INSERT INTO wf_requests (wf_class_id, employee_id, description, created_date, table_name, table_id) VALUES ((SELECT wf_class_id FROM wf_classes WHERE description = '" . $wfClass . "'), '" . $_SESSION['user']->getEmployeeID() . "', 'Termination', NOW(), 'terminations', " . $id . ")";
		$id = execSQL($sql, true);

		$sql = "INSERT INTO wf_processes (wf_request_id, required_action, hr_action, created_date) VALUES (" . $id . ", 'Employee termination: " . $employeeName . "', 1, NOW())";
		execSQL($sql, true);

		if ($_SESSION['user']->getSupervisorID() != "") {
			$sql = "INSERT INTO wf_processes(wf_request_id, employee_id, required_action, notification, created_date) VALUES (" . $id . ", '" . $_SESSION['user']->getSupervisorID() . "', 'Employee termination: " . $employeeName . "', 1, NOW())";
			execSQL($sql, true);
		}

		$sql = "INSERT INTO wf_processes(wf_request_id, employee_id, required_action, notification, created_date) VALUES (" . $id . ", '" . $_SESSION['user']->getEmployeeID() . "', 'Employee termination: " . $employeeName . "', 1, NOW())";
		execSQL($sql, true);

	}

	if ($_REQUEST['action'] == "WARNING") {
		$redirectURL = "/default.php?xapp=" . mt_rand();
		$wfClass = "Employee Warning";
		$employeeName = null;
		$attachment = "NULL";
		$targetFile = null;
		$fn = "Attachment";
		$fileName = null;
		$id = null;

		if ($_FILES[$fn]["tmp_name"] != "") {
			$fileName = basename($_FILES[$fn]["name"]);
  			$filePath = stripBrackets(getGUID()) . "_" . $fileName;
  			$targetFile = $warningsTargetDir . $filePath;
  			move_uploaded_file($_FILES[$fn]["tmp_name"], $targetFile);
  			$attachment = "'" . $filePath . "'";
        }

        $sql = "SELECT CONCAT(FirstName, \" \", LastName) AS EmployeeName FROM Employees WHERE EmployeeID = '" . $_REQUEST['employee_id'] . "'";
        $rs = execSQL($sql);

        foreach ($rs as $r) {
        	if ($r[0] == "") { continue; }
        	$employeeName = $r['EmployeeName'];
        }

        $sql = "INSERT INTO Warnings (EmployeeID, DateOfRecord, WarningType_Primary, WarningType_Secondary, WarningType_Tertiary, WarningStyleID, Attachments, Notes) VALUES ('" . $_REQUEST['employee_id'] . "', CURDATE(), " . $_REQUEST['WarningTypePrimary'] . ", " . checkNull($_REQUEST['WarningTypeSecondary']) . ", " . checkNull($_REQUEST['WarningTypeTertiary']) . ", " . $_REQUEST['WarningStyleID'] . ", " . $attachment . ", '" . formatQuotes($_REQUEST['notes']) . "')";
        $id = execSQL($sql, true);

        $sql = "INSERT INTO wf_requests (wf_class_id, employee_id, description, created_date, table_name, table_id) VALUES ((SELECT wf_class_id FROM wf_classes WHERE description = '" . $wfClass . "'), '" . $_SESSION['user']->getEmployeeID() . "', 'Warning', NOW(), 'Warnings', " . $id . ")";
		$id = execSQL($sql, true);

		$sql = "INSERT INTO wf_processes (wf_request_id, required_action, hr_action, notification, created_date) VALUES (" . $id . ", 'Employee warning: " . $employeeName . "', 1, 1, NOW())";
		execSQL($sql, true);

		if ($_SESSION['user']->getSupervisorID() != "") {
			$sql = "INSERT INTO wf_processes(wf_request_id, employee_id, required_action, notification, created_date) VALUES (" . $id . ", '" . $_SESSION['user']->getSupervisorID() . "', 'Employee warning: " . $employeeName . "', 1, NOW())";
			execSQL($sql, true);
		}

		$sql = "INSERT INTO wf_processes(wf_request_id, employee_id, required_action, notification, created_date) VALUES (" . $id . ", '" . $_SESSION['user']->getEmployeeID() . "', 'Employee warning: " . $employeeName . "', 1, NOW())";
		execSQL($sql, true);

		$sql = "INSERT INTO wf_processes(wf_request_id, employee_id, required_action, notification, created_date) VALUES (" . $id . ", '" . $_REQUEST['employee_id'] . "', 'Employee warning: " . $employeeName . "', 1, NOW())";
		execSQL($sql, true);

	}

	if ($_REQUEST['action'] == "TRAINING") {
		$redirectURL = "/default.php?xapp=TRAINING";
		$sql = "INSERT INTO training (employee_id, training_type_id, training_date, completed) VALUES ('" . $_SESSION['user']->getEmployeeID() . "', 1, NOW(), 1)";
		execSQL($sql, true);
	}

	if ($_REQUEST['action'] == "SUSPEND") {
		securityCheck("suspend_employee", false);
		suspendEmployee($_REQUEST['eid']);
	}

	if ($_REQUEST['action'] == "ADMIN-PASSWORD-RESET") {
		$email = null;

		securityCheck("reset_password", false);
		$password = mysqli_real_escape_string($dbconn,$_REQUEST['passwd']);
		$secret = password_hash($password, PASSWORD_BCRYPT);
		$employeeID = $_REQUEST['eid'];

		$sql = "UPDATE Users SET Password = '" . $secret . "', ActiveFlag = 1 WHERE EmployeeID = '" . $employeeID . "'";
		execSQL($sql, true);

		$sql = "SELECT c.email_address FROM census c, Employees e WHERE LPAD(c.ssn_nid,9,0) = REPLACE(e.SSN,'-','') AND e.EmployeeID = '" . $employeeID . "'";
		$rs = execSQL($sql);

		foreach ($rs as $r) {
			if ($r[0] == "") { continue; }
			$email = $r['email_address'];
		}

		if ($email != "") {
			$mail = new PHPMailer;
			$mail->IsSMTP();
			$mail->CharSet = 'UTF-8';
			$mail->SMTPAuth = true;
			$mail->SMTPSecure = 'tls';
			$mail->Host = 'smtp.office365.com';
			$mail->Username = "do-not-reply@nataviation.com";
			$mail->Password = "N@tion@l1";
			$mail->Port = 587;

			$mail->setFrom('do-not-reply@nataviation.com', 'National Aviation Services');
			$mail->addAddress($email);
			$mail->isHTML(true);

			$mail->Subject = 'National Aviation Services Password Reset';
			$mail->Body = "<b>PASSWORD RESET</b><br />Your system password has been reset to " . $_REQUEST['passwd'] . " and you can login now at http://hr.nataviation.com <br /><br />Please contact info@nataviation.com if you continue to have problems logging in. Thank you,<br /><br />National Aviation Services HR";
			$mail->send();
		}
	}

	if ($_REQUEST['action'] == "USER-PASSWORD-RESET") {
		$password = mysqli_real_escape_string($dbconn,$_REQUEST['passwd']);
		$secret = password_hash($password, PASSWORD_BCRYPT);

		$sql = "UPDATE Users SET Password = '" . $secret . "' WHERE EmployeeID = '" . $_SESSION['user']->getEmployeeID() . "'";
		execSQL($sql, true);
	}

	if ($_REQUEST['action'] == "PASSWORD-RESET") {
		$redirectURL = $domainRoot . "index.php?xlogout=" . mt_rand();
		$employeeID = null;
		$userID = null;
		$a = null;

		$sql = "SELECT e.EmployeeID, p.UserID, p.a FROM Employees e, passwd_reset p, Users u WHERE p.q = " . $_REQUEST['q'] . " AND p.UserID = u.UserID AND u.ActiveFlag = 1 AND u.EmployeeID = e.EmployeeID AND e.SSN = '" . formatSSN($_REQUEST['employee_reset_id']) . "'";
		$rs = execSQL($sql);

		foreach ($rs as $r) {
			if ($r[0] == "") { continue; }
			$employeeID = $r['EmployeeID'];
			$userID = $r['UserID'];
			$a = explode("~~", $r['a'])[0];
		}

		if ($userID != "") {
			if ($a == $_REQUEST['a']) {
				$password = mysqli_real_escape_string($dbconn,$_REQUEST['password']);
				$secret = password_hash($password, PASSWORD_BCRYPT);

				$sql = "UPDATE Users SET Password = '" . $secret . "', ActiveFlag = 1 WHERE EmployeeID = '" . $employeeID . "'";
				execSQL($sql, true);

				$_SESSION['user'] = new User($_REQUEST['employee_reset_id'], $password);
				$redirectURL = $domainRoot . "default.php";
			}
		}
	}

	if ($_REQUEST['action'] == "MESSAGES-MODIFY") {
		$redirectURL = "/default.php?xapp=MESSAGES";
		$employmentID = null;
		$description = null;
		$startDate = null;
		$processID = null;
		$hours = null;
		$notes = null;

		if (isset($_REQUEST['WarningsID'])) {
			$sql = "UPDATE Warnings SET Reply = '" . formatQuotes($_REQUEST['Reply']) . "' WHERE WarningsID = " . $_REQUEST['WarningsID'];
			execSQL($sql,true);
		}

		if (isset($_REQUEST['termination_id'])) {
			$sql = "UPDATE terminations SET hr_employee_id = '" . $_SESSION['user']->getEmployeeID() . "', hr_reason = '" . $_REQUEST['reason'] . "', hr_type_id = " . $_REQUEST['type_id'] . ", hr_description = '" . formatQuotes($_REQUEST['description']) . "', hr_date = NOW(), ";

			if ($_REQUEST['trinet'] == "on") { $sql .= "trinet = 1, trinet_date = NOW() "; } else { $sql .= "trinet = 0, trinet_date = NULL "; }
			$sql .= "WHERE termination_id = " . $_REQUEST['termination_id'];
			execSQL($sql,true);

			$sql = "UPDATE wf_processes SET closed_date = NOW(), approved = 1 WHERE wf_request_id = " . $_REQUEST['id'];
			execSQL($sql,true);

			$sql = "INSERT INTO wf_actions (wf_process_id, employee_id, action, action_date) VALUES (" . $_REQUEST['wf_process_id'] . ", '" . $_SESSION['user']->getEmployeeID() . "', 'COMPLETED', NOW())";
			execSQL($sql,true);

		} else {

			$sql = "SELECT wf_process_id FROM wf_processes WHERE wf_request_id = " . $_REQUEST['id'];
			$rs = execSQL($sql);

			foreach ($rs as $r) {
				if ($r[0] == "") { continue; }
				$processID = $r[0];
			}

			$sql = "UPDATE wf_processes SET closed_date = NOW(), approved = 1 WHERE wf_request_id = " . $_REQUEST['id'];
			execSQL($sql, true);

			$sql = "INSERT INTO wf_actions (wf_process_id, employee_id, action, action_date) VALUES (" . $processID . ", '" . $_SESSION['user']->getEmployeeID() . "', 'APPROVED', NOW())";
			execSQL($sql, true);

			$sql = "SELECT Date(start_date) AS start_date, Hours, Notes FROM time_off_requests WHERE time_off_request_id = (SELECT time_off_request_id FROM wf_requests WHERE wf_request_id = " . $_REQUEST['id'] . ")";
			$rs = execSQL($sql);

			foreach ($rs as $r) {
				if ($r[0] == "") { continue; }
				$startDate = $r['start_date'];
				$hours = $r['Hours'];
				$notes = $r['Notes'];
			}

			$sql = "SELECT em.EmploymentID, wc.description FROM Employments em, wf_classes wc, wf_requests w WHERE w.wf_class_id = wc.wf_class_id AND em.EmployeeID = w.employee_id AND wf_request_id = " . $_REQUEST['id'];
			$rs = execSQL($sql);

			foreach ($rs as $r) {
				if ($r[0] == "") { continue; }
				$employmentID = $r['EmploymentID'];
				$description = $r['description'];
			}

			$sql = "INSERT INTO ScheduledTimeOff (EmploymentID, Birthday, DateOff, Hours, Vacation, Sick, LOA, Notes) VALUES ('" . $employmentID . "', ";
			if ($description == "Birthday") { $sql .= "1, "; } else { $sql .= "0, "; }

			$sql .= "'" . $startDate . "', '" . $hours . "', ";
			if ($description == "Vacation") { $sql .= "1, "; } else { $sql .= "0, "; }
			if ($description == "Sick") { $sql .= "1, "; } else { $sql .= "0, "; }
			if ($description == "LOA") { $sql .= "1, "; } else { $sql .= "0, "; }

			$sql .= "'" . formatQuotes($notes) . "')";
			execSQL($sql);
		}
	}

	if ($_REQUEST['action'] == "TIMEOFF") {
		$redirectURL = "/default.php?xapp=TIMEOFF";
		$wfClass = null;
		$birthday = "0";
		$vacation = "0";
		$sick = "0";
		$loa = "0";
		$id = null;

		if ($_REQUEST['request_type'] == "0") { $vacation = "1"; $wfClass = "Vacation"; }
		if ($_REQUEST['request_type'] == "1") { $birthday = "1"; $wfClass = "Birthday"; }
		if ($_REQUEST['request_type'] == "2") { $sick = "1"; $wfClass = "Sick"; }
		if ($_REQUEST['request_type'] == "3") { $loa = "1"; $wfClass = "LOA - leave of absence"; }
		$sql = "INSERT INTO time_off_requests (employee_id, start_date, end_date, Hours, Birthday, Vacation, Sick, LOA, Notes) VALUES ('" . $_SESSION['user']->getEmployeeID() . "', '" . $_REQUEST['start_date'] . "', '" . $_REQUEST['end_date'] . "', '" . $_REQUEST['hours'] . "', " . $birthday . ", " . $vacation . ", " . $sick . ", " . $loa . ", '" . formatQuotes($_REQUEST['notes']) . "')";
		$id = execSQL($sql, true);

		$sql = "INSERT INTO wf_requests (wf_class_id, employee_id, description, created_date, table_name, table_id) VALUES ((SELECT wf_class_id FROM wf_classes WHERE description = '" . $wfClass . "'), '" . $_SESSION['user']->getEmployeeID() . "', 'Time off request', NOW(), 'time_off_requests', " . $id . ")";
		$id = execSQL($sql, true);

		$sql = "INSERT INTO wf_processes (wf_request_id, required_action, hr_action, created_date) VALUES (" . $id . ", 'Approve time off request (" . $wfClass . "): " . $_SESSION['user']->getFirstName() . " " . $_SESSION['user']->getLastName() . "', 1, NOW())";
		execSQL($sql, true);

		if ($_SESSION['user']->getSupervisorID() != "") {
			$sql = "INSERT INTO wf_processes(wf_request_id, employee_id, required_action, notification, created_date) VALUES (" . $id . ", '" . $_SESSION['user']->getSupervisorID() . "', 'Time off notification (" . $wfClass . "): " . $_SESSION['user']->getFirstName() . " " . $_SESSION['user']->getLastName() . "', 1, NOW())";
			execSQL($sql, true);
		}

		$sql = "INSERT INTO wf_processes(wf_request_id, employee_id, required_action, notification, created_date) VALUES (" . $id . ", '" . $_SESSION['user']->getEmployeeID() . "', 'Time off notification (" . $wfClass . "): " . $_SESSION['user']->getFirstName() . " " . $_SESSION['user']->getLastName() . "', 1, NOW())";
		execSQL($sql, true);
	}

	if ($_REQUEST['action'] == "CONTACTS") {
		$redirectURL = "/default.php?xapp=EMERGENCY-CONTACTS";
		if ($_REQUEST['EmergencyContactID'] != "") {
			$sql = "UPDATE EmergencyContacts SET ContactName = '" . formatQuotes($_REQUEST['ContactName']) . "', Relationship = '" . formatQuotes($_REQUEST['Relationship']) . "', Phone = '" . formatQuotes($_REQUEST['Phone']) . "', AltPhone = '" . formatQuotes($_REQUEST['AltPhone']) . "', DTLastModified = NOW() WHERE EmergencyContactID = " . $_REQUEST['EmergencyContactID'];
		} else {
			$sql = "INSERT INTO EmergencyContacts (EmployeeID, ContactName, Relationship, Phone, AltPhone, DTLastModified) VALUES ('" . $_SESSION['user']->getEmployeeID() . "', '" . formatQuotes($_REQUEST['ContactName']) . "', '" . formatQuotes($_REQUEST['Relationship']) . "', '"  . formatQuotes($_REQUEST['Phone']) . "', '" . formatQuotes($_REQUEST['AltPhone']) . "', NOW())";
		}
		execSQL($sql, true);
	}

	if ($_REQUEST['action'] == "PERMISSIONS-MODIFY") {
		$sql = "UPDATE permissions SET description = '" . formatQuotes($_REQUEST['description']) . "' WHERE permissions_id = " . $_REQUEST['pid'];
		execSQL($sql, true);

		modifyPermissions($_REQUEST['position_array'], "position");
		modifyPermissions($_REQUEST['employee_array'], "employee");
		modifyPermissions($_REQUEST['location_array'], "location");
	}

	if ($_REQUEST['action'] == "POSITIONS-MODIFY") {
		$sql = "UPDATE Positions SET Description = '" . formatQuotes($_REQUEST['Description']) . "', ShirtStyleID = " . $_REQUEST['ShirtStyleID'] . ", IsRegionalMgr = " . convertCheckBox($_REQUEST['IsRegionalMgr']) . ", IsManager = " . convertCheckBox($_REQUEST['IsManager']) . ", IsTSA = " . convertCheckBox($_REQUEST['IsTSA']) . ", FAADocs = " . convertCheckBox($_REQUEST['FAADocs']) . ", DTLastModified = NOW() WHERE PositionID = " . $_REQUEST['pid'];
		execSQL($sql, true);
		modifyPositions($_REQUEST['position_array']);
	}

	if ($_REQUEST['action'] == "SCHEDULE-UPDATE") {
		$sql = "UPDATE wf_requests SET notes = '" . formatQuotes($_REQUEST['notes']) . "' WHERE wf_request_id = " . $_REQUEST['id'];
		execSQL($sql,true);
	}

	if ($_REQUEST['action'] == "SCHEDULE-REJECT") {
		$id = null;

		$sql = "UPDATE shift_schedule_staging SET active = 0 WHERE location_id IN (SELECT LocationID FROM Locations WHERE SUBSTRING_INDEX(LocationCode, \" \", 1) LIKE '" . $_REQUEST['l'] . "%')";
		execSQL($sql,true);

		$sql = "UPDATE wf_requests SET notes = '" . formatQuotes($_REQUEST['notes']) . "' WHERE wf_request_id = " . $_REQUEST['id'];
		execSQL($sql,true);

		$sql = "UPDATE wf_processes SET started_date = CURDATE(), closed_date = CURDATE() WHERE wf_request_id = " . $_REQUEST['id'];
		$id = execSQL($sql,true);

		$sql = "INSERT INTO wf_actions (wf_process_id, employee_id, action, action_date) VALUES (" . $id . ", '" . $_SESSION['user']->getEmployeeID() . "', 'REJECTED', NOW())";
		execSQL($sql, true);

	}

	if ($_REQUEST['action'] == "SCHEDULE-CREATE") {
		$redirectURL = "/default.php?xapp=SCHEDULE-CREATE";
		$shiftScheduleDate = null;
		$staffArray = array();
		$initialLoad = true;
		$rowIndex = 0;

		$staffArray = $_REQUEST['staff'];
		loadScheduleData($scheduleDate, $staffArray, $scheduleArray, $rowIndex, $initialLoad, $_REQUEST['l']);

		$sql = "INSERT INTO shift_schedule_notes (location_code, description, employee_id, modified_date) VALUES ('" . $_REQUEST['l'] . "', '" . formatQuotes($_REQUEST['shift_schedule_notes']) . "', '" . $_SESSION['user']->getEmployeeID() . "', NOW())";
		execSQL($sql,true);
	}

	if ($_REQUEST['action'] == "SCHEDULE-MODIFY") {
		$redirectURL = "/default.php?xapp=SCHEDULE-MODIFY";
		$finalScheduleArray = array();
		$shiftScheduleDate = null;
		$initialLoad = false;
		$locationCode = null;
		$override = false;
		$totalHours = 0;
		$rowIndex = 0;

		$locationCode = null;
		$location = null;

		if (isset($_REQUEST['lc'])) {
			$locationCode = $_REQUEST['lc'];
		} else {
			$locationCode = "0";
		}

		if ($_REQUEST['l'] == "Office") {
			$location = $_REQUEST['LOCATION'];
			$locationCode = $_REQUEST['LOCATIONCODE'];
		} else {
			$location = $_REQUEST['l'];
		}

		if (isset($_REQUEST['shift_schedule_date'])) {
			$shiftScheduleDate = $_REQUEST['shift_schedule_date'];
		} else {
			$shiftScheduleDate = $_REQUEST['s'];
		}

		loadScheduleArrays($location, $locationCode, date('Y-m-d', strtotime($shiftScheduleDate)), true, true, true);
		if ($scheduleDate == date('Y-m-d', strtotime($shiftScheduleDate))) {
			if (isset($_REQUEST['table_name']) && $_REQUEST['table_name'] == "shift_schedule_changes") {
				$sql = "UPDATE shift_schedule SET active = 0, end_date = NOW() WHERE location_id IN (SELECT LocationID FROM Locations WHERE SUBSTRING_INDEX(LocationCode, \" \", 1) LIKE '" . $location . "%')";
				execSQL($sql, true);
				foreach ($staffArray as $s) { generateScheduleRow($rowIndex++, "shift_schedule"); }

				$sql = "UPDATE shift_schedule_staging SET active = 0 WHERE location_id IN (SELECT LocationID FROM Locations WHERE SUBSTRING_INDEX(LocationCode, \" \", 1) LIKE '" . $location . "%')";
				execSQL($sql,true);

				$sql = "UPDATE wf_requests SET notes = '" . formatQuotes($_REQUEST['notes']) . "' WHERE wf_request_id = " . $_REQUEST['id'];
				execSQL($sql,true);

				$sql = "UPDATE wf_processes SET started_date = CURDATE(), closed_date = CURDATE() WHERE wf_request_id = " . $_REQUEST['id'];
				$id = execSQL($sql,true);

				$sql = "INSERT INTO wf_actions (wf_process_id, employee_id, action, action_date) VALUES (" . $id . ", '" . $_SESSION['user']->getEmployeeID() . "', 'APPROVED', NOW())";
				execSQL($sql, true);


			} else {
				loadScheduleData($scheduleDate, $staffArray, $scheduleArray, $rowIndex, $initialLoad, $location);
			}

		} else {
			if (isset($_REQUEST['supersede']) && $_REQUEST['supersede'] == "on") {
				$d = (DateTime::createFromFormat('Y-m-d', $shiftScheduleDate))->modify('+7 day')->format('Y-m-d');
				$sql = "SELECT * FROM shift_schedule WHERE active = 1 AND location_id IN (SELECT LocationID FROM Locations WHERE SUBSTRING_INDEX(LocationCode, \" \", 1) LIKE '" . $location . "%')";
				$rs = execSQL($sql);

				foreach ($rs as $r) {
				  if ($r[0] == "") { continue; }
				  $sql = "INSERT INTO shift_schedule_future (location_id, start_date, ssn_nid, sat, sun, mon, tue, wed, thu, fri, active, comments) VALUES (" . $r['location_id'] . ", '" . $d . "', '" . $r['ssn_nid'] . "', '" . $r['sat'] .  "', '" . $r['sun'] .  "', '" . $r['mon'] .  "', '" . $r['tue'] .  "', '" . $r['wed'] .  "', '" . $r['thu'] .  "', '" . $r['fri'] .  "', 1, NULL)";
				  execSQL($sql, true);
				}
			}

			$sql = "DELETE FROM shift_schedule_future WHERE start_date = '" . $shiftScheduleDate . "' AND location_id IN (SELECT LocationID FROM Locations WHERE SUBSTRING_INDEX(LocationCode, \" \", 1) LIKE '" . $location . "%')";
			execSQL($sql,true);
			
			foreach ($staffArray as $s) { generateScheduleRow($rowIndex++, "shift_schedule_future", $shiftScheduleDate); }
			loadFutureScheduleData($location, $shiftScheduleDate);
		}

		$sql = "SELECT shift_schedule_notes_id FROM shift_schedule_notes WHERE location_code = '" . $location . "'";
  		$rs = execSQL($sql);

  		if (sizeof($rs) == 1) {
			$sql = "INSERT INTO shift_schedule_notes (location_code, description, employee_id, modified_date) VALUES ('" . $location . "', '" . formatQuotes($_REQUEST['shift_schedule_notes']) . "', '" . $_SESSION['user']->getEmployeeID() . "', NOW())";
			execSQL($sql,true);
		} else {
			foreach ($rs as $r) {
				if ($r[0] == "") { continue; }
				$sql = "UPDATE shift_schedule_notes SET description = '" . formatQuotes($_REQUEST['shift_schedule_notes']) . "', employee_id = '" . $_SESSION['user']->getEmployeeID() . "', modified_date = NOW() WHERE shift_schedule_notes_id = " . $r[0];
				execSQL($sql, true);
			}
		}

	}
	if ($_REQUEST['action'] == "AIRCRAFT-TYPE-CREATE") {
		$redirectURL = "/display.php?xapp=AIRCRAFT";
		$sql = "INSERT INTO aircraft(aircraft_type) VALUES ('" . formatQuotes($_REQUEST['aircraft_type']) . "')";
		execSQL($sql, true);
	}
	if ($_REQUEST['action'] == "RULES-CREATE") {
		$redirectURL = "/display.php?xapp=SYSTEM-RULES";
		$sql = "INSERT INTO rules_engine (employee_id, rule_name, rule_description) VALUES ('" . $_SESSION['user']->getEmployeeID() . "', '" . formatQuotes($_REQUEST['rule_name']) . "', '" . formatQuotes($_REQUEST['rule_description']) . "')";
		execSQL($sql, true);
	}

	if ($_REQUEST['action'] == "ISSUES-CREATE") {
		$redirectURL = "/display.php?xapp=ISSUES";
		$sql = "INSERT INTO issues (employee_id, description, created_date, active) VALUES ('" . $_SESSION['user']->getEmployeeID() . "', '" . formatQuotes($_REQUEST['description']) . "', NOW(), 1)";
		execSQL($sql, true);
	}

	if ($_REQUEST['action'] == "ISSUES-MODIFY") {
		$redirectURL = "/display.php?xapp=ISSUES";
		$sql = "INSERT INTO issues_journal (issues_id, employee_id, description, modified_date) VALUES (" . $_REQUEST['iid'] . ", '" . $_SESSION['user']->getEmployeeID() . "', '" . formatQuotes($_REQUEST['description']) . "', NOW())";
		execSQL($sql);

		if ($_REQUEST['active'] == "on") {
			$sql = "UPDATE issues SET active = 0 WHERE issues_id = " . $_REQUEST['iid'];
			execSQL($sql, true);
		}
	}

	if ($_REQUEST['action'] == "RULES-MODIFY") {
		$redirectURL = "/display.php?xapp=SYSTEM-RULES";
		$sql = "INSERT INTO rules_engine_journal (rules_engine_id, employee_id, base_rule, delta_rule) VALUES (" . $_REQUEST['rid'] . ", '" . $_SESSION['user']->getEmployeeID() . "', (SELECT rule_description FROM rules_engine WHERE rules_engine_id = " . $_REQUEST['rid'] . "), '" . formatQuotes($_REQUEST['rule_description']) . "')";
		execSQL($sql, true);

		$sql = "UPDATE rules_engine SET rule_description = '" . formatQuotes($_REQUEST['rule_description']) . "' WHERE rules_engine_id = " . $_REQUEST['rid'];
		execSQL($sql, true);

	}

	if ($_REQUEST['action'] == "LOCATIONS-MODIFY") {
		$redirectURL = "/locations.php?xapp=LOCATIONS-MODIFY&p=" . $_REQUEST['p'];
		$ArptRsrvCntrID = $_REQUEST['ArptRsrvCntrID'];
		$LocationID = $_REQUEST['LocationID'];
		$TopLevelID = $_REQUEST['TopLevelID'];
		$Name = formatQuotes($_REQUEST['Name']);
		$AirportCode = $_REQUEST['AirportCode'];
		$Director = $_REQUEST['Director'];
		$TLManager = $_REQUEST['TLManager'];
		$LManager = $_REQUEST['LManager'];
		$Burden = $_REQUEST['Burden'];
		$StateID = $_REQUEST['StateID'];
		$LocationCode = $_REQUEST['LocationCode'];
		$nsLocationID = $_REQUEST['ns_location_id'];

		$sql = "UPDATE ArptRsrvCntr SET Name = '" . $Name . "', AirportCode = '" . $AirportCode . "' WHERE ArptRsrvCntrID = " . $ArptRsrvCntrID;
		execSQL($sql, true);

		$sql = "UPDATE TopLevelLocation SET Manager = '" . $TLManager . "', StateID = " . $StateID . ", Code = '" . $LocationCode . "' WHERE TopLevelID = " . $TopLevelID;
		execSQL($sql, true);

		$sql = "UPDATE Locations SET Director = '" . $Director . "', Manager = '" . $LManager . "', Burden = '" . $Burden . "', DTLastModified = NOW() WHERE LocationID = " . $LocationID;
		execSQL($sql, true);

		if ($nsLocationID != "") {
			$sql = "SELECT location_mapping_id FROM location_mapping WHERE LocationID = " . $LocationID . " AND ns_location_id = " . $nsLocationID;
			$rs = execSQL($sql);

			$num = sizeof($rs);
			if ($num > 1) {
				foreach($rs as $r) {
					if ($r[0] == "") { continue; }
					$sql = "UPDATE location_mapping SET ns_location_id = " . $nsLocationID . ", LocationID = " . $LocationID . " WHERE location_mapping_id = " . $r[0];
				}
			} else {
				$sql = "INSERT INTO location_mapping (LocationID, ns_location_id) VALUES (" . $LocationID . ", " . $nsLocationID . ")";
			}
			execSQL($sql, true);
		}
	}

	if ($_REQUEST['action'] == "NETSUITE_INTEGRATION") {
		$redirectURL = "/default.php?x=NETSUITE_INTEGRATION&xapp=" . mt_rand();
		if ($_FILES["customer_csv"]["tmp_name"] != "") {
			$csv = loadFile("customer_csv");
			$handle = fopen($targetDir . $csv, "r");
			if ($handle) {
				$sql = "TRUNCATE TABLE ns_customers;";
				execSQL($sql,true);
			    while (($line = fgets($handle)) !== false) {
			        $fields = explode(",", $line);
			        $sql = "INSERT INTO ns_customers (netsuite_id, name) VALUES (" . $fields[0] . ", '" . str_replace("\r", "", str_replace("\n", "", $fields[2])) . "');";
			        execSQL($sql,true);
			    }
			    fclose($handle);
			}
			rename($targetDir . $csv, $archiveDir . $csv);
        }

        if ($_FILES["items_csv"]["tmp_name"] != "") {
			$csv = loadFile("items_csv");
			$handle = fopen($targetDir . $csv, "r");
			if ($handle) {
			    while (($line = fgets($handle)) !== false) {
			        $fields = explode(",", $line);
			        if ($fields[0] == "Internal ID") { continue; }
			        $sql = "SELECT netsuite_id FROM ns_items WHERE netsuite_id = " . $fields[0] . " AND name = '" . $fields[2] . "' AND price = '" . $fields[3] . "' AND location = '" . $fields[4] . "' AND start_date = '" . str_replace("\r", "", convertDate(str_replace("\n", "", $fields[5]))) . "' AND active = 1";
			        $rs = execSQL($sql);
		    		if (sizeof($rs) == 1) {
		    			$sql = "SELECT netsuite_id from ns_items WHERE netsuite_id = " . $fields[0];
		    			$rs = execSQL($sql);
		    			if (sizeof($rs) > 1) {
		    				$sql = "UPDATE ns_items SET end_date = NOW(), active = 0 WHERE netsuite_id = " . $fields[0];
		    				execSQL($sql,true);
		    			}
	    				$sql = "INSERT INTO ns_items (netsuite_id, name, price, location, start_date, active) VALUES (" . $fields[0] . ", '" . $fields[2] . "', '" . $fields[3] . "', '" . $fields[4] . "', '" . convertDate(str_replace("\r", "", str_replace("\n", "", $fields[5]))) . "', 1);";
	    				execSQL($sql, true);
	    			}
			    }
			    fclose($handle);
			}
			rename($targetDir . $csv, $archiveDir . $csv);
        }

        if ($_FILES["locations_csv"]["tmp_name"] != "") {
			$csv = loadFile("locations_csv");
			$handle = fopen($targetDir . $csv, "r");
			if ($handle) {
				$sql = "TRUNCATE TABLE ns_locations;";
				execSQL($sql,true);
			    while (($line = fgets($handle)) !== false) {
			        $fields = explode(",", $line);
			        $sql = "INSERT INTO ns_locations (netsuite_id, name) VALUES (" . $fields[0] . ", '" . str_replace("\r", "", str_replace("\n", "", $fields[2])) . "');";
			        execSQL($sql,true);
			    }
			    fclose($handle);
			}
			rename($targetDir . $csv, $archiveDir . $csv);
        }
	}
	
	//######################################
	//######################################
	clean();
	//######################################
	//######################################
}

//########################################
//########################################
if ($redirectURL != null && !$debugMode) { header("Location: " . $redirectURL); }

?>