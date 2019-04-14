<?php $loginScreen = false; ?>
<?php require_once("scripts/e.php"); ?>
<?php sessionCheck(); ?>
<?php securityCheck("view_position_mapping", false); ?>
<?php require_once("scripts/top.php"); ?>
<?php $positionsDisplay = true; ?>

<div style='display: inline;'><a href='/default.php'><img  alt='Return to start page' title='Return to start page' border='0' width='60' src='/images/internal_reload.gif' /></a> &nbsp;&nbsp; <font style="color:#3a90c9; font:bold 30px/1.2em Arial, Helvetica, sans-serif; text-transform:uppercase;">Position Mapping</font></div>
<center><div id='dt_display_div'><table id='sh_table_id' class="ui celled table" style="width:100%">
	<thead><tr><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Position ID</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Description</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Shirt Style</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Employee Count</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Census Mapped Count</font></th></tr></thead><tbody><?php loadPositionMapping(); ?></tbody></table></div></center>
<p><div id='positions_div' style='display:none;'></div></p>


<?php require_once("scripts/bottom.php"); ?>
