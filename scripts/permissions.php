<?php $loginScreen = false; ?>
<?php require_once("e.php"); ?>
<?php

if (isset($_REQUEST['xargs']) && $_REQUEST['xargs'] == "NAS2018") {
	if ($_REQUEST['action'] == "PERMISSIONS") {
		$authorized = securityCheck("edit_permission_mapping", true);
		$rowArray = array();
		loadPermissions();

		$cout = "<form name='permissions_form' id='permissions_form'><center><table border='0' id='permissions_top_level_table'><tr><td class='navtitle' colspan='2'>National Aviation Services Permission Mapping</td></tr><tr><td colspan='2'><b><font size='3' face='Arial'><div id='permissions_ajax_div'></div></font></b></td></tr>";

		
		$sql = "SELECT permissions_id, permission, description FROM permissions WHERE permissions_id = " . $_REQUEST['pid'];
	    $rs = execSQL($sql);

		foreach($rs as $r) {
			if ($r[0] == "") { continue; }
			$rowArray = $r;
		}

		$cout .= "<input id='pid' type='hidden' name='pid' value='" . $rowArray[0] . "' />";

		$cout .= "<tr><td style='padding: 7px; text-align: left;' colspan='2'><b>Name:</b> &nbsp; <i>" . $rowArray[1] . "</i></td></tr>";
		$cout .= "<tr><td  colspan='2' style='padding: 7px; text-align: left;'><b>Description:</b><br /><textarea id='description' name='description' rows='2' cols='100' required='required'>" . $rowArray[2] . "</textarea></td></tr>";

		$cout .= "<tr><td colspan='2' align='right'><input id='permissions_modify_cancel' type='button' value='RESET' /> &nbsp; ";

		if ($authorized) { $cout .= "<input id='permissions_submit' type='button' value='SUBMIT' />"; }

		$cout .= "</td></tr><tr><td colspan='2'><p>&nbsp;</p></td></tr><tr><td colspan='2'><table border='1' bordercolor='#000000' id='permissions_table' width='100%'><tr><td class='navtitle'>Active Permissions</td></tr></table></td></tr><tr><td colspan='2'><p>&nbsp;</p></td></tr>";

		if ($authorized) {
			$cout .= "<tr><td><div class='button-group' display:inline;'>Add  <button type='button' class='btn btn-default btn-sm dropdown-toggle' data-toggle='dropdown' style='background-color: #efefef'><span class='glyphicon glyphicon-user'></span> <span class='caret'></span></button><ul class='dropdown-menu'><li>&nbsp;&nbsp; <input type='checkbox' name='permission_position' id='permission_position'>Position</input></li><li>&nbsp;&nbsp; <input type='checkbox' name='permission_location' id='permission_location'>Location</input></li><li>&nbsp;&nbsp; <input type='checkbox' name='permission_employee' id='permission_employee'>Employee</input></li></ul></div></td><td><div id='permissions_0_display_div'></div></td><tr><tr><td>&nbsp;</td><td><div id='permissions_1_display_div'></div></td></tr><tr><td>&nbsp;</td><td><div id='permissions_2_display_div'></div></td></tr>";
		}

		$cout .= "<input type='hidden' name='position_array' id='position_array' value='" . implode(",", $positionArray) . "' />";
		$cout .= "<input type='hidden' name='location_array' id='location_array' value='" . implode(",", $locationArray) . "' />";
		$cout .= "<input type='hidden' name='employee_array' id='employee_array' value='" . implode(",", $employeeArray) . "' />";
		$cout .= "<input type='hidden' name='authorized' id='authorized' value='" . $authorized . "' />";

		$cout .= "</table></center></form>";
		echo $cout;

	}

	//######################################
	//######################################
	clean();
	//######################################
	//######################################
}

?>