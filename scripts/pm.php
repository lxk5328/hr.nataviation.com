<?php $loginScreen = false; ?>
<?php require_once("e.php"); ?>
<?php

if (isset($_REQUEST['xargs']) && $_REQUEST['xargs'] == "NAS2018") {
	if ($_REQUEST['action'] == "LOAD-MAPPING-DATA") {

		$sql = "SELECT LPAD(ssn_nid,9,0) as ssn_nid, business_title FROM census";
		$rs = execSQL($sql);

		foreach ($rs as $r) {
			if ($r[1] == "") { continue; }
			
			$sql = "SELECT PositionID FROM Positions WHERE PositionID = (SELECT PositionID FROM EmploymentStatus WHERE EmploymentID = (SELECT EmploymentID FROM Employments WHERE EmployeeID = (SELECT EmployeeID FROM Employees WHERE SSN = '" . formatSSN($r['ssn_nid']) . "' LIMIT 1) LIMIT 1) LIMIT 1)";
			$rs1 = execSQL($sql);

			if (sizeof($rs1) > 1) {
				foreach ($rs1 as $r1) {
					if ($r1[0] == "") { continue; }
					$sql = "SELECT position_mapping_id FROM position_mapping WHERE business_title = '" . $r['business_title'] . "'";
					$rs2 = execSQL($sql);
					if (sizeof($rs2) == 1) {
						$sql = "INSERT INTO position_mapping (position_id, business_title) VALUES (" . $r1[0] . ", '" . $r['business_title'] . "')";
						execSQL($sql, true);
					}
				}
			}
		}
	}

	if ($_REQUEST['action'] == "ENCRYPT-COMPENSATION") {

		$sql = "SELECT census_id, current_annual_rt, current_monthly_rt, current_hourly_rt, bonus, ytd_bonus, addl_pay, shift_rate FROM census";
		$rs = execSQL($sql);

		foreach ($rs as $r) {
			if ($r[0] == "") { continue; }
			$current_annual_rt = aescrypt($r['current_annual_rt']);
			$current_monthly_rt = aescrypt($r['current_monthly_rt']);
			$current_hourly_rt = aescrypt($r['current_hourly_rt']);
			$bonus = aescrypt($r['bonus']);
			$ytd_bonus = aescrypt($r['ytd_bonus']);
			$addl_pay = aescrypt($r['addl_pay']);
			$shift_rate = aescrypt($r['shift_rate']);

		 	$sql = "UPDATE census SET current_annual_rt = '" . $current_annual_rt . "', current_monthly_rt = '" . $current_monthly_rt . "', current_hourly_rt = '" . $current_hourly_rt . "', bonus = '" . $bonus . "', ytd_bonus = '" . $ytd_bonus . "', addl_pay = '" . $addl_pay . "', shift_rate = '" . $shift_rate . "' WHERE census_id = " . $r['census_id'];
		 	execSQL($sql, true);
		}

		$sql = "SELECT census_delta_id, current_annual_rt, current_monthly_rt, current_hourly_rt, bonus, ytd_bonus, addl_pay, shift_rate FROM census_delta";
		$rs = execSQL($sql);

		foreach ($rs as $r) {
			if ($r[0] == "") { continue; }
			$current_annual_rt = aescrypt($r['current_annual_rt']);
			$current_monthly_rt = aescrypt($r['current_monthly_rt']);
			$current_hourly_rt = aescrypt($r['current_hourly_rt']);
			$bonus = aescrypt($r['bonus']);
			$ytd_bonus = aescrypt($r['ytd_bonus']);
			$addl_pay = aescrypt($r['addl_pay']);
			$shift_rate = aescrypt($r['shift_rate']);

		 	$sql = "UPDATE census_delta SET current_annual_rt = '" . $current_annual_rt . "', current_monthly_rt = '" . $current_monthly_rt . "', current_hourly_rt = '" . $current_hourly_rt . "', bonus = '" . $bonus . "', ytd_bonus = '" . $ytd_bonus . "', addl_pay = '" . $addl_pay . "', shift_rate = '" . $shift_rate . "' WHERE census_delta_id = " . $r['census_delta_id'];
		 	execSQL($sql, true);
		}

	}

	if ($_REQUEST['action'] == "LOAD-EMPLOYEES") {
		$priorEmployee = null;
		$censusTableID = null;
		$initialPhase = null;
		$initialLoad = false;
		$activeStatus = null;
		$employmentID = null;
		$employeeID = null;
		$finalPhase = null;
		$wfNewHire = null;
		$table = null;

		$wfLocationMapping = false;
		$wfPositionMapping = false;
		$wfNewHireMapping = false;

		if (isset($_REQUEST['LOAD']) && $_REQUEST['LOAD'] == "INITIAL") {
			$initialLoad = true;
			$table = "census";
		} else {
			$initialLoad = false;
			$table = "census_delta";
			$initialPhase = "0";
			$finalPhase = "1";
		}

		if ($initialLoad) {
			$sql = "SELECT census_id, LPAD(ssn_nid,9,0) as ssn_nid FROM census WHERE census_id = 1681";
		} else {
			$sql = "SELECT census_delta_id, LPAD(ssn_nid,9,0) as ssn_nid FROM census_delta WHERE loaded = " . $initialPhase;
		}
		$rs = execSQL($sql);



		foreach ($rs as $r) {
			if ($r[0] == "") { continue; }

			$censusTableID = $r[0];
			$activeStatus = false;
			$priorEmployee = false;
			$employmentID = null;
			$employeeID = null;

			$sql = "SELECT EmployeeID FROM Employees WHERE REPLACE(SSN,'-','') = '" . $r['ssn_nid'] . "'";
			$rs1 = execSQL($sql);

			$sql = "SELECT * FROM " . $table . " WHERE " . $table . "_id = " . $censusTableID;
			$rs2 = execSQL($sql);

			foreach ($rs2 as $r2) {
				if ($r2[0] == "") { continue; }

				if (sizeof($rs1) == 1) {
					echo "<p>----------------------------<br />";
					echo "New employee<br />";

					$employmentID = stripBrackets(getGUID());
					$employeeID = stripBrackets(getGUID());
					$wfNewHireMapping = true;

					$sql = "INSERT INTO EmploymentStatus (EmploymentID, EmploymentClassID, WorkStatusID, PositionID, TopSOIID, Active, DateOfChange, HourlyRate, MonthlyAllowance, Parking) VALUES ('" . $employmentID . "', " . convertPayType($r2['pay_type']) . ", " . convertWorkStatus($r2['full_part']) . ", ";

					$pSQL = "SELECT position_id FROM position_mapping WHERE business_title = '" . $r2['business_title'] . "' LIMIT 1";
					$rs3 = execSQL($pSQL);

					if (sizeof($rs3) == 1) {
						$sql .= $onboardingID . ", ";
						$wfPositionMapping = true;
					} else {
						foreach ($rs3 as $r3) {
							if ($r3[0] == "") { continue; }
							$sql .= $r3['position_id'] . ", ";
						}
					}

					$lSQL = "SELECT TopSOIID FROM TopLevel_SOILocation where TopLevelID = (SELECT TopLevelID FROM Locations WHERE LocationCode = '" . $r2['location_description'] . "' LIMIT 1) LIMIT 1;";
					$rs4 = execSQL($lSQL);

					if (sizeof($rs4) == 1) {
						$sql .= "0, ";
						$wfLocationMapping = true;
					} else {
						foreach ($rs4 as $r4) {
							if ($r4[0] == "") { continue; }
							$sql .= $r4['TopSOIID'] . ", ";
						}
					}

					if (convertStatus($r2['status']) == "1") { $activeStatus = true; }
					$sql .= convertStatus($r2['status']) . ", NOW(), '" . $r2['current_hourly_rt'] . "', 0, 0)";
					execSQL($sql, true);
					
					$terminated = "NULL";
					if ($r2['action'] == "TER") { $terminated = "'" . $r2['action_date'] . "'"; }

					$sql = "INSERT INTO Employments (EmploymentID, EmployeeID, HireDate, `Terminated`, DTLastModified) VALUES ('" . $employmentID . "', '" . $employeeID . "', '" . $r2['trinet_hire_date'] . "', " . $terminated . ", NOW())";
					echo "<br />Employments: " . $sql . "<br />";
					execSQL($sql, true);

					$sql = "INSERT INTO Employees (EmployeeID, GenderID, RaceID, ZipCodeID, SSN, LastName, FirstName, MiddleName, DOB, Address1, Address2, Phone, AltPhone, EMail, DTLastModified) VALUES ('" . $employeeID . "', " . convertGender($r2['sex']) . ", " . convertRace($r2['race']) . ", (SELECT ZipCodeID FROM ZipCodes where ZipCode = '" . $r2['home_zip'] . "' AND IsPreferred = 1), '" . formatSSN(str_pad($r2['ssn_nid'], 8, '0', STR_PAD_LEFT)) . "', '" . $r2['last_name'] . "', '" . $r2['first_name'] . "', '" . $r2['middle_name'] . "', '" . $r2['dob'] . "', '" . $r2['home_address_1'] . "', '" . $r2['home_address_2'] . "', '" . $r2['home_phone'] . "', '" . $r2['cell_phone'] . "', '" . $r2['email_address'] . "', NOW())";
					execSQL($sql, true);


				} else {
					if ($initialLoad) { continue; }
					echo "<p>----------------------------<br />";
					echo "Current employee<br />";

					foreach ($rs1 as $r1) {
						if ($r1[0] == "") { continue; }
						$employeeID = $r1['EmployeeID'];
					}

					$sql = "SELECT * FROM EmploymentStatus WHERE Active = 1 AND EmploymentID IN (SELECT EmploymentID FROM Employments Where EmployeeID = '" . $employeeID . "')";
					$rs3 = execSQL($sql);

					foreach ($rs3 as $r3) {
						if ($r3[0] == "") { continue; }
						$employmentID = $r3['EmploymentID'];

						$sql = "UPDATE EmploymentStatus SET Active = 0 WHERE EmpStatusID = " . $r3['EmpStatusID'];
						execSQL($sql, true);

						$sql = "INSERT INTO EmploymentStatus (EmploymentID, EmploymentClassID, WorkStatusID, PositionID, TopSOIID, Active, DateOfChange, HourlyRate, MonthlyAllowance, Parking) VALUES ('" . $employmentID . "', " . convertPayType($r2['pay_type']) . ", " . convertWorkStatus($r2['full_part']) . ", ";
						execSQL($sql, true);

						$pSQL = "SELECT position_id FROM position_mapping WHERE business_title = '" . $r2['business_title'] . "' LIMIT 1";
						$rs3 = execSQL($pSQL);

						if (sizeof($rs3) == 1) {
							$sql .= $onboardingID . ", ";
							$wfPositionMapping = true;
						} else {
							foreach ($rs3 as $r3) {
								if ($r3[0] == "") { continue; }
								$sql .= $r3['position_id'] . ", ";
							}
						}

						$lSQL = "SELECT TopSOIID FROM TopLevel_SOILocation where TopLevelID = (SELECT TopLevelID FROM Locations WHERE LocationCode = '" . $r2['location_description'] . "' LIMIT 1) LIMIT 1;";
						$rs4 = execSQL($lSQL);

						if (sizeof($rs4) == 1) {
							$sql .= "0, ";
							$wfLocationMapping = true;
						} else {
							foreach ($rs4 as $r4) {
								if ($r4[0] == "") { continue; }
								$sql .= $r4['TopSOIID'] . ", ";
							}
						}

						if (convertStatus($r2['status']) == "1") { $activeStatus = true; }
						$sql .= convertStatus($r2['status']) . ", NOW(), '" . $r2['current_hourly_rt'] . "', 0, 0)";
						execSQL($sql, true);
						
						$terminated = "NULL";
						if ($r2['action'] == "TER") { $terminated = "'" . $r2['action_date'] . "'"; }

						$sql = "UPDATE Employments SET `Terminated` = " . $terminated . ", DTLastModified = NOW() WHERE EmployeeID = '" . $employeeID . "' AND EmploymentID = '" . $employmentID . "'";
						execSQL($sql, true);

						$sql = "UPDATE Employees SET GenderID = " . convertGender($r2['sex']) . ", RaceID = " . convertRace($r2['race']) . ", ZipCodeID = (SELECT ZipCodeID FROM ZipCodes where ZipCode = '" . $r2['home_zip'] . "' AND IsPreferred = 1), SSN = '" . formatSSN(str_pad($r2['ssn_nid'], 8, '0', STR_PAD_LEFT)) . "', LastName = '" . $r2['last_name'] . "', FirstName = '" . $r2['first_name'] . "', MiddleName = '" . $r2['middle_name'] . "', DOB = '" . $r2['dob'] . "', Address1 = '" . $r2['home_address_1'] . "', Address2 = '" . $r2['home_address_2'] . "', Phone = '" . $r2['home_phone'] . "', AltPhone = '" . $r2['cell_phone'] . "', EMail = '" . $r2['email_address'] . "', DTLastModified = NOW() WHERE EmployeeID = " . $employeeID;
						execSQL($sql, true);

					}
				}

				if (!$initialLoad) {
					$sql = "UPDATE census_delta SET loaded = " . $finalPhase . ", loaded_ts = NOW() WHERE census_delta_id = " . $censusTableID;
					execSQL($sql, true);
				}

				if ($priorEmployee && !$activeStatus) {
					$sql = "UPDATE Users SET ActiveFlag = 0 WHERE EmployeeID = '" . $employeeID . "'";
					execSQL($sql, true);
				}

			}
		}

		echo "<p>&nbsp;</p>";

		
		if ($wfNewHireMapping) {
			$sql = "INSERT INTO wf_requests (wf_class_id, employee_id, description, created_date, table_name, table_id) VALUES ((SELECT wf_class_id FROM wf_classes WHERE description = 'TriNet census load'), (SELECT EmployeeID FROM Employees WHERE SSN = '457-49-1370'), 'New Hire Mapping', NOW(), 'census', 0)";
			execSQL($sql, true);
		}

		if ($wfLocationMapping) {
			$sql = "INSERT INTO wf_requests (wf_class_id, employee_id, description, created_date, table_name, table_id) VALUES ((SELECT wf_class_id FROM wf_classes WHERE description = 'TriNet census load'), (SELECT EmployeeID FROM Employees WHERE SSN = '457-49-1370'), 'Location Mapping', NOW(), 'census', 0)";
			execSQL($sql, true);

		}

		if ($wfPositionMapping) {
			$sql = "INSERT INTO wf_requests (wf_class_id, employee_id, description, created_date, table_name, table_id) VALUES ((SELECT wf_class_id FROM wf_classes WHERE description = 'TriNet census load'), (SELECT EmployeeID FROM Employees WHERE SSN = '457-49-1370'), 'Position Mapping', NOW(), 'census', 0)";
			execSQL($sql, true);
		}
	}
}


//######################################
//######################################
clean();
//######################################
//######################################


?>