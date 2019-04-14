<?php $loginScreen = false; ?>
<?php require_once("e.php"); ?>
<?php

if (isset($_REQUEST['xargs']) && $_REQUEST['xargs'] == "NAS2018") {
	if ($_REQUEST['action'] == "REASONS") {
		$sql = "SELECT TerminationTypeID, TerminationType FROM TerminationTypes WHERE Voluntary = ";
		if ($_REQUEST['r'] == "voluntary") { $sql .= "1"; } else { $sql .= "0"; }
		$rs = execSQL($sql);

		$cout = "<select id='type_id' name='type_id'>";

		foreach ($rs as $r) {
			if ($r[0] == "") { continue; }
			$cout .= "<option value='" . $r['TerminationTypeID'] . "'>" . $r['TerminationType'] . "</option>";
		}

		$cout .= "</select>";

		echo $cout;

	}

	//######################################
	//######################################
	clean();
	//######################################
	//######################################
}


?>