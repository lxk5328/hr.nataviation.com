<?php $loginScreen = false; ?>
<?php require_once("scripts/e.php"); ?>
<?php sessionCheck(); ?>
<?php require_once("scripts/top.php"); ?>
<?php managerCheck(); ?>
<?php $dtScroll = false; ?>
<?php $messagesDisplay = true; ?>


<div style='display: inline;'><a href='/default.php'><img  alt='Return to start page' title='Return to start page' border='0' width='60' src='/images/internal_reload.gif' /></a> &nbsp;&nbsp; <font style="color:#3a90c9; font:bold 30px/1.2em Arial, Helvetica, sans-serif; text-transform:uppercase;">Message Center</font> <div id='message_view_control_div' style='display: inline;'>&nbsp;


<?php

if (isset($_REQUEST['message_filter'])) {
	echo "&nbsp;&nbsp; <input id='message_view_all' type='button' value='VIEW ALL' style='height:30px;width:150px;display:inline;margin:0 auto;' />";
} else {
	echo "&nbsp;&nbsp; <input id='message_view_open' type='button' value='VIEW OPEN' style='height:30px;width:150px;display:inline;margin:0 auto;' />";
}

?>


</div></div>
<center>
	<div id='dt_display_div'><table id='sh_table_id' class="ui celled table" style="width:100%"><?php loadMessages(); ?></table></div><p>&nbsp;</p>
	<div id='message_display'></div><br />
	<div id='station_log_div'><table width='600' border='0' style='border-collapse: collapse; border: 1px solid black;'><tr><td class='navtitle'>National Aviation Services 

	<?php

	if (isset($_REQUEST['l'])) {
		echo $l . " ";
	} else {
		echo $_SESSION['user']->getLocation() . " ";
	}

	?>

	Message Log</td></tr><tr><td><?php loadStationLog(); ?></td></tr></table>

</center>
<p><div id='messages_div' style='display:none;'></div></p>


<?php require_once("scripts/bottom.php"); ?>
