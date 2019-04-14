<?php $loginScreen = false; ?>
<?php require_once("scripts/e.php"); ?>
<?php sessionCheck(); ?>
<?php securityCheck("view_employee_data", false); ?>
<?php require_once("scripts/top.php"); ?>

<?php $dtScroll = false; ?>
<?php $employmentAppDisplay = true; ?>


<div style='display: inline;'><a href='/default.php'><img  alt='Return to start page' title='Return to start page' border='0' width='60' src='/images/internal_reload.gif' /></a> &nbsp;&nbsp; <font style="color:#3a90c9; font:bold 30px/1.2em Arial, Helvetica, sans-serif; text-transform:uppercase;">Employment Applications</font></div>


<br /><center><div id='dt_display_div'><table id='sh_table_id' class="ui celled table" style="width:100%">
	<thead><tr><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Application ID</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Last Name</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>First Name</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Position</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Airport</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Email Address</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Phone Number</font></th></tr></thead>
	<tbody><?php loadEmployeeApplications(); ?></tbody></table></div></center>


<center><form name='employeeApplicationForm'>


<div id='employee_application_div'></div>


</form></center>


<?php require_once("scripts/bottom.php"); ?>
