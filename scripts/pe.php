<?php $loginScreen = false; ?>
<?php require_once("e.php"); ?>
<?php

if (isset($_REQUEST['xargs']) && $_REQUEST['xargs'] == "NAS2018") {
	if ($_REQUEST['action'] == "EMPLOYEE-PERMISSIONS") {

		$cout = "<input type='hidden' name='EMPLOYEE-ACTION' value='EMPLOYEE-PERMISSIONS-MODIFY' />";
		$cout .= "<table><tr><td style='padding: 7px; text-align: left;' colspan='2'><b>Last Name</b> &nbsp; <input type='text' size='25' name='employee' id='employee' placeholder='Search...' /> &nbsp; <input type='button' name='employee_search' id='employee_search' value='SEARCH' /> <div id='employee_search_results_div' style='display: inline;'></div></td></tr></table>";

		echo $cout;

	}

	if ($_REQUEST['action'] == "EMPLOYEE-SEARCH") {

		$cout = "";
		$sql = "SELECT e.EmployeeID, e.FirstName, e.LastName FROM Employees e, census c WHERE e.LastName LIKE '" . formatQuotes($_REQUEST['employee']) . "%' AND LPAD(c.ssn_nid,9,0) = REPLACE(e.SSN,'-','') AND c.status = 'Active'";
		$rs = execSQL($sql);

		if (sizeof($rs) > 1) {
			$cout = "<select name='employee_list' id='employee_list'>";
			foreach ($rs as $r) {
				if ($r[0] == "") { continue; }
				$cout .= "<option value='" . $r['EmployeeID'] . "'>" . $r['FirstName'] . " " . $r['LastName'] . "</option>";
			}
			$cout .= "</select> <input type='button' name='employee_permissions_add' id='employee_permissions_add' value='ADD' />";
		} else {
			$cout = "No employee found... please try again.";
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