<?php $loginScreen = false; ?>
<?php require_once("scripts/e.php"); ?>
<?php sessionCheck(); ?>
<?php require_once("scripts/top.php"); ?>
<?php $scheduleDisplay = true; ?>
<?php $projectOptionList = loadProjectOptions(); ?>

<?php

if (isset($_REQUEST['lc']) && $viewGlobalSchedule) {
	$manager = true;
	$location = $_REQUEST['lc'];
} else {
	managerCheck();
}

locationCheck();

 ?>

<form id='shift_schedule_form' name='shift_schedule_form' action='/scripts/x.php?xargs=NAS2018' method='post'>
<div style='display: inline;'><a href='/default.php'><img border='0' width='60' src='/images/internal_reload.gif' alt='Return to start page' title='Return to start page' /></a> &nbsp;&nbsp; <font style="color:#3a90c9; font:bold 30px/1.2em Arial, Helvetica, sans-serif; text-transform:uppercase;">Shift Schedule</font> &nbsp;&nbsp;<div id='schedule_location_div' style='display: inline;'><?php printScheduleLocation(); ?></div> <div id='schedule_week_div' style='display: inline;'></div></div><br /><center><div style='display: inline;'>&nbsp;&nbsp;&nbsp;<select style="height:30px;display:none;margin:0 auto;" name='project' id='project'><?php echo $projectOptionList; ?></select> <input type='text' size='10' name='shift_schedule_date' id='shift_schedule_date' /> &nbsp; <input id='shift_schedule_submit' type='button' value='SUBMIT' style="height:30px;width:150px;display:inline;margin:0 auto;" /> <input id='shift_schedule_cancel' type='button' value='RESET' style="height:30px;width:150px;display:inline;margin:0 auto;" /> <?php if ($manager) { ?><div id='schedule_edit_div' style='display:none;'><input id='shift_schedule_change' type='button' value='CHANGE' style="height:30px;width:150px;display:inline;margin:0 auto;" /></div> <div id='schedule_create_div' style='display:none;'><input id='shift_schedule_save' type='button' value='SAVE SCHEDULE' style='height:30px;width:150px;display:inline;margin:0 auto;' /></div><?php } ?> &nbsp;&nbsp;&nbsp;&nbsp;<div id='schedule_widget_div' style='display:none;'><?php printScheduleWidget(); ?></div></div><br /><p><div id='schedule_display_div'>

<?php

$lockScheduleView = true;
$p0 = $projectOptionDefault;
if (isset($_REQUEST['l'])) { $p0 = $_REQUEST['l']; }

$p1 = $scheduleDate;
if (isset($_REQUEST['s'])) { $p1 = $_REQUEST['s']; }

printSchedule($p0, $p1, $lockScheduleView);
scheduleOpenWorkFlowCheck($location);
$scheduleDate = $p1;

//echo "currentDate: " . $currentDate . "<br />";
//echo "scheduleDate: " . $scheduleDate . "<br />";

?>

<input type='hidden' id='l' value='<?php echo $location; ?>' name='l' />
<input type='hidden' id='lc' value='<?php echo $p0; ?>' name='lc' />
<input type='hidden' id='v' value='<?php echo $lockScheduleView; ?>' name='v' />
</div>


<?php require_once("scripts/bottom.php"); ?>
