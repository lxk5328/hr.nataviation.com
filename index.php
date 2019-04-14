<?php $loginScreen = true; ?>
<?php if (isset($_REQUEST['step'])) { $loginScreen = false; } ?>
<?php require_once("scripts/e.php"); ?>

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
</head>
<body>
<p>&nbsp;</p><p>&nbsp;</p>
<div id='container' style='vertical-align: center;'>
<center>
<table border='2' bordercolor='#000000'>
<tr bgcolor='#ffffff'>
    <td width='185' rowspan='2' valign='top'><center>
        <div style='background-color: #0a2e68'><div style='border: 3px solid black; border-top-left-radius: 25px; background-color: #ffffff;'><a href='http://w3.nataviation.com/'><img border='0' src='/images/nat-logo-500.png' width='175' /></a><br /><p>&nbsp;</p>
            <?php if (isset($_REQUEST['step']) && $_REQUEST['step'] == "2") {
                echo "<img id='step2' style='display: none;' border='0' src='/images/step2.png'>";
            } else {
                echo "<img id='step1' style='display: none;' border='0' src='/images/step1.png'>";
            }
            ?>

        </div></div></center></td><td valign='top'>


        <div id='tabs' class="ui top attached tabular menu">
          <?php if (isset($_REQUEST['step'])) { ?>
            <a class="item" data-tab="first">System Login</a>
            <a class="active item" data-tab="second">Employee Onboarding</a>
            <a class="item" data-tab="third">Password Reset</a>
        <?php } else { ?>
            <a class="active item" data-tab="first">System Login</a>
            <a class="item" data-tab="second">Employee Onboarding</a>
            <a class="item" data-tab="third">Password Reset</a>
        <?php } ?>
        </div>

        <?php if (isset($_REQUEST['step'])) { ?>
        <div class="ui bottom attached tab segment" data-tab="first">
        <?php } else { ?>
        <div class="ui bottom attached active tab segment" data-tab="first">
        <?php } ?>
            <p>&nbsp;</p><table border='0' width='550'><tr><td>

            <?php if (isset($_REQUEST['xlogin']) || isset($_REQUEST['xlogout'])) { echo "<b>System login failed.</b> Please correct the problem with your login or password. Your login ID should be your SSN. If you are still having problems, then please try a password reset.<br /><br /><b>Onboarding is a requirement for all users</b>, so please complete the onboarding process if you haven't done that already.<br /><br />If you are unable to get into the system then please send an email to <a href='mailto:info@nataviation.com'>info@nataviation.com</a> and you will be contacted by email.<br /><br />"; } ?>

            <?php if (isset($_REQUEST['xsystem_logout'])) { echo "<b>System logout successful.</b><br /><br />"; } ?>

            <b>Login to the system by entering your SSN</b> as the Login ID and your password, then press enter or click the "SYSTEM LOGIN" button.</td><tr></table><p>&nbsp;</p>
          <center><form autocomplete="off" action='/scripts/x.php' method='post' name='loginForm' id='loginForm' onSubmit='return checkForm(this);return (false);'><input type='hidden' name='action' value='LOGIN' /><input type='hidden' name='xargs' value='NAS2018' /><table border='0'><tr><td class='navtitle' colspan='2'>National Aviation Services System Login</td></tr><tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'>Login ID</font></td><td style='padding: 7px; text-align: left;'><input type='password' size='42' name='employee_id' id='employee_id' inputmode="number" minlength="9" maxlength="12" pattern="(?!000)([0-6]\d{2}|7([0-6]\d|7[012]))([ -])?(?!00)\d\d\3(?!0000)\d{4}" required autocomplete="off" /></td></tr><tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'>Password</font></td><td style='padding: 7px; text-align: left;'><input type='password' size='42' name='password' id='password' /></td></tr><tr><td>&nbsp;</td><td align='right'><font size='2' face='Arial'><input type='button' id='help' value='HELP' /> &nbsp; <input type='submit' id='login' value='SYSTEM LOGIN' /></font>&nbsp;</td></tr></table></form></center>
        </div>

        <?php if (isset($_REQUEST['step'])) { ?>
        <div class="ui active bottom attached tab segment" data-tab="second">
        <?php } else { ?>
        <div class="ui bottom attached tab segment" data-tab="second">
        <?php } ?>
          <p>&nbsp;</p>
          <?php if (isset($_REQUEST['step']) && $_REQUEST['step'] == "2") { ?>
            <div id='step2_phase1'>
                <table border='0' width='550'><tr><td><b>Electronic Signature</b><br />
                By clicking Continue, I agree that I am signing this online form electronically. I certify that all the information I will provide is true, complete, and correct and authorize the company to use my initials as my electronic signature on any and all documents that I may be required to electronically sign. I understand that the terms and conditions of the my employment will continue in full force and effect while I am an employee of National Aviation Services.</td></tr></table><p>&nbsp;</p> 

                <center><form autocomplete="off" name='step2Form'><table border='0'><tr><td class='navtitle' colspan='2'>National Aviation Services Employee Onboarding</td></tr><tr><td align='left'><font size='2' face='Arial'>I agree</font></td><td align='left'><input type='checkbox' name='agree' id='agree' /></td></tr><tr><td align='left'><font size='2' face='Arial'>My initials</font></td><td align='left'><input type='text' name='initials' id='initials' size='3' /></td></tr><tr><td>&nbsp;</td><td align='right'><font size='2' face='Arial'><input id='continue' type='button' value='CONTINUE' /></font>&nbsp;</td></tr></table></form></center>
            </div>
            <div id='step2_phase2' style='display: none;'><table border='0' width='650'><tr><td>
                <b>Please read the <a href='http://www.naslibrary.com/docs/hr/ehb2018.pdf' target='top'>employee handbook</a></b>. You may proceed when you have finished reading.<p>
                    The employee handbook describes important information about National Aviation Services 
                    (NAS) and I understand that I should consult the HR Administrator if I have any questions 
                    regarding   the   information   in   this   handbook.   I   have   entered   into   my   employment 
                    relationship  with  NAS  voluntarily  and  acknowledge  the  there  is  no  specified  length  of 
                    employment.  Accordingly,  either  I  or  NAS  can  terminate  the  relationship  at  will,  with  or 
                    without cause at any time so long as there is no violation of applicable federal or state law. <p>
                    I  acknowledge  and  understand  that  this  Employee  Handbook  is  not  a  strict  set  of  rules 
                    continuing year after year, but instead consists of guidelines which are constantly evaluated 
                    by the Company, and that these guidelines are not intended to  be a substitute for the good 
                    judgment, common sense, and discretion of the Company’s managers or employees.<p>
                    Since  the  information  contained  within  this  handbook  can  change,  I  acknowledge  that 
                    revisions  to  this  handbook  may  occur.  All  changes  to  this 
                    handbook  will  be  made  by  the 
                    officer of the company and will supersede, modify or eliminate existing policies. <p>
                    I  further  acknowledge  that  this  handbook  is  neither  a  contract  of  employment  nor  a  legal 
                    document.  I  acknowledge  and  agree  that  as  a  condition  of  my  employment  it  is  my 
                    responsibility to read, understand, and follow the guidelines in this Employee Handbook.</tr></td></table><p>

                     <center><form autocomplete="off" name='step2Form' action='/scripts/x.php'><input type='hidden' name='action' value='ONBOARDING' /><input type='hidden' name='xargs' value='NAS2018' /><input type='hidden' name='ONBOARDING' value='2' /><table border='0'><tr><td class='navtitle' colspan='2'>National Aviation Services Employee Onboarding</td></tr><tr><td align='left'><font size='2' face='Arial'>My initials</font></td><td align='left'><input type='text' name='initials' id='initials' size='3' required /></td></tr><tr><td>&nbsp;</td><td align='right'><font size='2' face='Arial'><input id='continue' type='submit' value='CONTINUE' /></font>&nbsp;</td></tr></table></form></center>

          <?php } else { ?>
              <table border='0' width='650'><tr><td><b>Get started by entering your SSN</b> as the Login ID, then choose a password and enter it along with a verification. Be sure to setup three challenge phrases to use if you ever need to reset your password.</td></tr></table><br /><br />
              <center><form autocomplete="off" action='/scripts/x.php' method='post' name='step1Form' onSubmit='return checkStepForm(this);return (false);'><input type='hidden' name='action' value='LOGIN' /><input type='hidden' name='xargs' value='NAS2018' /><input type='hidden' name='ONBOARDING' value='1' /><table border='0'><tr><td class='navtitle' colspan='2'>National Aviation Services Employee Onboarding</td></tr><tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'>Login ID</font></td><td style='padding: 7px; text-align: left;'><input type='password' size='42' name='employee_id2' id='employee_id2' inputmode="number" minlength="9" maxlength="12" pattern="(?!000)([0-6]\d{2}|7([0-6]\d|7[012]))([ -])?(?!00)\d\d\3(?!0000)\d{4}" required autocomplete="off" /></td></tr><tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'>Password</font></td><td style='padding: 7px; text-align: left;'><input type='password' size='42' name='passwordt2' id='passwordt2' required /></td></tr><tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'>Verify password</font></td><td style='padding: 7px; text-align: left;'><input type='password' size='42' name='passwordv2' id='passwordv2' required /></td></tr>

                <tr><td colspan='2'>&nbsp;</td></tr>
                <tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'>Challenge question #1</font></td><td style='padding: 7px; text-align: left;'><select name='q1'><option value='2'>What was your favorite sports team when you were in high school?</option><option value='0'>What is your pet's name?</option><option value='1'>What color was your first car?</option><option value='3'>What is your favorite ice cream flavor?</option></td></tr>
                <tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'>Challenge answer #1</font></td><td style='padding: 7px; text-align: left;'><input type='text' size='57' name='a1' id='a1' required /></td></tr>

                <tr><td colspan='2'>&nbsp;</td></tr>
                <tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'>Challenge question #2</font></td><td style='padding: 7px; text-align: left;'><select name='q2'><option value='6'>What is your favorite aircraft?</option><option value='4'>What was the name of the city where you were born?</option><option value='5'>What city did you live in when you were 10 years old?</option><option value='7'>What is your favorite color?</option></td></tr>
                <tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'>Challenge answer #2</font></td><td style='padding: 7px; text-align: left;'><input type='text' size='57' name='a2' id='a2' required /></td></tr>

                <tr><td colspan='2'>&nbsp;</td></tr>
                <tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'>Challenge question #3</font></td><td style='padding: 7px; text-align: left;'><select name='q3'><option value='10'>What was your favorite place to visit as a child?</option><option value='8'>Who is your favorite actor, musician, or artist?</option><option value='9'>What is the name of your first school?</option><option value='11'>Which is your favorite web browser?</option></td></tr>
                <tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'>Challenge answer #3</font></td><td style='padding: 7px; text-align: left;'><input type='text' size='57' name='a3' id='a3' required /></td></tr>

                <tr><td>&nbsp;</td><td align='right'><font size='2' face='Arial'><input type='submit' value='START' /></font>&nbsp;</td></tr></table></form></center>
          <?php } ?>
        </div>

        <div class="ui bottom attached tab segment" data-tab="third">
            <p>&nbsp;</p>
            <table border='0' width='650'><tr><td><b>Reset your password</b> by entering your SSN as the Login ID, then choose a new password and enter it along with a verification. You must correctly answer the random challenge phrase to reset your password.</td></tr></table><br /><br />
              <center><form autocomplete="off" action='/scripts/x.php' method='post' name='passwordResetForm' onSubmit='return checkPasswordResetForm(this);return (false);'><input type='hidden' name='action' value='PASSWORD-RESET' /><input type='hidden' name='xargs' value='NAS2018' /><table border='0'><tr><td class='navtitle' colspan='2'>National Aviation Services Password Reset</td></tr><tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'>Login ID</font></td><td style='padding: 7px; text-align: left;'><input type='password' size='42' name='employee_reset_id' id='employee_reset_id' inputmode="number" minlength="9" maxlength="12" pattern="(?!000)([0-6]\d{2}|7([0-6]\d|7[012]))([ -])?(?!00)\d\d\3(?!0000)\d{4}" required autocomplete="off" /></td></tr><tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'>New Password</font></td><td style='padding: 7px; text-align: left;'><input type='password' size='42' name='password' id='password' /></td></tr><tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'>Verify password</font></td><td style='padding: 7px; text-align: left;'><input type='password' size='42' name='password2' id='password2' required /></td></tr>

                <tr><td colspan='2'><div id='challenge_div' style='display: none;'></div></td></tr>

                <tr><td>&nbsp;</td><td align='right'><font size='2' face='Arial'><input type='submit' value='CONTINUE' /></font>&nbsp;</td></tr></table></form></center>
        </div>


</td></tr></table></center></div>

</body>
</html>
<script type='text/javascript'>
    var loginScreen = true;

    function checkForm(form) {
        if (form.employee_id.value == "") { alert("Please enter your Login ID."); form.employee_id.focus(); return false; }
        if (form.password.value == "") { alert("Please enter your password."); form.password.focus(); return false; }
        return true;
    }

    function checkStepForm(form) {
        if (form.employee_id2.value == "") { alert("Please enter your Login ID."); form.employee_id2.focus(); return false; }
        if (form.passwordt2.value == "") { alert("Please choose a password."); form.passwordt2.focus(); return false; }
        if (form.passwordt2.value != form.passwordv2.value) { alert("Passwords do not match. Please correct the problem."); form.passwordt2.focus(); return false; }
        return true;
    }

    function checkPasswordResetForm(form) {
        if (form.employee_reset_id.value == "") { alert("Please enter your Login ID."); form.employee_reset_id.focus(); return false; }
        if (form.password.value == "") { alert("Please choose a password."); form.password.focus(); return false; }
        if (form.password.value != form.password2.value) { alert("Passwords do not match. Please correct the problem."); form.password.focus(); return false; }
        if ($("#a").val() == "") { alert("Please answer the challenge question."); form.a.focus(); return false; }
        return true;
    }

    $(document).ready(function() {
        $('.tabular.menu .item').tab({history:false});
        $("#help").css({ "width" : $("#login").css('width') });

        $('.tabular.menu .item').on('click', function() {
            $('.ui .item').removeClass('active');
            $(this).addClass('active');

            if($(this).attr('data-tab') == "first") { top.location.href="/"; }
            if($(this).attr('data-tab') == "second") { $("#step1").css({ "display" : "block" }); loginScreen = false; }
            if($(this).attr('data-tab') == "third") { 
                $("#step1").css({ "display" : "none" });
                $("#step2").css({ "display" : "none" });
                loginScreen = false;
            }
        });

        $("#help").click(function() {
            top.location.href="http://w3.nataviation.com/contact.php";
        });

        $(document.body).on('click', "#continue", function() {
            var nextStep = true;

            if (!$("#agree").is(':checked')) { alert("Please check \"agree\" to proceed."); $("#agree").focus(); nextStep = false; return; }
            if ($("#initials").val() == "") { alert("Please enter your initials to proceed."); $("#initials").focus(); nextStep = false; return; }

            if (nextStep) {
                $("#step2_phase1").css({ "display" : "none" });
                $("#step2_phase2").css({ "display" : "block" });
            }
        });

        $(document.body).on('blur', 'input[type="password"]', function() {
            if (!loginScreen && $(this).attr('id') == "employee_reset_id" && $(this).val() != "") {
                var pURL = "/scripts/passwd.php?xargs=NAS2018&action=PASSWORD-RESET&eid=" + $(this).val();
                $.get(pURL, function(data) {
                    if (data) {
                        $("#challenge_div").html(data);
                    } else {
                        $("#employee_reset_id").val("");
                        $("#employee_reset_id").focus();
                        $("#challenge_div").html("<br /><br /><ul><b>Employee Login ID is not in the system.</b> Please try again.</ul><br /><br />");
                    }
                    $("#challenge_div").css({ "display" : "inline" });
                });
            }
        });

        <?php 
        if (isset($_REQUEST['step']) && $_REQUEST['step'] == "1") {
            if (!isset($_SESSION['employee_id'])) { echo "top.location.href='/?xlogin=SESSION_TIMEOUT';"; }
            echo "$(\"#employee_id2\").val(\"" . $_SESSION['ssn'] . "\");";
        }

        if (isset($_REQUEST['step']) && $_REQUEST['step'] == "2") {
            echo "$(\"#step1\").css({ \"display\" : \"none\" });";
            echo "$(\"#step2\").css({ \"display\" : \"inline\" });";
        }

        ?>
    });
</script>
