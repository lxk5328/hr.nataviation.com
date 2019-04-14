<?php $loginScreen = false; ?>
<?php require_once("scripts/e.php"); ?>
<?php sessionCheck(); ?>
<?php require_once("scripts/top.php"); ?>
<?php $interactionType = null; ?>
<?php $interactionTitle = null; ?>



<div style='display: inline;'><a href='/default.php'><img  alt='Return to start page' title='Return to start page' border='0' width='60' src='/images/internal_reload.gif' /></a> &nbsp;&nbsp; <font style="color:#3a90c9; font:bold 30px/1.2em Arial, Helvetica, sans-serif; text-transform:uppercase;">

<?php
if (isset($_REQUEST['xapp']) && $_REQUEST['xapp'] == "SUGGESTION") {
	echo "Submit a suggestion";
	$interactionType = "SUGGESTION";
	$interactionTitle = "Suggestion";
} else if (isset($_REQUEST['xapp']) && $_REQUEST['xapp'] == "HR") {
	echo "HR Help ticket";
	$interactionType = "HR";
	$interactionTitle = "HR Help ticket";
} else {
	echo "IT Help ticket";
	$interactionType = "IT";
	$interactionTitle = "IT Help ticket";
}

?>

</font></div>

<?php
if (isset($_REQUEST['xapp-db'])) {
	echo "<p>&nbsp;</p><div style='padding-left:10px';><font size='3' face='Arial'><b>Thank you for sending your feedback.</b> We will review your information and be in touch with you when we can.</font></div>";
} else {
?>

<p>&nbsp;</p><center><form action='scripts/x.php' method='post' name='interactionsForm'><input type='hidden' name='xargs' value='NAS2018' /><input type='hidden' name='action' value='INTERACTIONS' /><input type='hidden' name='interaction_type' value='<?php echo $interactionType; ?>' /><table border='0'><tr><td class='navtitle'>National Aviation Services <?php echo $interactionTitle; ?> Form</td></tr><tr><td align='left' valign='top'><font size='2' face='Arial'>Comments</font><br /><font size='2' face='Arial'><textarea rows='4' cols='78' name='description' required></textarea></font></td></tr><tr><td align='right'> <font size='2' face='Arial'><input style='display:inline' type='submit' value='SUBMIT' /></font></td></tr></table></form></center>

<?php } ?>

<?php require_once("scripts/bottom.php"); ?>
