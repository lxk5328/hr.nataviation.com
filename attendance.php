<?php $loginScreen = false; ?>
<?php require_once("scripts/e.php"); ?>
<?php sessionCheck(); ?>
<?php require_once("scripts/top.php"); ?>

<?php 
$attendanceDisplay = true;

if ($_SESSION['user']->getEmployeeID() == $operationsEmployeeID) {
	if (isset($_REQUEST['l'])) {
		$sql = "SELECT LocationID, LocationCode FROM Locations WHERE SUBSTRING_INDEX(LocationCode, \" \", 1) = (SELECT AirportCode FROM ArptRsrvCntr WHERE ArptRsrvCntrID = " . $_REQUEST['l'] . ") LIMIT 1";
		$rs = execSQL($sql);
		foreach ($rs as $r) {
			if ($r[0] == "") { continue; }
			$_SESSION['user']->setLocationID($r[0]);
			$_SESSION['user']->setLocation(explode(" ", $r['LocationCode'])[0]);
			break;
		}
	} else {
		$_SESSION['user']->setLocation("ABQ");
		$_SESSION['user']->setLocationID(109);
	}
}

$airportOptionList = loadAirportOptions();

if (isset($_REQUEST['lc']) && $viewGlobalSchedule) {
	$manager = true;
	$location = $_REQUEST['lc'];
} else {
	managerCheck();
}

locationCheck();

?>

<form id='shift_attendance_form' name='shift_attendance_form' action='/scripts/x.php?xargs=NAS2018' method='post'><input type='hidden' name='action' value='SHIFT-REPORT' />
<div style='display: inline;'><a href='/default.php'><img border='0' width='60' src='/images/internal_reload.gif' alt='Return to start page' title='Return to start page' /></a> &nbsp;&nbsp; <font style="color:#3a90c9; font:bold 30px/1.2em Arial, Helvetica, sans-serif; text-transform:uppercase;">Shift Report</font> &nbsp;&nbsp;<div id='attendance_location_div' style='display: inline;'><select style="height:30px;display:inline;margin:0 auto;" name='airport' id='airport'><?php echo $airportOptionList; ?></select> <div id='shift_control_div' style='display: none;'><input type='submit' name='save' value='SAVE' style="height:30px;width:150px;display:inline;margin:0 auto;" /> &nbsp; <?php if (!$viewGlobalSchedule) { ?><input type='button' style="height:30px;width:150px;display:inline;margin:0 auto;" id='shift_override' name='shift_override' value='OVERRIDE' /><?php } ?> &nbsp; <input name='submit' type='submit' value='SUBMIT' style="height:30px;width:150px;display:inline;margin:0 auto;" /> </div><p>&nbsp;</p><center>


<?php

if ($manager || in_array($_SESSION['user']->getPositionID(), $shiftReportLeads)) {
	$p0 = $airportOptionDefault;
	if (isset($_REQUEST['l'])) { $p0 = $_REQUEST['l']; }
	if (shiftReportAvailable($p0)) {
		$shiftReportAvailable = true;
		generateAttendance($_SESSION['user']->getLocationID(), true);
	} else {
		generateAttendance($_SESSION['user']->getLocationID(), false);
	}
} else {
	generateAttendance($_SESSION['user']->getLocationID(), false);
}

?>

<input type='hidden' id='l' value='<?php echo $location; ?>' name='l' />
<input type='hidden' id='lc' value='<?php echo $p0; ?>' name='lc' />
</center></div>


<?php require_once("scripts/bottom.php"); ?>
