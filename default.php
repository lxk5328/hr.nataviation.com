<?php $loginScreen = false; ?>
<?php require_once("scripts/e.php"); ?>
<?php sessionCheck(); ?>
<?php require_once("scripts/top.php"); ?>
<?php managerCheck(); ?>
<!--<p>USER: <?php var_dump($_SESSION['user']); ?></p>-->
<!--<p>POSITION: <?php var_dump($_SESSION['position_security']); ?></p>-->
<!--<p>LOCATION: <?php var_dump($_SESSION['location_security']); ?></p>-->
<!--<p>EMPLOYEE: <?php var_dump($_SESSION['employee_security']); ?></p>-->

<?php

if ($_SESSION['user']->getEmployeeID() == $operationsEmployeeID) {
	$_SESSION['user']->setLocation("Office");
	$_SESSION['user']->setLocationID(104);
	if ($debugMode) { echo "Loaded defaults into location values: " . $_SESSION['user']->getLocationID() . "<br />"; }
}

$w = 33;
$r = false;
$a = false;

if (securityCheck("view_reports", true)) { $r = true; }
if (securityCheck("view_trinet", true)) { $a = true; }
if ($r || $a) { $w = 25; }

?>

<form>
<div style='display: inline;'><font style="color:#3a90c9; font:bold 30px/1.2em Arial, Helvetica, sans-serif; text-transform:uppercase;">Welcome</font> &nbsp;&nbsp;<font style="color:#000000; font:bold 25px/1.2em Arial, Helvetica, sans-serif; text-transform:uppercase;"><?php echo $_SESSION['user']->getFirstName(); ?> <?php echo $_SESSION['user']->getLastName(); ?></font>
	
	<?php if (isset($_REQUEST['xapp'])) { echo "<b>System update successful.</b>"; } ?>

</font></div><div style='float:right' id='shift_clock_display_div'></div>
<table width='100%' border='3' bordercolor='#000000'><tr><td valign='top' width='<?php echo $w; ?>%'>

<table border='0' width='98%'>
	<tr><td colspan='2'><div class='navtitle'>System Management</div></td></tr>
	<tr>
		<td style='padding-right: 0px; vertical-align: top; width: 65px;'>
			<div class="w3-container">
				<div class="w3-dropdown-hover w3-transparent">
				<img border='0' width='60' id='task_list_image' src='/images/task_list.gif' />
				<div class="w3-dropdown-content w3-bar-block w3-border" style='width: 200px;'>
					<a href="/messages.php" class="w3-bar-item w3-button">Message center</a>
					<?php if (securityCheck("view_employee_data", true)) { ?>
					<a href="/employees.php" class="w3-bar-item w3-button">Manage employees</a>
					<a href='/applications.php' class="w3-bar-item w3-button">Employment applications</a>
					<?php } ?>
					<?php if (securityCheck("view_location_mapping", true)) { ?>
					<a href="/locations.php" class="w3-bar-item w3-button">Location mapping</a>
					<?php } ?>
					<?php if (securityCheck("view_permission_mapping", true)) { ?>
					<a href="/permissions.php" class="w3-bar-item w3-button">Permission mapping</a>
					<?php } ?>
					<?php if (securityCheck("view_position_mapping", true)) { ?>
					<a href="/positions.php" class="w3-bar-item w3-button">Position mapping</a>
					<?php } ?>
					<?php if (securityCheck("view_census", true)) { ?>
					<a href="/display.php?xapp=CENSUS" class="w3-bar-item w3-button">TriNet Census list</a>
					<?php } ?>
					<?php if (securityCheck("view_rules_engine", true)) { ?>
					<a href="/display.php?xapp=SYSTEM-RULES" class="w3-bar-item w3-button">Rules engine</a>
					<?php } ?>
					<?php if (securityCheck("view_issue_tracker", true)) { ?>
					<a href="/display.php?xapp=ISSUES" class="w3-bar-item w3-button">Issues tracker</a>
					<?php } ?>
					<?php if (securityCheck("view_security_log", true)) { ?>
					<a href="/security.php" class="w3-bar-item w3-button">Security log</a>
					<?php } ?>
					<?php if (securityCheck("view_aircraft", true)) { ?>
					<a href="/display.php?xapp=AIRCRAFT" class="w3-bar-item w3-button">Aircraft list</a>
					<?php } ?>
					<?php if (securityCheck("view_services", true)) { ?>
					<a href="/display.php?xapp=SERVICES" class="w3-bar-item w3-button">Services list</a>
					<?php } ?>
					<?php if (securityCheck("terminate_employee", true)) { ?>
					<a href="/terminate.php" class="w3-bar-item w3-button">Employee termination</a>
					<?php } ?>
					<?php if (securityCheck("warn_employee", true)) { ?>
					<a href="/warning.php" class="w3-bar-item w3-button">Employee warning</a>
					<?php } ?>
				</div></div>
			</div>
		</td>
<td align='left' valign="top">
		<ul>
			View and take action on system workflows.<br />
		</ul>
</td></tr></table>

</td><td valign='top' width='<?php echo $w; ?>%'>

<table border='0' width='98%'>
	<tr><td colspan='2'><div class='navtitle'>Schedule / Attendance</div></td></tr>
	<tr>
		<td style='padding-right: 0px; vertical-align: top; width: 65px;'>
			<div class="w3-container">
				<div class="w3-dropdown-hover w3-transparent">
					<img border='0' width='60' src='/images/schedule.gif' />
					<div class="w3-dropdown-content w3-bar-block w3-border">
					<a href="/schedule.php" class="w3-bar-item w3-button">Shift schedule</a>
					<?php if (checkShiftReport()) { ?><a href="/attendance.php" class="w3-bar-item w3-button">Shift report</a><?php } ?>
					<?php printTimeClock(); ?>
				</div>
				</div>
			</div>
		</td>
<td align='left' valign="top">
		<ul>
			View schedule and attendance details. &nbsp;<div id='punch_clock_display' style='display:inline;'></div>
		</ul>
</td></tr></table>

</td><td valign='top' width='<?php echo $w; ?>%'>


<table border='0' width='98%'>
	<tr><td colspan='2'><div class='navtitle'>Training Portal</div></td></tr>
	<tr>
		<td style='padding-right: 0px; vertical-align: top; width: 65px;'>
			<div class="w3-container">
				<div class="w3-dropdown-hover w3-transparent">
					<img border='0' width='60' src='/images/paperwork.gif' />
					<div class="w3-dropdown-content w3-bar-block w3-border">
					<a href="/training.php?xapp=REQUIRED" class="w3-bar-item w3-button">Maintenance training</a>
				</div>
				</div>
			</div>
		</td>
<td align='left' valign="top">
		<ul>
			View required training videos and documentation.
		</ul>
</td></tr></table>

<?php if ($r || $a) { echo "</td><td valign='top' width='<?php echo $w; ?>%'>"; } ?>
<?php if ($r) { ?>
<table border='0' width='98%'>
	<tr><td colspan='2'><div class='navtitle'>Reports Dashboard</div></td></tr>
	<tr>
		<td style='padding-right: 0px; vertical-align: top; width: 65px;'>
			<div class="w3-container">
				<div class="w3-dropdown-hover w3-transparent">
					<img border='0' width='60' src='/images/reports.gif' />
					<div class="w3-dropdown-content w3-bar-block w3-border">
					<a href="/reports.php?xapp=POSITIONS" class="w3-bar-item w3-button">Positions report</a>
				</div>
				</div>
			</div>
		</td>
<td align='left' valign="top">
		<ul>
			Run reports and view performance metrics.
		</ul>
</td></tr></table>
<?php } ?>

<p>&nbsp;</p>
</td></tr><tr><td valign='top'>

<table border='0' width='98%'>
	<tr><td colspan='2'><div class='navtitle'>Manage Requests</div></td></tr>
	<tr>
		<td style='padding-right: 0px; vertical-align: top; width: 65px;'>
			<div class="w3-container">
				<div class="w3-dropdown-hover w3-transparent">
					<img border='0' width='60' src='/images/request.gif' />
					<div class="w3-dropdown-content w3-bar-block w3-border">
					<!--<a href="/time.php?xapp=TIMEOFF&t=0" class="w3-bar-item w3-button">Vacation</a>-->
					<a href="/time.php?xapp=TIMEOFF&t=1" class="w3-bar-item w3-button">Birthday</a>
					<a href="/time.php?xapp=TIMEOFF&t=2" class="w3-bar-item w3-button">Sick</a>
					<a href="/time.php?xapp=TIMEOFF&t=3" class="w3-bar-item w3-button">Leave of absence</a>
					<?php if (securityCheck("submit_badge", true)) { ?>
					<a href="#" class="w3-bar-item w3-button">Replacement badge</a>
					<?php } ?>
				</div>
				</div>
			</div>
		</td>
<td align='left' valign="top">
		<ul>
			Submit and track requests in the system.
		</ul>
</td></tr></table>

<p>&nbsp;</p>
</td><td valign='top'>

<table border='0' width='98%'>
	<tr><td colspan='2'><div class='navtitle'>Manage Profile</div></td></tr>
	<tr>
		<td style='padding-right: 0px; vertical-align: top; width: 65px;'>
			<div class="w3-container">
				<div class="w3-dropdown-hover w3-transparent">
					<img border='0' width='60' src='/images/profile.gif' />
					<div class="w3-dropdown-content w3-bar-block w3-border">
					<a href="/profile.php" class="w3-bar-item w3-button">Profile information</a>
					<a href="/contacts.php" class="w3-bar-item w3-button">Emergency contacts</a>
					<a href='#' id='change_passwd' class='w3-bar-item w3-button'>Change password</a>
				</div>
				</div>
			</div>
		</td>
<td align='left' valign="top">
		<ul>
			View your profile and keep your emergency contact up-to-date.<br />
			<div id='change_passwd_div' style='display: none;'><input type='password' name='passwd' id='passwd' size='25' /> <input id='change_passwd_submit' type='button' value='SUBMIT' style="height:30px;width:150px;display:inline;margin:0 auto;" /> <!--<input id='change_passwd_cancel' type='button' value='CANCEL' style="height:30px;width:150px;display:inline;margin:0 auto;" />--></div>

		</ul>
</td></tr></table>

</td><td valign='top'>

<table border='0' width='98%'>
	<tr><td colspan='2'><div class='navtitle'>Suggestions / Help Tickets</div></td></tr>
	<tr>
		<td style='padding-right: 0px; vertical-align: top; width: 65px;'>
			<div class="w3-container">
				<div class="w3-dropdown-hover w3-transparent">
					<img border='0' width='60' src='/images/suggestions.gif' />
					<div class="w3-dropdown-content w3-bar-block w3-border">
					<a href="/interactions.php?xapp=SUGGESTION" class="w3-bar-item w3-button">Submit suggestion</a>
					<a href="/interactions.php?xapp=IT" class="w3-bar-item w3-button">IT help ticket</a>
					<a href="/interactions.php?xapp=HR" class="w3-bar-item w3-button">HR help ticket</a>
				</div>
				</div>
			</div>
		</td>
<td align='left' valign="top">
		<ul>
			Submit suggestions or help tickets.
		</ul>
</td></tr></table>


<?php if ($r || $a) { echo "</td><td valign='top' width='<?php echo $w; ?>%'>"; } ?>
<?php if ($a) { ?>

<table border='0' width='98%'>
	<tr><td colspan='2'><div class='navtitle'>Accounting / Payroll</div></td></tr>
	<tr>
		<td style='padding-right: 0px; vertical-align: top; width: 65px;'>
			<div class="w3-container">
				<div class="w3-dropdown-hover w3-transparent">
					<img border='0' width='60' src='/images/dashboard.gif' />
					<div class="w3-dropdown-content w3-bar-block w3-border">
					<?php if (securityCheck("edit_trinet", true)) { ?>
					<a href="/integration.php" class="w3-bar-item w3-button">Upload NetSuite files</a>
					<?php } ?>
					<a href="/display.php?xapp=CUSTOMERS" class="w3-bar-item w3-button">NetSuite Customer list</a>
					<a href="/display.php?xapp=ITEMS" class="w3-bar-item w3-button">NetSuite Items list</a>
					<a href="/display.php?xapp=LOCATIONS" class="w3-bar-item w3-button">NetSuite Locations list</a>
					
				</div>
				</div>
			</div>
		</td>
<td align='left' valign="top">
		<ul>
			View TriNet / NetSuite integration details.
		</ul>
</td></tr></table>
<?php } ?>

<p>&nbsp;</p>
</td></tr></table>


<?php require_once("scripts/bottom.php"); ?>
