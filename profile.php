<?php $loginScreen = false; ?>
<?php require_once("scripts/e.php"); ?>
<?php sessionCheck(); ?>
<?php require_once("scripts/top.php"); ?>
<?php $profileDisplay = true; ?>

<div style='display: inline;'><a href='/default.php'><img  alt='Return to start page' title='Return to start page' border='0' width='60' src='/images/internal_reload.gif' /></a> &nbsp;&nbsp; <font style="color:#3a90c9; font:bold 30px/1.2em Arial, Helvetica, sans-serif; text-transform:uppercase;">Profile Information</font></div><div style='display: inline;'>&nbsp;&nbsp;&nbsp;<select style="height:30px;display:inline;margin:0 auto;" name='view_profile' id='view_profile'><option value=''>View census profile</option><option value='1'>View employee profile</option><option value='2'>View employment history</option><option value='3'>View employment status</option></select>  <input id='profile_submit' type='button' value='SUBMIT' style="height:30px;width:150px;display:inline;margin:0 auto;" /> <input id='profile_cancel' type='button' value='RESET' style="height:30px;width:150px;display:inline;margin:0 auto;" /></div>




<center><form name='profileForm'>


<div id='profile_div'></div>


</form></center>

</td></tr></table>

<?php require_once("scripts/bottom.php"); ?>
