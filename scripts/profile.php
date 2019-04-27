<?php $loginScreen = false; ?>
<?php require_once("e.php"); ?>
<?php

if (isset($_REQUEST['xargs']) && $_REQUEST['xargs'] == "NAS2018") {
	if ($_REQUEST['action'] == "PROFILE") {
		$fieldArray = array();
		$financialArray = array();

		array_push($financialArray, "current_annual_rt");
		array_push($financialArray, "current_monthly_rt");
		array_push($financialArray, "current_hourly_rt");

		array_push($financialArray, "increase_amount");
		array_push($financialArray, "prev_annual_rt");

		array_push($financialArray, "bonus");
		array_push($financialArray, "ytd_bonus");
		array_push($financialArray, "addl_pay");
		array_push($financialArray, "shift_rate");

		$cout = "<center><table width='600' border='0' style='border-collapse: collapse; border: 1px solid black;'><tr><td class='navtitle' colspan='2'>National Aviation Services Census Profile</td></tr>";

		$sql = "SHOW COLUMNS FROM census";
		$rs = execSQL($sql);
		foreach($rs as $r) {
			if ($r[0] == "") { continue; }
			if ($_REQUEST['delta'] == "1" && $r[0] == "census_id") { $r[0] = "census_delta_id"; }
		    array_push($fieldArray, $r[0]);
		}

		if ($_REQUEST['delta'] == "0") {
			$sql = "SELECT * FROM census WHERE census_id = " . $_REQUEST['cid'];
		} else {
			$sql = "SELECT * FROM census_delta WHERE census_delta_id = " . $_REQUEST['cid'];
		}
	    $rs = execSQL($sql);

		foreach($rs as $r) {
			if ($r[0] == "") { continue; }

		    foreach ($fieldArray as $d) {
		    	if ($r[$d] == "") { continue; }
		    	if (in_array($d, $financialArray)) { $r[$d] = "$$$$"; }
		    	$cout .= "<tr><td style='border: 1px solid black; padding: 7px; text-align: left;'><b>" . $d . "</b></td><td style='border: 1px solid black; padding: 7px; text-align: left;'>" . $r[$d] . "</td></tr>";
		  	}
		}

		$cout .= "</table></center>";
		echo $cout;

	}

	if ($_REQUEST['action'] == "APPLICATION") {
		$cout = "<center><input id='employee_application_cancel' type='button' value='RESET' style='height:30px;width:150px;display:inline;margin:0 auto;' /> &nbsp; ";


		$sql = "SELECT * FROM employee_application WHERE employee_application_id = " . $_REQUEST['aid'];
		$rs = execSQL($sql);

		foreach ($rs as $r) {
			if ($r[0] == "") { continue; }

			if ($r['file_name'] != "") {
				$cout .= "<input id='employee_resume' type='button' value='RESUME' onclick='window.open(\"/files/employment/" . $r['file_path'] . "\");' style='height:30px;width:150px;display:inline;margin:0 auto;' />";
			} else {
				$cout .= "<div style='display: inline; border: solid; border-color: #000000; padding: 5px;'>No resume was uploaded</div>";
			}

			$cout .= "<p><table width='600' border='1' bordercolor='#000000'><tr><td colspan='2' class='navtitle'>National Aviation Services Employment Application</td></tr>";
			$cout .= "<tr><td width='200'><b>First Name:</b></td><td>" . $r['first_name'] . "</td></tr>";
			$cout .= "<tr><td><b>Last Name:</b></td><td>" . $r['last_name'] . "</td></tr>";
			$cout .= "<tr><td><b>Home address1:</b></td><td>" . $r['home_address_1'] . "</td></tr>";
			$cout .= "<tr><td><b>Home address2:</b></td><td>" . $r['home_address_2'] . "</td></tr>";
			$cout .= "<tr><td><b>Home city:</b></td><td>" . $r['home_city'] . "</td></tr>";
			$cout .= "<tr><td><b>Home state:</b></td><td>" . $r['home_state'] . "</td></tr>";
			$cout .= "<tr><td><b>Home zip:</b></td><td>" . $r['home_zip'] . "</td></tr>";
			$cout .= "<tr><td><b>Home country:</b></td><td>" . $r['home_country'] . "</td></tr>";
			$cout .= "<tr><td><b>Home phone:</b></td><td>" . $r['home_phone'] . "</td></tr>";
			$cout .= "<tr><td><b>Home email:</b></td><td>" . $r['home_email'] . "</td></tr>";
			$cout .= "<tr><td><b>SSN:</b></td><td>" . $r['ssn'] . "</td></tr>";
			$cout .= "<tr><td><b>DOB:</b></td><td>" . $r['dob'] . "</td></tr>";
			$cout .= "<tr><td><b>Position:</b></td><td>" . $r['position'] . "</td></tr>";
			$cout .= "<tr><td><b>Airport:</b></td><td>" . $r['airport'] . "</td></tr>";
			$cout .= "<tr><td><b>Work previous? " . $r['work_prev'] . "</td></tr>";
			$cout .= "<tr><td><b>When:</b></td><td>" . $r['work_when'] . "</td></tr>";
			$cout .= "<tr><td><b>Shirt size:</b></td><td>" . $r['shirt_size'] . "</td></tr>";
			$cout .= "<tr><td><b>US citizen:</b></td><td>" . $r['us_citizen'] . "</td></tr>";
			$cout .= "<tr><td><b>Work authorization:</b></td><td>" . $r['work_auth'] . "</td></tr>";
			$cout .= "<tr><td><b>Felony conviction:</b></td><td>" . $r['felony_conviction'] . "</td></tr>";
			$cout .= "<tr><td><b>Felony explanation:</b></td><td>" . $r['felony_explanation'] . "</td></tr>";
			$cout .= "<tr><td><b>HS name:</b></td><td>" . $r['hs_name'] . "</td></tr>";
			$cout .= "<tr><td><b>HS address:</b></td><td>" . $r['hs_address'] . "</td></tr>";
			$cout .= "<tr><td><b>HS dates:</b></td><td>" . $r['hs_dates'] . "</td></tr>";
			$cout .= "<tr><td><b>HS graduate:</b></td><td>" . $r['hs_graduate'] . "</td></tr>";
			$cout .= "<tr><td><b>College name:</b></td><td>" . $r['college_name'] . "</td></tr>";
			$cout .= "<tr><td><b>College address:</b></td><td>" . $r['college_address'] . "</td></tr>";
			$cout .= "<tr><td><b>College dates:</b></td><td>" . $r['college_dates'] . "</td></tr>";
			$cout .= "<tr><td><b>College graduate:</b></td><td>" . $r['college_graduate'] . "</td></tr>";
			$cout .= "<tr><td><b>Other school name:</b></td><td>" . $r['other_school_name'] . "</td></tr>";
			$cout .= "<tr><td><b>Other school address:</b></td><td>" . $r['other_school_address'] . "</td></tr>";
			$cout .= "<tr><td><b>Other school dates:</b></td><td>" . $r['other_school_dates'] . "</td></tr>";
			$cout .= "<tr><td><b>Other school graduate:</b></td><td>" . $r['other_school_graduate'] . "</td></tr>";
			$cout .= "<tr><td><b>Reference 1 name:</b></td><td>" . $r['ref_1_name'] . "</td></tr>";
			$cout .= "<tr><td><b>Reference 1 relationship:</b></td><td>" . $r['ref_1_relationship'] . "</td></tr>";
			$cout .= "<tr><td><b>Reference 1 company:</b></td><td>" . $r['ref_1_company'] . "</td></tr>";
			$cout .= "<tr><td><b>Reference 1 phone:</b></td><td>" . $r['ref_1_phone'] . "</td></tr>";
			$cout .= "<tr><td><b>Reference 2 name:</b></td><td>" . $r['ref_2_name'] . "</td></tr>";
			$cout .= "<tr><td><b>Reference 2 relationship:</b></td><td>" . $r['ref_2_relationship'] . "</td></tr>";
			$cout .= "<tr><td><b>Reference 2 company:</b></td><td>" . $r['ref_2_company'] . "</td></tr>";
			$cout .= "<tr><td><b>Reference 2 phone:</b></td><td>" . $r['ref_2_phone'] . "</td></tr>";
			$cout .= "<tr><td><b>Reference 3 name:</b></td><td>" . $r['ref_3_name'] . "</td></tr>";
			$cout .= "<tr><td><b>Reference 3 relationship:</b></td><td>" . $r['ref_3_relationship'] . "</td></tr>";
			$cout .= "<tr><td><b>Reference 3 company:</b></td><td>" . $r['ref_3_company'] . "</td></tr>";
			$cout .= "<tr><td><b>Reference 3 phone:</b></td><td>" . $r['ref_3_phone'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 1 name:</b></td><td>" . $r['emp_1_name'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 1 phone:</b></td><td>" . $r['emp_1_phone'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 1 address1:</b></td><td>" . $r['emp_1_address_1'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 1 address2:</b></td><td>" . $r['emp_1_address_2'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 1 city:</b></td><td>" . $r['emp_1_city'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 1 state:</b></td><td>" . $r['emp_1_state'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 1 zip:</b></td><td>" . $r['emp_1_zip'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 1 country:</b></td><td>" . $r['emp_1_country'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 1 supervisor:</b></td><td>" . $r['emp_1_supervisor'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 1 jobtitle:</b></td><td>" . $r['emp_1_jobtitle'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 1 responsibilities:</b></td><td>" . $r['emp_1_responsibilities'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 1 reason for leaving:</b></td><td>" . $r['emp_1_reason_for_leaving'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 1 from date:</b></td><td>" . $r['emp_1_from'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 1 to date:</b></td><td>" . $r['emp_1_to'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 1 contact:</b></td><td>" . $r['emp_1_contact'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 2 name:</b></td><td>" . $r['emp_2_name'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 2 phone:</b></td><td>" . $r['emp_2_phone'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 2 address1:</b></td><td>" . $r['emp_2_address_1'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 2 address2:</b></td><td>" . $r['emp_2_address_2'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 2 city:</b></td><td>" . $r['emp_2_city'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 2 state:</b></td><td>" . $r['emp_2_state'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 2 zip:</b></td><td>" . $r['emp_2_zip'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 2 country:</b></td><td>" . $r['emp_2_country'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 2 supervisor:</b></td><td>" . $r['emp_2_supervisor'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 2 jobtitle:</b></td><td>" . $r['emp_2_jobtitle'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 2 responsibilities:</b></td><td>" . $r['emp_2_responsibilities'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 2 reason for leaving:</b></td><td>" . $r['emp_2_reason_for_leaving'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 2 from date:</b></td><td>" . $r['emp_2_from'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 2 to date:</b></td><td>" . $r['emp_2_to'] . "</td></tr>";
			$cout .= "<tr><td><b>Employer 2 contact:</b></td><td>" . $r['emp_2_contact'] . "</td></tr>";
			$cout .= "<tr><td><b>Military branch:</b></td><td>" . $r['military_branch'] . "</td></tr>";
			$cout .= "<tr><td><b>Military date from:</b></td><td>" . $r['military_from'] . "</td></tr>";
			$cout .= "<tr><td><b>Military date to:</b></td><td>" . $r['military_to'] . "</td></tr>";
			$cout .= "<tr><td><b>Military rank:</b></td><td>" . $r['military_rank'] . "</td></tr>";
			$cout .= "<tr><td><b>Military discharge:</b></td><td>" . $r['military_discharge'] . "</td></tr>";
			$cout .= "<tr><td><b>Military discharge explanation:</b></td><td>" . $r['military_explanation'] . "</td></tr>";
			$cout .= "<tr><td><b>Signature:</b></td><td>" . $r['signature'] . "</td></tr>";


		}

		$cout .= "</table></center>";
		echo $cout;

	}

	if ($_REQUEST['action'] == "NASPROFILE") {
		$fieldArray = array();
		$employeeID = null;
		$nameData = null;

		if (isset($_REQUEST['eid'])) {
			$employeeID = $_REQUEST['eid'];
		} else {
			$employeeID = $_SESSION['user']->getEmployeeID();
		}

		if (isset($_REQUEST['l'])) {
			$nameData = ": " . $_REQUEST['l'] . ", " . $_REQUEST['f'];
		}
		
		if ($_REQUEST['p'] == "1") {
			$cout = "<center><table width='600' border='0' style='border-collapse: collapse; border: 1px solid black;'><tr><td class='navtitle' colspan='2'>National Aviation Services Employee Profile" . $nameData . "<input type='hidden' name='globalVEmployee' id='globalVEmployee' value='1' /></td></tr>";

			$sql = "SHOW COLUMNS FROM Employees";
			$rs = execSQL($sql);
			foreach($rs as $r) {
				if ($r[0] == "") { continue; }
			    array_push($fieldArray, $r[0]);
			}
			$sql = "SELECT * FROM Employees WHERE EmployeeID = '" . $employeeID . "'";

		} else if ($_REQUEST['p'] == "2") {
			$cout = "<center><table width='600' border='0' style='border-collapse: collapse; border: 1px solid black;'><tr><td class='navtitle' colspan='2'>National Aviation Services Employment History" . $nameData . "<input type='hidden' name='globalVEmployee' id='globalVEmployee' value='2' /></td></tr>";

			$sql = "SHOW COLUMNS FROM Employments";
			$rs = execSQL($sql);
			foreach($rs as $r) {
				if ($r[0] == "") { continue; }
			    array_push($fieldArray, $r[0]);
			}
			$sql = "SELECT * FROM Employments WHERE EmployeeID = '" . $employeeID . "'";

		} else if ($_REQUEST['p'] == "3") {
			$cout = "<center><table width='600' border='0' style='border-collapse: collapse; border: 1px solid black;'><tr><td class='navtitle' colspan='2'>National Aviation Services Employment Status Information" . $nameData . "<input type='hidden' name='globalVEmployee' id='globalVEmployee' value='3' /></td></tr>";

			$sql = "SHOW COLUMNS FROM EmploymentStatus";
			$rs = execSQL($sql);
			foreach($rs as $r) {
				if ($r[0] == "") { continue; }
			    array_push($fieldArray, $r[0]);
			}
			$sql = "SELECT * FROM EmploymentStatus WHERE EmploymentID = (SELECT EmploymentID FROM Employments WHERE EmployeeID = '" . $employeeID . "') ORDER BY DateOfChange DESC";
		}

	    $rs = execSQL($sql);

		foreach($rs as $r) {
			if ($r[0] == "") { continue; }

		    foreach ($fieldArray as $d) {
		    	if ($r[$d] == "") { continue; }
		    	if ($d == "Active") { if ($r[$d] == "1") { $color = "#27d929"; } else { $color = "#cecece"; } break; }
		    }

		    foreach ($fieldArray as $d) {
		    	if ($r[$d] == "") { continue; }

		    	$fieldValue = $r[$d];
		    	if ($d == "HourlyRate") { $fieldValue = "$$$$"; }
		    	if ($d == "DOB") { $fieldValue = explode(" ", $fieldValue)[0]; }
		    	if ($d == "GenderID") { $d = "Gender"; $fieldValue = convertGenderID($fieldValue); }
		    	if ($fieldValue == "0" || $fieldValue == "1") { $fieldValue = convertBinary($fieldValue); }
		    	if ($d == "EmploymentClassID") { $d = "Employment Class"; $fieldValue = convertPayID($fieldValue); }
		    	if ($d == "WorkStatusID") { $d = "Work Status"; $fieldValue = convertWorkStatusID($fieldValue); }
		    	if ($d == "PositionID") { 
		    		$d = "Position"; 
		    		$sql = "SELECT Description FROM Positions WHERE PositionID = " . $fieldValue;
		    		$rs1 = execSQL($sql);

		    		foreach ($rs1 as $r1) {
		    			if ($r1[0] == "") { continue; }
		    			$fieldValue = $r1[0];
		    		}
		    	}

		    	if ($d == "RaceID") {
		    		$sql = "SELECT Race FROM Races WHERE RaceID = " . $r[$d];
		    		$rs1 = execSQL($sql);
		    		$d = "Race";

		    		foreach ($rs1 as $r1) {
		    			if ($r1[0] == "") { continue; }
		    			$fieldValue = $r1[0];
		    		}
		    	}

		    	if ($d == "EmpStatusID") {
		    		$cout .= "<tr bgcolor='" . $color . "'><td colspan='2'>&nbsp;</td></tr><tr><td style='border: 1px solid black; padding: 7px; text-align: left;'><b>" . $d . "</b></td><td style='border: 1px solid black; padding: 7px; text-align: left;'>" . $r[$d] . "</td></tr>";
		    	} else {
		    		$cout .= "<tr><td style='border: 1px solid black; padding: 7px; text-align: left;'><b>" . $d . "</b></td><td style='border: 1px solid black; padding: 7px; text-align: left;'>" . $fieldValue . "</td></tr>";
		    	}
		  	}
		}

		$cout .= "</table><p>";

		if ($_REQUEST['p'] == "1") {
			$color = "#cecece";
			$lastAccess = null;
			$loginStatus = "ENABLED";

			$cout .= "<table width='600' border='0' style='border-collapse: collapse; border: 1px solid black;'><tr><td class='navtitle' colspan='2'>National Aviation Services Account" . $nameData . "</td></tr>";

			$sql = "SELECT suspension_id FROM suspensions WHERE employee_id = '" . $employeeID . "'";
			$rs = execSQL($sql);

			foreach ($rs as $r) {
				if ($r[0] == "") { continue; }
				$color = "#ff0000";
				$loginStatus = "SUSPENDED";
			}

			$sql = "SELECT ActiveFlag, LastAccess FROM Users WHERE EmployeeID = '" . $employeeID . "'";
			$rs = execSQL($sql);

			foreach ($rs as $r) {
				if ($r[0] == "") { continue; }
				$lastAccess = $r['LastAccess'];
				if ($r['ActiveFlag'] == "0") {
					$color = "#ff0000";
					$loginStatus = "SUSPENDED";
				}
			}

			$cout .= "<tr id='account_color' style='background-color:" . $color . ";'><td colspan='2'>&nbsp;</td></tr><tr><td style='border: 1px solid black; padding: 7px; text-align: left;'><b>Login status:</b></td><td style='border: 1px solid black; padding: 7px; text-align: left;'><div id='login_status_div'>" . $loginStatus . "</div></td></tr>";


			$cout .= "<tr><td style='border: 1px solid black; padding: 7px; text-align: left;'><b>Last Access:</b></td><td style='border: 1px solid black; padding: 7px; text-align: left;'>" . $lastAccess . "</td></tr></table>";
		}


		$cout .= "</center>";
		echo $cout;
	}
	
	//######################################
	//######################################
	clean();
	//######################################
	//######################################
}

?>