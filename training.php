<?php $loginScreen = false; ?>
<?php require_once("scripts/e.php"); ?>
<?php sessionCheck(); ?>
<?php require_once("scripts/top.php"); ?>

<form id='training_form' name='training_form' action='/scripts/x.php?xargs=NAS2018&action=TRAINING&VIDEO=REQUIRED' method='post'>
<div style='display: inline;'><a href='/default.php'><img  alt='Return to start page' title='Return to start page' border='0' width='60' src='/images/internal_reload.gif' /></a> &nbsp;&nbsp; <font style="color:#3a90c9; font:bold 30px/1.2em Arial, Helvetica, sans-serif; text-transform:uppercase;">Training Portal</font></div>

<center><table border='1' width='60%'><tr><td>
<p align="center"><span style="font-size: 14pt"><b>*WARNING* THE 
				CONTENTS OF THIS PORTAL SHOULD BE TREATED AS SENSITIVE SECURITY 
				INFORMATION, AS MUCH OF IT CONCERNS SAFETY AND SECURITY 
				OPERATIONS REGARDING THE SEARCH OF AIRCRAFT, AND TRADE SECRETS 
				THAT ARE NOT TO BE SHARED WITH ANYONE NOT IN THE EMPLOY OF 
				NATIONAL AVIATION SERVICES. PLEASE LET THE VIDEO RUN ALL THE WAY TO COMPLETION IN ORDER TO SUBMIT YOUR SIGNATURE.</b></span></p></td></tr></table>

<p>&nbsp;</p>
<div id='video_agree_div' style='display:none;'><font size='3' face='Arial'>I agree that I have watched the entire video: <input type='checkbox' name='agree' /> &nbsp; &nbsp; My initials: <input type='text' name='initials' size='4' /> &nbsp;&nbsp; <input type='submit' value='SUBMIT' style="height:30px;width:150px;display:inline;margin:0 auto;" /></font></div><br /><video src="http://www.naslibrary.com/guides/maint/maintrain.mp4" width="768" height="512" controls></video></center>



<?php require_once("scripts/bottom.php"); ?>
