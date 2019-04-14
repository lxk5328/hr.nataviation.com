<?php $loginScreen = false; ?>
<?php require_once("scripts/e.php"); ?>
<?php sessionCheck(); ?>
<?php require_once("scripts/top.php"); ?>


<div style='display: inline;'><a href='/default.php'><img alt='Return to start page' title='Return to start page' border='0' width='60' src='/images/internal_reload.gif' /></a> &nbsp;&nbsp; <font style="color:#3a90c9; font:bold 30px/1.2em Arial, Helvetica, sans-serif; text-transform:uppercase;">

<?php
if ($_REQUEST['xapp'] == "CUSTOMERS") { ?>
	<?php securityCheck("view_trinet", false); ?>
	NetSuite Customers List</font></div><br />
	<center><table id='sh_table_id' class="ui celled table" style="width:100%">
	<thead><tr><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Customer ID</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>NetSuite ID</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Customer Name</font></th></tr></thead>
	<tbody><?php loadCustomersList(); ?></tbody></table></center>

<?php 
} else if ($_REQUEST['xapp'] == "ITEMS") { ?>
	<?php securityCheck("view_trinet", false); ?>
	NetSuite Items List</font></div><br />
	<center><table id='sh_table_id' class="ui celled table" style="width:100%">
	<thead><tr><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Item ID</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>NetSuite ID</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Item Name</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Price</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Location</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Effective Date</font></th></tr></thead>
	<tbody><?php loadItemsList(); ?></tbody></table></center>


<?php	
} else if ($_REQUEST['xapp'] == "LOCATIONS") { ?>
	<?php securityCheck("view_trinet", false); ?>
	NetSuite Locations List</font></div><br />
	<center><table id='sh_table_id' class="ui celled table" style="width:100%">
	<thead><tr><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Location ID</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>NetSuite ID</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Location Name</font></th></tr></thead>
	<tbody><?php loadLocationsList(); ?></tbody></table></center>

<?php	
} else if ($_REQUEST['xapp'] == "AIRCRAFT") { ?>
	<?php securityCheck("view_aircraft", false); ?>
	Aircraft Types</font></div><div id='aircraft_types_buttons_div' style='display: inline;'>&nbsp;&nbsp;&nbsp; <input id='at_add' type='button' value='ADD AIRCRAFT TYPES' style="height:30px;width:150px;display:inline;margin:0 auto;" /></div>
	<center>
	<div id='at_table_div'><table id='sh_table_id' class="ui celled table" style="width:100%">
	<thead><tr><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Aircraft ID</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Aircraft Type</font></th></tr></thead>
	<tbody><?php loadAircraftTypes(); ?></tbody></table></div></center>
	<p><div id='requirements_div' style='display:none;'></div>

		<div id='at_add_div' style='display:none;'>
			<center><form action='/scripts/x.php?xargs=NAS2018&action=AIRCRAFT-TYPE-CREATE' method='post' name='aircraft_type_form'>
			<table border='0'><tr><td class='navtitle' colspan='2'>Aircraft Type</td></tr>
			<tr><td style='padding: 7px; text-align: left;' width='100'><b>Aircraft Type:</b></td><td style='padding: 7px; text-align: left;'><input id='aircraft_type' name='aircraft_type' type='text' size='80' required="required" /></td></tr>
			<tr><td colspan='2' align='right'><input id='rules_cancel' type='button' value='CANCEL' /> &nbsp; <input id='at_submit' type='submit' value='SUBMIT' /></td></tr>
		</table></form></center>
		</div></p>
<?php	
} else if ($_REQUEST['xapp'] == "SERVICES") { ?>
	<?php securityCheck("view_services", false); ?>
	Services List</font></div><br />
	<center><table id='sh_table_id' class="ui celled table" style="width:100%">
	<thead><tr><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Service ID</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Service</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Description</font></th></tr></thead>
	<tbody><?php loadServices(); ?></tbody></table></center>

<?php	
} else if ($_REQUEST['xapp'] == "CENSUS") { ?>
	<?php $dtScroll = true; ?>
	<?php $censusDisplay = true; ?>
	<?php $locationsDisplay = false; ?>
	<?php securityCheck("view_census", false); ?>
	<?php loadCensusLimitDates(); ?>
	
	TriNet Census List</font></div><div style='display: inline;'>&nbsp;&nbsp;&nbsp;<select style="height:30px;width:150px;display:inline;margin:0 auto;" name='view_delta' id='view_delta'><option value='0'>View new records</option><option value='1'>View modified records</option></select> <input type='text' size='10' name='view_delta_date' id='view_delta_date' /> &nbsp; <input id='census_submit' type='button' value='SUBMIT' style="height:30px;width:150px;display:inline;margin:0 auto;" /> <input id='census_cancel' type='button' value='RESET' style="height:30px;width:150px;display:inline;margin:0 auto;" /></div><div style='display: inline; border: solid; border-color: #000000; padding: 5px;'>&nbsp;&nbsp;&nbsp;CURRENT VIEW: 

		<?php if (isset($_REQUEST['view_delta'])) {
			if ($_REQUEST['view_delta'] == "0") {
				echo "new records added on ";
			} else {
				echo "records modified on ";
			}
			echo $_REQUEST['view_delta_date'];
		} else {
			echo "active employees";
		}
		?>

	</div><br />
	<center><div id='dt_display_div'><table id='sh_table_id' class="ui celled table" style="width:100%">
	<thead><tr><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Census ID</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Employee ID</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>SSN</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Employee Name</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Business Title</th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Location Description</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>TriNet Hire Date</font></th></tr></thead>
	<tbody><?php loadCensusList(); ?></tbody></table></div></center>
	<p><div id='profile_div' style='display:none;'></div></p>


<?php	
} else if ($_REQUEST['xapp'] == "SYSTEM-RULES") { ?>
	<?php $dtScroll = true; ?>
	<?php $censusDisplay = false; ?>
	<?php $locationsDisplay = false; ?>
	<?php $rulesDisplay = true; ?>
	<?php securityCheck("view_rules_engine", false); ?>
	System Rules Engine</font></div><div id='rules_buttons_div' style='display: inline;'>&nbsp;&nbsp;&nbsp; <input id='rule_add' type='button' value='ADD RULE' style="height:30px;width:150px;display:inline;margin:0 auto;" /></div>
	<center><div id='dt_display_div'><table id='sh_table_id' class="ui celled table" style="width:100%;">
	<thead><tr><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Rule ID</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Employee</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Rule Name</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Rule Description</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Created Date</font></th></tr></thead>
	<tbody><?php loadRulesList(); ?></tbody></table></div></center>
	<p><div id='requirements_div' style='display:none;'></div>

		<div id='requirements_add_div' style='display:none;'>
			<center><form action='/scripts/x.php?xargs=NAS2018&action=RULES-CREATE' method='post' name='rules_engine_form'><table border='0'><tr><td class='navtitle' colspan='2'>National Aviation Services Rules Engine</td></tr>

			<tr><td style='padding: 7px; text-align: left;' width='65'><b>Name:</b></td><td style='padding: 7px; text-align: left;'><input id='rule_name' name='rule_name' type='text' size='80' required="required" /></td></tr>
			<tr><td  colspan='2' style='padding: 7px; text-align: left;'><b>Description:</b><br /><textarea name='rule_description' rows='4' cols='100' required="required"></textarea></td></tr>
			<tr><td colspan='2' align='right'><input id='rules_cancel' type='button' value='CANCEL' /> &nbsp; <input id='rules_submit' type='submit' value='SUBMIT' /></td></tr>
		</table></form></center>




		</div></p>

<?php	
} else if ($_REQUEST['xapp'] == "ISSUES") { ?>
	<?php $dtScroll = true; ?>
	<?php $issuesDisplay = true; ?>
	<?php securityCheck("view_issue_tracker", false); ?>
	Issues Tracker</font></div><div id='issues_buttons_div' style='display: inline;'>&nbsp;&nbsp;&nbsp; <input id='issue_add' type='button' value='ADD ISSUE' style="height:30px;width:150px;display:inline;margin:0 auto;" /></div>
	<center><div id='dt_display_div'><table id='sh_table_id' class="ui celled table" style="width:100%;">
	<thead><tr><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Issue ID</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Employee</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Issue Description</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Created Date</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Closed</font></th></tr></thead>
	<tbody><?php loadIssuesList(); ?></tbody></table></div></center>
	<p><div id='issues_div' style='display:none;'></div>

		<div id='issues_add_div' style='display:none;'>
			<center><form action='/scripts/x.php?xargs=NAS2018&action=ISSUES-CREATE' method='post' name='issues_form'><table border='0'><tr><td class='navtitle' colspan='2'>National Aviation Services Issues Tracker</td></tr>

			<tr><td  colspan='2' style='padding: 7px; text-align: left;'><b>Description:</b><br /><textarea name='description' rows='4' cols='100' required="required"></textarea></td></tr>
			<tr><td colspan='2' align='right'><input id='issues_cancel' type='button' value='CANCEL' /> &nbsp; <input id='issues_submit' type='submit' value='SUBMIT' /></td></tr>
		</table></form></center>


		</div></p>

<?php } ?>

<p>&nbsp;</p><br />


<?php require_once("scripts/bottom.php"); ?>
