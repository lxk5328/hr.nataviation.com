<?php $loginScreen = false; ?>
<?php require_once("scripts/e.php"); ?>
<?php sessionCheck(); ?>
<?php securityCheck("view_permission_mapping", false); ?>
<?php require_once("scripts/top.php"); ?>
<?php $permissionsDisplay = true; ?>

<div style='display: inline;'><a href='/default.php'><img  alt='Return to start page' title='Return to start page' border='0' width='60' src='/images/internal_reload.gif' /></a> &nbsp;&nbsp; <font style="color:#3a90c9; font:bold 30px/1.2em Arial, Helvetica, sans-serif; text-transform:uppercase;">Permission Mapping</font></div>
<center><div id='dt_display_div'><table id='sh_table_id' class="ui celled table" style="width:100%">
	<thead><tr><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Permission ID</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Permission</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Description</font></th></tr></thead><tbody><?php loadPermissionMapping(); ?></tbody></table></div></center>
<p><div id='permissions_div' style='display:none;'></div></p>

<?php require_once("scripts/bottom.php"); ?>
