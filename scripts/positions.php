<?php $loginScreen = false; ?>
<?php require_once("e.php"); ?>
<?php

if (isset($_REQUEST['xargs']) && $_REQUEST['xargs'] == "NAS2018") {
	if ($_REQUEST['action'] == "POSITIONS") {
		$authorized = securityCheck("edit_position_mapping", true);
		$shirtStyleArray = array();
		$rowArray = array();
		$checked = null;
		loadPositions();

		$cout = "<form name='positions_form' id='positions_form'><center><table border='0' id='positions_top_level_table'><tr><td class='navtitle' colspan='2'>National Aviation Services Position Mapping</td></tr><tr><td colspan='2'><b><font size='3' face='Arial'><div id='positions_ajax_div'></div></font></b></td></tr>";

		
		$sql = "SELECT Description, ShirtStyleID, DTLastModified, IsRegionalMgr, IsManager, IsTSA, FAADocs FROM Positions WHERE PositionID = " . $_REQUEST['pid'];
	    $rs = execSQL($sql);

		foreach($rs as $r) {
			if ($r[0] == "") { continue; }
			$rowArray = $r;
		}

		$sql = "SELECT ShirtStyleID, ShirtStyle FROM ShirtStyles";
		$shirtStyleArray = execSQL($sql);


		$cout .= "<input id='pid' type='hidden' name='pid' value='" . $_REQUEST['pid'] . "' />";

		$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Description:</b></td><td style='padding: 7px; text-align: left;'><input id='Description' name='Description' type='text' size='62' value='" . $rowArray[0] . "' /></td></tr>";

		$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Shirt Style:</b></td><td style='padding: 7px; text-align: left;'><select id='ShirtStyleID' name='ShirtStyleID'>";
		foreach ($shirtStyleArray as $s) {
			$selected = "";
			if ($s[0] == $rowArray['ShirtStyleID']) { $selected = " selected"; }
			$cout .= "<option" . $selected . " value='" . $s[0] . "'>" . $s[1] . "</option>";
		}
		$cout .= "</select></td></tr>";

		if ($rowArray['IsRegionalMgr'] == "1") { $checked = " checked"; } else { $checked = null; }
		$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Regional Manager:</b></td><td style='padding: 7px; text-align: left;'><input id='IsRegionalMgr' name='IsRegionalMgr' type='checkbox'" . $checked . " /></td></tr>";

		if ($rowArray['IsManager'] == "1") { $checked = " checked"; } else { $checked = null; }
		$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Manager:</b></td><td style='padding: 7px; text-align: left;'><input id='IsManager' name='IsManager' type='checkbox'" . $checked . " /></td></tr>";

		if ($rowArray['IsTSA'] == "1") { $checked = " checked"; } else { $checked = null; }
		$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>TSA:</b></td><td style='padding: 7px; text-align: left;'><input id='IsTSA' name='IsTSA' type='checkbox'" . $checked . " /></td></tr>";

		if ($rowArray['FAADocs'] == "1") { $checked = " checked"; } else { $checked = null; }
		$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>FAA Docs:</b></td><td style='padding: 7px; text-align: left;'><input id='FAADocs' name='FAADocs' type='checkbox'" . $checked . " /></td></tr>";

		$cout .= "<tr><td style='padding: 7px; text-align: left;'><b>Last Modified:</b></td><td style='padding: 7px; text-align: left;'>" . $rowArray['DTLastModified'] . "</td></tr>";

		$cout .= "<tr><td colspan='2' align='right'><input id='positions_modify_cancel' type='button' value='RESET' /> &nbsp; ";

		if ($authorized) { $cout .= "<input id='positions_submit' type='button' value='SUBMIT' />"; }

		$cout .= "</td></tr><tr><td colspan='2'><p>&nbsp;</p></td></tr><tr><td colspan='2'><table border='1' bordercolor='#000000' id='positions_table' width='100%'><tr><td class='navtitle'>Active Census Business Title Mapping</td></tr></table></td></tr><tr><td colspan='2'><p>&nbsp;</p></td></tr>";

		if ($authorized) {
			$cout .= "<tr><td colspan='2'><img title='Search census business titles' alt='Search census business titles' id='position_search' border='0' src='/images/glyphicons-28-search.png' style='cursor: pointer;' /> &nbsp; <img title='List census business titles' alt='List census business titles' id='position_list' border='0' src='/images/glyphicons-115-list.png' style='cursor: pointer;' /> &nbsp;&nbsp;&nbsp;&nbsp; <div id='position_search_div' style='display: inline;'></div> <div id='position_list_div' style='display: inline;'></div> <div id='position_add_div' style='display:none;'><input type='button' id='position_add' value='ADD' /></div></td></tr>";


		}

		$cout .= "<input type='hidden' name='position_array' id='position_array' value='" . implode(",", $positionArray) . "' />";
		$cout .= "<input type='hidden' name='authorized' id='authorized' value='" . $authorized . "' />";

		$cout .= "</table></center></form>";
		echo $cout;

	}

	if ($_REQUEST['action'] == "POSITIONS-LIST") {

        $cout = "<select name='business_title' id='business_title'>";
		$sql = "SELECT DISTINCT business_title FROM census WHERE business_title NOT IN (SELECT DISTINCT business_title FROM position_mapping) ORDER BY business_title";
		$rs = execSQL($sql);

		foreach ($rs as $r) {
			if ($r[0] == "") { continue; }
			$cout .= "<option value='" . $r[0] . "'>" . strtoupper($r[0]) . "</option>";
		}

		$cout .= "</select>";
		echo $cout;
	}

	if ($_REQUEST['action'] == "POSITIONS-SEARCH") {

		$cout = "";
		$sql = "SELECT DISTINCT business_title FROM census WHERE business_title NOT IN (SELECT DISTINCT business_title FROM position_mapping) AND business_title LIKE '%" . formatQuotes($_REQUEST['s']) . "%' ORDER BY business_title";
		$rs = execSQL($sql);

		if (sizeof($rs) > 1) {
			$cout .= "<select name='business_title' id='business_title'>";
			foreach ($rs as $r) {
				if ($r[0] == "") { continue; }
				$cout .= "<option value='" . $r[0] . "'>" . strtoupper($r[0]) . "</option>";
			}
			$cout .= "</select>";
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