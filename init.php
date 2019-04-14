<?php $loginScreen = false; ?>
<?php require_once("scripts/e.php"); ?>
<?php initCheck("login_bypass"); ?>
<!DOCTYPE html>
<html>
<head>
    <title>National Aviation Services</title>
    <meta content="text/html;charset=utf-8" http-equiv="Content-Type" />
    <META HTTP-EQUIV="Pragma" CONTENT="no-cache" />
    <meta http-equiv="expires" content="0" />
    <meta name="description" content="National Aviation Services - Aircraft Appearance and Maintenance Experts; Facility Maintenance Specialists" />
    <meta name="keywords" content="Aircraft Maintenance,Aircraft Cleaning,Aircraft Appearance" />
    <meta name="author" content="National Aviation Services" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="/css/jquery-ui.css" />
    <link rel="stylesheet" href="/css/semantic.min.css" />
    <link rel="stylesheet" href="/css/w3.css" type="text/css" />
    <link rel="stylesheet" href="/css/main.css" type="text/css" />
    <link rel="stylesheet" href="/css/dataTables.semanticui.min.css" />
    <script src="/js/jquery-3.3.1.js"></script>
    <script src="/js/semantic.min.js"></script>
    <script type='text/javascript'>
        function checkPasswordForm(form) {
            if (form.password.value == "") { alert("Please choose a password."); form.password.focus(); return false; }
            if (form.password.value != form.password2.value) { alert("Passwords do not match. Please correct the problem."); form.password.focus(); return false; }
            for (var z = 1; z < 4; z++) { if ($("#challenge_answer_" + z).val() == "") { alert("Please enter a challenge answer."); $("#challenge_answer_" + z).focus(); return false; }}
            return true;
        }
    </script>
</head>
<body>
<p>&nbsp;</p><p>&nbsp;</p>
<div id='container' style='vertical-align: center;'>
<center>
<table border='2' bordercolor='#000000'>
<tr bgcolor='#ffffff'>
    <td width='185' rowspan='2' valign='top'><center>
        <div style='background-color: #0a2e68'><div style='border: 3px solid black; border-top-left-radius: 25px; background-color: #ffffff;'><a href='http://w3.nataviation.com/'><img border='0' src='/images/nat-logo-500.png' width='175' /></a><br /><p>&nbsp;</p><img id='step2' border='0' src='/images/step2.png'>
        </div></div></center></td><td><p>&nbsp;</p>

            <table border='0' width='550'><tr><td><font size='3' face='Arial'><b>Please choose a password</b> and then enter it along with a verification. Be sure to setup three challenge phrases to use if you ever need to reset your password.</font></td></tr></table><p>&nbsp;</p> 

<center><form action='/scripts/x.php' method='post' name='initForm' onSubmit='return checkPasswordForm(this);return (false);'><input type='hidden' name='action' value='INIT' /><input type='hidden' name='xargs' value='NAS2018' /><table border='0'><tr><td class='navtitle' colspan='2'>National Aviation Services System Login</td></tr><tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'>Choose password</font></td><td style='padding: 7px; text-align: left;'><input type='password' size='42' name='password' id='password' /></td></tr><tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'>Verify password</font></td><td style='padding: 7px; text-align: left;'><input type='password' size='42' name='password2' id='password2' /></td></tr>

<tr><td colspan='2'>&nbsp;</td></tr>
<tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'>Challenge question #1</font></td><td style='padding: 7px; text-align: left;'><select name='q1'><option value='2'>What was your favorite sports team when you were in high school?</option><option value='0'>What is your pet's name?</option><option value='1'>What color was your first car?</option><option value='3'>What is your favorite ice cream flavor?</option></td></tr>
<tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'>Challenge answer #1</font></td><td style='padding: 7px; text-align: left;'><input type='text' size='57' name='a1' id='a1' /></td></tr>

<tr><td colspan='2'>&nbsp;</td></tr>
<tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'>Challenge question #2</font></td><td style='padding: 7px; text-align: left;'><select name='q2'><option value='6'>What is your favorite aircraft?</option><option value='4'>What was the name of the city where you were born?</option><option value='5'>What city did you live in when you were 10 years old?</option><option value='7'>What is your favorite color?</option></td></tr>
<tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'>Challenge answer #2</font></td><td style='padding: 7px; text-align: left;'><input type='text' size='57' name='a2' id='a2' /></td></tr>

<tr><td colspan='2'>&nbsp;</td></tr>
<tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'>Challenge question #3</font></td><td style='padding: 7px; text-align: left;'><select name='q3'><option value='10'>What was your favorite place to visit as a child?</option><option value='8'>Who is your favorite actor, musician, or artist?</option><option value='9'>What is the name of your first school?</option><option value='11'>Which is your favorite web browser?</option></td></tr>
<tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'>Challenge answer #3</font></td><td style='padding: 7px; text-align: left;'><input type='text' size='57' name='a3' id='a3' /></td></tr>


    <tr><td>&nbsp;</td><td align='right'><font size='2' face='Arial'><input type='submit' value='CONTINUE'></input></font>&nbsp;</td></tr></table></form>
</center></td></tr></table></center>
</div>

</body>
</html>
