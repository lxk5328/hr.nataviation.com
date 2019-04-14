<?php $loginScreen = false; ?>
<?php require_once("scripts/e.php"); ?>
<?php sessionCheck(); ?>
<?php securityCheck("view_security_log", false); ?>
<?php require_once("scripts/top.php"); ?>
<?php $securityDisplay = true; ?>

<div style='display: inline;'><a href='/default.php'><img  alt='Return to start page' title='Return to start page' border='0' width='60' src='/images/internal_reload.gif' /></a><font style="color:#3a90c9; font:bold 30px/1.2em Arial, Helvetica, sans-serif; text-transform:uppercase;">Security Log</font></div>
<center><div id='dt_display_div'><table id='sh_table_id' class="ui celled table" style="width:100%">
	<thead><tr><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Log ID</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Employee</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>IP Address</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Action</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Result</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>TimeStamp</font></th></tr></thead><tbody><?php loadSecurityLog(); ?></tbody></table></div></center>

<?php require_once("scripts/bottom.php"); ?>
