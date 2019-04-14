<?php $loginScreen = false; ?>
<?php require_once("scripts/e.php"); ?>
<?php sessionCheck(); ?>
<?php require_once("scripts/top.php"); ?>


<div style='display: inline;'><a href='/default.php'><img  alt='Return to start page' title='Return to start page' border='0' width='60' src='/images/internal_reload.gif' /></a> &nbsp;&nbsp; <font style="color:#3a90c9; font:bold 30px/1.2em Arial, Helvetica, sans-serif; text-transform:uppercase;">NetSuite Integration</font></div>
<center>

<form method="post" enctype="multipart/form-data" action="/scripts/x.php">
<input type='hidden' name='xargs' value='NAS2018' />
<input type='hidden' name='action' value='NETSUITE_INTEGRATION' />

<table border='0' id='integration_table'><tr><td class='navtitle' colspan='2'>National Aviation Services NetSuite Integration Form</td></tr>
<tr></tr><tr></tr>

<?php if (isset($_REQUEST['xapp'])) { 
	echo "<tr><td colspan='2'><b><font size='3' face='Arial'>System update successful.</font></b></td></tr>";
}

?>

<tr>
	<td align='left'><font size='2' face='Arial'>Upload a CustomerIDCSV file: </font></td><td align='left' colspan='3'><input type='file' size='40' name='customer_csv' /></td>
</tr>

<tr><td colspan='2'><p>&nbsp;</p></td></tr><tr></tr>

<tr>
	<td align='left'><font size='2' face='Arial'>Upload a ItemsExportCSV file: </font></td><td align='left' colspan='3'><input type='file' size='40' name='items_csv' /></td>
</tr>

<tr><td colspan='2'><p>&nbsp;</p></td></tr><tr></tr>

<tr>
	<td align='left'><font size='2' face='Arial'>Upload a LocationExportCSV file: </font></td><td align='left' colspan='3'><input type='file' size='40' name='locations_csv' /></td>
</tr>

<tr><td colspan='2'><p>&nbsp;</p><br /></td></tr><tr></tr>

<tr><td colspan='2' align='right' style='border-top: 3px; border-color: #000000;'><font size='2' face='Arial'><input id='integration_cancel' style='display:inline' type='button' value='CANCEL'> &nbsp; <input style='display:inline' type='submit' value='SUBMIT'></input></font></td></tr></table>
</center></form>

</td></tr></table>

<?php require_once("scripts/bottom.php"); ?>
