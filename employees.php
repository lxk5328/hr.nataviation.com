<?php $loginScreen = false; ?>
<?php require_once("scripts/e.php"); ?>
<?php sessionCheck(); ?>
<?php securityCheck("view_employee_data", false); ?>
<?php require_once("scripts/top.php"); ?>

<?php $dtScroll = false; ?>
<?php $employeeDisplay = true; ?>
<?php $op = "="; ?>


<div style='display: inline;'><a href='/default.php'><img  alt='Return to start page' title='Return to start page' border='0' width='60' src='/images/internal_reload.gif' /></a> &nbsp;&nbsp; <font style="color:#3a90c9; font:bold 30px/1.2em Arial, Helvetica, sans-serif; text-transform:uppercase;">Manage Employees</font></div><div style='display: inline;'>&nbsp;&nbsp;&nbsp;<select style="height:30px;display:inline;margin:0 auto;" name='employee_view' id='employee_view'><option value='0'>View active employees</option><option value='1'>View terminated employees</option></select> &nbsp; <input id='employee_submit' type='button' value='SUBMIT' style="height:30px;width:150px;display:inline;margin:0 auto;" /> <input id='employee_cancel' type='button' value='RESET' style="height:30px;width:150px;display:inline;margin:0 auto;" /></div> &nbsp; 

<div id='employee_view_div' style='display: inline; border: solid; border-color: #000000; padding: 5px;'>&nbsp;&nbsp;&nbsp;CURRENT VIEW: 

		<?php if (isset($_REQUEST['v'])) {
			if ($_REQUEST['v'] == "0") {
				echo "active employees";
			} else {
				$op = "!=";
				echo "terminated employees";
			}
		} else {
			echo "active employees";
		}
		?>

	</div>

<div id='employee_control_div' style='display: none;'>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select style="height:30px;display:inline;margin:0 auto;" name='view_profile' id='view_profile'><option value='1'>View employee profile</option><option value='2'>View employment history</option><option value='3'>View employment status</option><?php if (securityCheck("reset_password", false, false) && ((isset($_REQUEST['v']) && $_REQUEST['v'] != "1") || !isset($_REQUEST['v']))) { ?><option value='4'>Reset password</option><?php } ?><?php if (securityCheck("suspend_employee", false, false) && ((isset($_REQUEST['v']) && $_REQUEST['v'] != "1") || !isset($_REQUEST['v']))) { ?><option value='5'>Suspend</option><?php } ?></select> <input style='display: none;' type='password' name='passwd' id='passwd' size='25' /> <input id='employee_profile_submit' type='button' value='SUBMIT' style="height:30px;width:150px;display:inline;margin:0 auto;" />
</div>

<br /><center><div id='dt_display_div'><table id='sh_table_id' class="ui celled table" style="width:100%">
	<thead><tr><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Employee ID</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Last Name</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>First Name</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Middle Name</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Birth Date</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Modified Date</font></th></tr></thead>
	<tbody><?php loadEmployees($op); ?></tbody></table></div></center>


<center><form name='employeeForm'>


<div id='profile_div'></div>


</form></center>


<?php require_once("scripts/bottom.php"); ?>
