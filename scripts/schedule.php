<?php $loginScreen = false; ?>
<?php require_once("e.php"); ?>
<?php

if (isset($_REQUEST['xargs']) && $_REQUEST['xargs'] == "NAS2018") {
	if ($_REQUEST['action'] == "CHANGE-SCHEDULE") {
		if (isset($_REQUEST['s'])) {
			echo changeSchedule($_REQUEST['l'], $_REQUEST['lc'], date('Y-m-d', strtotime($_REQUEST['s'])));
		} else {
			echo changeSchedule($_REQUEST['l'], $_REQUEST['lc'], date('Y-m-d', strtotime("last Saturday")));
		}
	}

	if ($_REQUEST['action'] == "DETAIL-SCHEDULE") {
		if ($_SESSION['user']->getEmployeeID() == $operationsEmployeeID) {
			echo changeSchedule($_REQUEST['l'], $_REQUEST['lc'], date('Y-m-d', strtotime($_REQUEST['s'])), true);
		} else {
			echo printSchedule($_REQUEST['l'], date('Y-m-d', strtotime($_REQUEST['s'])), true, true);
		}
	}
	
	//######################################
	//######################################
	clean();
	//######################################
	//######################################
}

?>
