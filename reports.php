<?php $loginScreen = false; ?>
<?php require_once("scripts/e.php"); ?>
<?php sessionCheck(); ?>
<?php securityCheck("view_reports", false); ?>
<?php require_once("scripts/top.php"); ?>
<?php $reportsDisplay = true; ?>

<div style='display: inline;'><a href='/default.php'><img  alt='Return to start page' title='Return to start page' border='0' width='60' src='/images/internal_reload.gif' /></a> &nbsp;&nbsp; <font style="color:#3a90c9; font:bold 30px/1.2em Arial, Helvetica, sans-serif; text-transform:uppercase;">Reports Dashboard</font></div>

<center><table border='0' width='60%'><tr><td>
	
<div id='reports_display_div'></div>

</td></tr></table></center>

<?php require_once("scripts/bottom.php"); ?>
