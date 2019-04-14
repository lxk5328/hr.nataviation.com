<?php $loginScreen = false; ?>
<?php require_once("scripts/e.php"); ?>
<?php sessionCheck(); ?>
<?php securityCheck("view_location_mapping", false); ?>
<?php require_once("scripts/top.php"); ?>

<?php $dtScroll = true; ?>
<?php $locationsDisplay = true; ?>
<?php $censusDisplay = false; ?>

<div style='display: inline;'><a href='/default.php'><img  alt='Return to start page' title='Return to start page' border='0' width='60' src='/images/internal_reload.gif' /></a> &nbsp;&nbsp; <font style="color:#3a90c9; font:bold 30px/1.2em Arial, Helvetica, sans-serif; text-transform:uppercase;">Location Mapping</font></div>
<center><div id='dt_display_div'><table id='sh_table_id' class="ui celled table" style="width:100%">
	<thead><tr><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Airport Name</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Airport Code</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Director</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Top Level Manager</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Station Manager</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>State</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Location Code</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>NetSuite Code</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Burden %</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Modified Date</font></th></tr></thead>
	<tbody><?php loadLocationMapping(); ?></tbody></table></div></center>
<p><div id='locations_div' style='display:none;'></div></p>


<?php require_once("scripts/bottom.php"); ?>
