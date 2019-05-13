<?php

//# DEBUG MODE DEFINITION
//##############################################
//##############################################
//##############################################

$debugMode = false;
if (isset($_REQUEST['debug'])) { $debugMode = true; }
if ($debugMode) { $_SESSION['debugMode'] = true; }
if (isset($_SESSION['debugMode'])) { $debugMode = true; }

if (isset($_REQUEST['info'])) { $debugMode = false; }
if (!$debugMode) { $_SESSION['debugMode'] = false; }

$securityDebugMode = false;
if (isset($_REQUEST['security'])) { $securityDebugMode = true; }



//# GLOBAL SQL EXECUTION
//##############################################
//##############################################

function execSQL($sql, $r = false) {
  global $dbconn;
  global $debugMode;

  $rs[] = null;
  if ($debugMode) { echo $sql . "<br>"; }
  $result = mysqli_query($dbconn, $sql);
  if ($r) { return mysqli_insert_id($dbconn); }
  while($row = mysqli_fetch_array( $result )) { $rs[] = $row; }
  return $rs;
}



//# SARA BARNES
//##############################################
//##############################################
$operationsEmployeeID = "8975b958-d6df-437e-a251-a6cf00af9e85";



//# STATION ROLLOUT
//##############################################
//##############################################
$stationRolloutArray = array();
array_push($stationRolloutArray, "MCI");


//# STATION ATTENDANCE TRACKING
//##############################################
//##############################################
$stationAttendanceArray = array();
array_push($stationAttendanceArray, "MCI");


//# DAILY HOURLY EMPLOYEE LIMIT
$dailyHourlyLimit = 8.5;
$weeklyHourlyLimit = 42.5;


//# SHIFT REPORT LEADS
$shiftReportLeads = array();
array_push($shiftReportLeads, "5");
array_push($shiftReportLeads, "10");
array_push($shiftReportLeads, "12");
array_push($shiftReportLeads, "18");
array_push($shiftReportLeads, "28");
array_push($shiftReportLeads, "44");
array_push($shiftReportLeads, "74");
array_push($shiftReportLeads, "77");
array_push($shiftReportLeads, "78");
array_push($shiftReportLeads, "92");

array_push($shiftReportLeads, "32"); //# Junior Accounting Clerk
array_push($shiftReportLeads, "67"); //# Accounting Assistant
array_push($shiftReportLeads, "68"); //# System Analyst
array_push($shiftReportLeads, "79"); //# Senior Accountant





if (isset($loginScreen) && $loginScreen) {
  session_start();
  $_SESSION = array();
  session_destroy();
}

ob_start();
session_cache_expire(0);
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$staffAvailableArray = array();
$locationsArray = array();
$scheduleArray = array();
$staffArray = array();

$locationArray = array();
$positionArray = array();
$employeeArray = array();

$securityLocationArray = array();
$securityPositionArray = array();
$securityEmployeeArray = array();

$mArray = array();
$uArray = array();

$warningsTargetDir = "/NationalAviationServices/files/warnings/";
$targetDir = "/NationalAviationServices/integration/incoming/NetSuite/";
$archiveDir = "/NationalAviationServices/integration/archive/";

if (strtolower(substr(Date("l"),0,3)) == "sat") {
  $scheduleBaseDate = date('Y-m-d');
  $scheduleDate = date('Y-m-d');
} else {
  $scheduleBaseDate = date('Y-m-d', strtotime("last Saturday"));
  $scheduleDate = date('Y-m-d', strtotime("last Saturday"));
}

$attendanceDate = date('Y-m-d');
$currentDate = date('Y-m-d');

$updateAttendanceMode = false;
$shiftReportAvailable = false;
$scheduleOpenWorkFlow = false;
$projectOptionDefault = null;
$scheduleCurrentWeek = false;
$scheduleWeekDisplay = null;
$scheduleCreateMode = false;
$airportCodeDefault = null;
$scheduleEditMode = false;
$shiftBudgetMode = false;
$scheduleExists = false;
$shiftClockTime = null;
$shiftTotalTime = null;

$shiftDisplayDate = null;
$baseServiceRow = null;
$maxLimitDate = null;
$redirectURL = null;
$authorized = false;
$serviceRow = null;
$domainRoot = "/";
$limitDate = null;
$dtScroll = false;

$employmentAppDisplay = false;
$terminationDisplay = false;
$editGlobalSchedule = false;
$viewGlobalSchedule = false;
$permissionsDisplay = false;
$attendanceDisplay = false;
$locationsDisplay = false;
$positionsDisplay = false;
$overrideDisplay = false;
$employeeDisplay = false;
$messagesDisplay = false;
$scheduleDisplay = false;
$securityDisplay = false;
$reportsDisplay = false;
$profileDisplay = false;
$issuesDisplay = false;
$censusDisplay = false;
$rulesDisplay = false;

$onboardingID = "92";
$color = "#cecece";
$director = false;
$location = null;
$manager = false;
$response = null;
$dbconn = null;

function formatQuotes($d) {
  return str_replace("'", "''", $d);
}

function formatSSN($s, $strip = NULL) {
  global $debugMode;

  if ($debugMode) { echo "<br />formatSSN()<br />"; }
  if ($strip) { return str_replace("-", "", $s); }
  if (strPos($s, "-")) { return $s; }
  return substr($s, 0, 3) . "-" . substr($s, 3, 2) . "-" . substr($s, 5);
}

function checkNull($v) {
  if ($v == "") { return "NULL"; }
  return $v;
}

function checkShiftReport() {
  global $operationsEmployeeID;
  global $shiftReportLeads;
  global $debugMode;
  global $manager;

  if ($debugMode) { echo "<br />checkShiftReport(): " . $manager . "<br />"; }
  if ($operationsEmployeeID == $_SESSION['user']->getEmployeeID()) { return true; }

  if ($manager || in_array($_SESSION['user']->getPositionID(), $shiftReportLeads)) {
    $sql = "SELECT shift_report_id FROM shift_report WHERE location  = '" . $_SESSION['user']->getLocation() . "' AND shift_date = (CURDATE() - 1) AND reported = 1";
    $rs = execSQL($sql);
    if (sizeof($rs) > 1) { return false; }
    return true;
  }
  return false;
}

function loadFile($fn) {
  global $targetDir;
  global $debugMode;

  if ($debugMode) { echo "<br />loadFile()<br />"; }
  $fileName = basename($_FILES[$fn]["name"]);
  $filePath = stripBrackets(getGUID()) . "_" . $fileName;
  $targetFile = $targetDir . $filePath;

  move_uploaded_file($_FILES[$fn]["tmp_name"], $targetFile);
  return $filePath;
}

function loadEmergencyContacts() {
  global $debugMode;

  $contactName = null;
  $contactPhone = null;
  $dtLastModified = null;
  $altContactPhone = null;
  $emergencyContactID = null;
  $contactRelationship = null;

  if ($debugMode) { echo "<br />loadEmergencyContacts()<br />"; }
  $sql = "SELECT * FROM EmergencyContacts WHERE EmployeeID = '" . $_SESSION['user']->getEmployeeID() . "'";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r[0] == "") { continue; }
    $emergencyContactID = $r['EmergencyContactID'];
    $contactName = $r['ContactName'];
    $contactPhone = $r['Phone'];
    $altContactPhone = $r['AltPhone'];
    $contactRelationship = $r['Relationship'];
    $dtLastModified = $r['DTLastModified'];
  }

  echo "<center><form action='scripts/x.php' method='post' name='contactsForm' onSubmit='return checkContactsForm(this);return (false);'><input type='hidden' name='xargs' value='NAS2018' /><input type='hidden' name='action' value='CONTACTS' /><table border='0'><tr><td class='navtitle' colspan='2'>National Aviation Services Emergency Contact Form</td></tr><tr><td style='padding: 7px; text-align: left;' width='120'><font size='2' face='Arial'><b>Contact name:</b></font></td><td style='padding: 7px; text-align: left;'><input type='text' size='60' name='ContactName' value='" . $contactName . "' /></td></tr><tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'><b>Relationship:</b></font></td><td style='padding: 7px; text-align: left;'><input type='text' size='32' name='Relationship' value='" . $contactRelationship . "' /></td></tr><tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'><b>Phone:</b></font></td><td style='padding: 7px; text-align: left;'><input type='text' size='32' name='Phone' value='" . $contactPhone . "'  /></td></<tr><tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'><b>Alternate Phone:</b></font></td><td colspan='2' style='padding: 7px; text-align: left;'><input type='text' size='32' name='AltPhone' value='" . $altContactPhone . "' /></td></tr>";

  if (!is_null($dtLastModified)) {
    echo "<tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'><b>Date Modified:</b></font></td><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'>" . $dtLastModified . "</font></td></tr>";
  }

  echo "<tr><td colspan='2' align='right'><font size='2' face='Arial'><input type='button' id='contacts_cancel' value='CANCEL' /> &nbsp; <input type='submit' value='SUBMIT' /></font></td></tr></table><input type='hidden' name='EmergencyContactID' value='" . $emergencyContactID . "' /></form></center>";

}

function loadTerminationForm() {

  echo "<center><form action='scripts/x.php' method='post' name='terminateForm'><input type='hidden' name='xargs' value='NAS2018' /><input type='hidden' name='action' value='TERMINATE' /><table border='0'><tr><td class='navtitle' colspan='2'>National Aviation Services Employee Termination Form</td></tr><tr><td style='padding: 7px; text-align: left;' width='180'><font size='2' face='Arial'><b>Employee:</b></font></td><td style='padding: 7px; text-align: left;'><select name='employee_id'>";

  $sql = "SELECT e.EmployeeID, UPPER(c.first_name) AS first_name, UPPER(c.last_name) AS last_name FROM Employees e, census c WHERE LPAD(c.ssn_nid,9,0) = REPLACE(e.SSN,'-','') AND c.location_description LIKE '" . $_SESSION['user']->getLocation() . "%' AND c.status = 'Active' AND c.business_title != 'Trusted Advisor' AND ssn_nid != '" . $_SESSION['user']->getSSN() . "'  ORDER BY c.last_name";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r[0] =="") { continue; }
    echo "<option value='" . $r['EmployeeID'] . "'>" . $r['first_name'] . " " . $r['last_name'] . "</option>";
  }

  echo "</select></td></tr><tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'><b>Termination reason:</b></font></td><td style='padding: 7px; text-align: left;'><select id='reason' name='reason'><option value='voluntary'>Voluntary</option><option value='involuntary'>Involuntary</option></select></td></tr>";


  echo "<tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'><b>Termination Type:</b></font></td><td style='padding: 7px; text-align: left;'><div id='type_id_div'><select id='type_id' name='type_id'>";

  $sql = "SELECT TerminationTypeID, TerminationType, Voluntary FROM TerminationTypes ORDER BY TerminationType";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r[0] == "") { continue; }
    if ($r['Voluntary'] == 0) { continue; }
    echo "<option value='" . $r['TerminationTypeID'] . "'>" . $r['TerminationType'] . "</option>";
  }

  echo "</select></div></td></tr><tr id='termination_description_tr'><td style='padding: 7px; text-align: left;' colspan='2'><font size='2' face='Arial'><b>Termination description:</b><br /><textarea rows='5' cols='80' name='description' required></textarea></td></tr>";


  echo "<tr><td colspan='2'><p>&nbsp;</p></td></tr><tr><td colspan='2' align='right'><font size='2' face='Arial'><input type='button' id='action_cancel' value='CANCEL' style='height:30px;width:150px;display:inline;margin:0 auto;' /> &nbsp; <input type='submit' value='SUBMIT' style='height:30px;width:150px;display:inline;margin:0 auto;' /></font></td></tr></table></form></center>";
}

function loadWarningForm() {
  echo "<center><form enctype='multipart/form-data' action='scripts/x.php' method='post' name='warningForm'><input type='hidden' name='xargs' value='NAS2018' /><input type='hidden' name='action' value='WARNING' /><table border='0'><tr><td class='navtitle' colspan='2'>National Aviation Services Employee Warning Form</td></tr><tr><td style='padding: 7px; text-align: left;' width='180'><font size='2' face='Arial'><b>Employee:</b></font></td><td style='padding: 7px; text-align: left;'><select name='employee_id'>";

  $sql = "SELECT e.EmployeeID, UPPER(c.first_name) AS first_name, UPPER(c.last_name) AS last_name FROM Employees e, census c WHERE LPAD(c.ssn_nid,9,0) = REPLACE(e.SSN,'-','') AND c.location_description LIKE '" . $_SESSION['user']->getLocation() . "%' AND c.status = 'Active' AND c.business_title != 'Trusted Advisor' AND ssn_nid != '" . $_SESSION['user']->getSSN() . "'  ORDER BY c.last_name";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r[0] =="") { continue; }
    echo "<option value='" . $r['EmployeeID'] . "'>" . $r['first_name'] . " " . $r['last_name'] . "</option>";
  }

  echo "</select></td></tr><tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'><b>Warning Style:</b></font></td><td style='padding: 7px; text-align: left;'><select id='WarningStyleID' name='WarningStyleID'>";

  $sql = "SELECT WarningStyleID, WarningStyle FROM WarningStyles";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r[0] == "") { continue; }
    echo "<option value='" . $r['WarningStyleID'] . "'>" . $r['WarningStyle'] . "</option>";
  }

 echo "</select></td></tr><tr><tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'><b>Primary Warning Type:</b></font></td><td style='padding: 7px; text-align: left;'><select id='WarningType' name='WarningTypePrimary'>";

  $sql = "SELECT WarningTypeID, WarningType FROM WarningTypes WHERE ActiveFlag = 1";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r[0] == "") { continue; }
    echo "<option value='" . $r['WarningTypeID'] . "'>" . $r['WarningType'] . "</option>";
  }

  echo "</select></td></tr><tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'><b>Secondary Warning Type:</b></font></td><td style='padding: 7px; text-align: left;'><select id='WarningType' name='WarningTypeSecondary'><option value=''></option>";


  foreach ($rs as $r) {
    if ($r[0] == "") { continue; }
    echo "<option value='" . $r['WarningTypeID'] . "'>" . $r['WarningType'] . "</option>";
  }

  echo "</select></td></tr><tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'><b>Tertiary Warning Type:</b></font></td><td style='padding: 7px; text-align: left;'><select id='WarningType' name='WarningTypeTertiary'><option value=''></option>";


  foreach ($rs as $r) {
    if ($r[0] == "") { continue; }
    echo "<option value='" . $r['WarningTypeID'] . "'>" . $r['WarningType'] . "</option>";
  }

  echo "</select></td></tr><tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'><b>Attachment:</b></font></td><td style='padding: 7px; text-align: left;'><input type='file' name='Attachment' size='40' /></td></tr>";

  echo "<tr><td style='padding: 7px; text-align: left;' colspan='2'><font size='2' face='Arial'><b>Warning notes:</b><br /><textarea rows='5' cols='150' name='notes' required></textarea></td></tr>";

  echo "<tr><td colspan='2'><p>&nbsp;</p></td></tr><tr><td colspan='2' align='right'><font size='2' face='Arial'><input type='button' id='action_cancel' value='CANCEL' style='height:30px;width:150px;display:inline;margin:0 auto;' /> &nbsp; <input type='submit' value='SUBMIT' style='height:30px;width:150px;display:inline;margin:0 auto;' /></font></td></tr></table></form></center>";
}

function loadTimeRequest($xapp, $cout) {
  global $debugMode;

  $selected = null;
  if ($debugMode) { echo "<br />loadTimeRequest()<br />"; }

  echo "<center><form action='scripts/x.php' method='post' name='timeForm' onSubmit='return checkTimeForm(this);return (false);'><input type='hidden' name='xargs' value='NAS2018' /><input type='hidden' name='action' value='" . $xapp . "' /><table border='0'><tr><td class='navtitle' colspan='2'>National Aviation Services " . $cout . " Form</td></tr><tr><td style='padding: 7px; text-align: left;' width='100'><font size='2' face='Arial'><b>Request type:</b></font></td><td style='padding: 7px; text-align: left;'><select name='request_type'>";

  if ($_REQUEST['t'] == "0") { $selected = " selected"; } else { $selected = null; }
  //echo "<option value='0'" . $selected . ">Vacation</option>";

  if ($_REQUEST['t'] == "1") { $selected = " selected"; } else { $selected = null; }
  echo "<option value='1'" . $selected . ">Birthday</option>";

  if ($_REQUEST['t'] == "2") { $selected = " selected"; } else { $selected = null; }
  echo "<option value='2'" . $selected . ">Sick</option>";

  if ($_REQUEST['t'] == "3") { $selected = " selected"; } else { $selected = null; }
  echo "<option value='3'" . $selected . ">Leave of Absence</option></select></td></tr><tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'><b>Start date:</b></font></td><td style='padding: 7px; text-align: left;'><input type='text' size='15' id='start_date' name='start_date' /></td></tr><tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'><b>End date:</b></font></td><td style='padding: 7px; text-align: left;'><input type='text' size='15' id='end_date' name='end_date' /></td></tr><tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'><b>Hours:</b></font></td><td style='padding: 7px; text-align: left;'><input type='text' size='5' name='hours' /></td></tr><tr><td colspan='2' style='padding: 7px; text-align: left;'><b>Notes:</b><br /><textarea name='notes' rows='2' cols='100'></textarea></td></tr><tr><td colspan='2' align='right'><font size='2' face='Arial'><input type='button' id='time_off_cancel' value='CANCEL' style='height:30px;width:150px;display:inline;margin:0 auto;' /> &nbsp; <input type='submit' value='SUBMIT' style='height:30px;width:150px;display:inline;margin:0 auto;' /></font></td></tr></table></form></center>";
}

function loadCensusLimitDates() {
  global $limitDate;
  global $debugMode;
  global $maxLimitDate;

  if ($debugMode) { echo "<br />loadCensusLimitDates()<br />"; }
  $sql = "SELECT Date(max(effective_date)) AS limit_date, DATE_ADD(Date(max(effective_date)), INTERVAL 1 DAY) AS max_date FROM census_sequence;";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r[0] == "") { continue; }
    $limitDate = $r['limit_date'];
    $maxLimitDate = $r['max_date'];
  }
}

function aescrypt($s) {
  return $s;
}

function loadPositions() {
  global $positionArray;
  global $debugMode;

  if ($debugMode) { echo "<br />loadPositions()<br />"; }
  $sql = "SELECT business_title FROM position_mapping WHERE position_id = " . $_REQUEST['pid'];
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r[0] == "") { continue; }
    array_push($positionArray, $r[0]);
  }
}

function loadPermissionsRow($t) {
  global $locationArray;
  global $employeeArray;
  global $positionArray;
  global $debugMode;

  if ($debugMode) { echo "<br />loadPermissionsRow()<br />"; }
  if ($t == "location") {
    $sql = "SELECT l.LocationID, l.LocationCode FROM Locations l, location_permissions lp WHERE l.ActiveFlag = 1 AND lp.active = 1 AND l.LocationID = lp.location_id AND lp.permissions_id = " . $_REQUEST['pid'];
  } else if ($t == "position") {
    $sql = "SELECT p.PositionID, p.Description FROM Positions p, position_permissions pp WHERE pp.active = 1 AND p.PositionID = pp.position_id AND pp.permissions_id = " . $_REQUEST['pid'];
  } else if ($t == "employee") {
    $sql = "SELECT e.EmployeeID, CONCAT(e.FirstName, \" \", e.LastName) AS EmployeeName FROM Employees e, employee_permissions ep WHERE ep.active = 1 AND e.EmployeeID = ep.employee_id AND ep.permissions_id = " . $_REQUEST['pid'];
  }
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r[0] == "") { continue; }
    if ($t == "location") { array_push($locationArray, $r[0] . "|" . $r[1]); }
    if ($t == "employee") { array_push($employeeArray, $r[0] . "|" . $r[1]); }
    if ($t == "position") { array_push($positionArray, $r[0] . "|" . $r[1]); }
  }
}

function loadSecurityLog() {
  global $debugMode;

  if ($debugMode) { echo "<br />loadSecurityLog()<br />"; }
  $sql = "SELECT s.security_log_id, s.ip_address, s.action, s.result, s.action_ts, (SELECT CONCAT(e.FirstName, \" \", e.LastName) AS employee FROM Employees e WHERE s.employee_id = e.EmployeeID) AS employee FROM security_log s";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r['security_log_id'] == "") { continue; }
    echo "<tr><td>" . $r['security_log_id'] . "</td><td>" . $r['employee'] . "</td><td>" . $r['ip_address'] . "</td><td>" . $r['action'] . "</td><td>" . $r['result'] . "</td><td>" . $r['action_ts'] . "</td></tr>";
  }
}

function suspendEmployee($e) {
  $sql = "INSERT INTO suspensions (employee_id, suspension_date) VALUES ('" . $e . "', NOW())";
    execSQL($sql, true);

    $sql = "UPDATE Users SET ActiveFlag = 0 WHERE EmployeeID = '" . $e . "'";
    execSQL($sql);
}

function securityCheck($s, $link, $override = NULL, $employeeID = NULL) {
  global $securityDebugMode;
  global $debugMode;

  $id = null;
  $v = false;

  if ($debugMode) { echo "<br />securityCheck(" . $s . ", " . $link . ")<br />"; }
  if (is_null($employeeID)) {
    if ($_SESSION['user'] != null) { $employeeID =  $_SESSION['user']->getEmployeeID(); }
  }

  $sql = "select permissions_id FROM permissions WHERE permission = '" . $s . "'";
  $rs = execSQL($sql);

  foreach ($rs as $r) { if ($r[0] == "") { continue; } $id = $r[0]; }
  if ((isset($_SESSION['position_security'])) && (in_array($id, $_SESSION['position_security']))) { $v = true; }
  if ((isset($_SESSION['location_security'])) && (in_array($id, $_SESSION['location_security']))) { $v = true; }
  if ((isset($_SESSION['employee_security'])) && (in_array($id, $_SESSION['employee_security']))) { $v = true; }

  if ($securityDebugMode) {
    var_dump($_SESSION['position_security']);
    echo "<br />";
    var_dump($_SESSION['employee_security']);
    echo "<br />";
    var_dump($_SESSION['location_security']);
    echo "<br />";
    echo "link: " . $link . "<br />";
    echo "v: " . $v . "<br />";
  }

  if ($link) { return $v; }
  if ($v) { return $v; } else {
    if (is_null($override)) {
      $sql = "INSERT INTO security_log (employee_id, ip_address, action, result) VALUES ('" . $employeeID . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $s . "', 'SYSTEM LOGOUT')";
      execSQL($sql, true);
      header("Location: /index.php");
    }
  }
}

function convertCheckBox($v) {
  if ($v == "on") { return "1"; } else { return "0"; }
}

function loadPermissions() {
  loadPermissionsRow("location");
  loadPermissionsRow("position");
  loadPermissionsRow("employee");
}

function calculateShiftTime($t1, $t2) {
  $t1 = strtotime("1/1/1980 $t1");
  $t2 = strtotime("1/1/1980 $t2");

  if ($t2 < $t1) { $t2 = $t2 + 86400; }
  return ($t2 - $t1) / 3600;
}

function modifyPositions($r) {
  $inputArray = explode(",", $r);

  if (sizeof($inputArray) > 0) {
    $sql = "DELETE FROM position_mapping WHERE position_id = " . $_REQUEST['pid'];
    execSQL($sql, true);

    foreach ($inputArray as $p) {
      if ($p == "") { continue; }
      $sql = "INSERT INTO position_mapping (position_id, business_title) VALUES (" . $_REQUEST['pid'] . ", '" . $p . "')";
      execSQL($sql, true);
    }
  }
}

function modifyPermissions($r, $t) {
  $inputArray = explode(",", $r);

  if (sizeof($inputArray) > 0) {
    $sql = "UPDATE " . $t . "_permissions SET active = 0 WHERE permissions_id = " . $_REQUEST['pid'];
    execSQL($sql, true);

    foreach ($inputArray as $p) {
      if ($p == "") { continue; }
      $sql = "INSERT INTO " . $t . "_permissions (permissions_id, " . $t . "_id, active) VALUES (" . $_REQUEST['pid'] . ", '" . $p . "', 1)";
      execSQL($sql, true);
    }
  }
}

function loadSecurityMapping() {
  global $securityPositionArray;
  global $securityLocationArray;
  global $securityEmployeeArray;

  $sql = "SELECT permissions_id FROM position_permissions WHERE active = 1 AND position_id = " . $_SESSION['user']->getPositionID();
  $rs = execSQL($sql);
  foreach ($rs as $r) { if ($r[0] == "") { continue; } array_push($securityPositionArray, $r[0]); }

  $sql = "SELECT permissions_id FROM location_permissions WHERE active = 1 AND location_id = " . $_SESSION['user']->getLocationID();
  $rs = execSQL($sql);
  foreach ($rs as $r) { if ($r[0] == "") { continue; } array_push($securityLocationArray, $r[0]); }

  $sql = "SELECT permissions_id FROM employee_permissions WHERE active = 1 AND employee_id = '" . $_SESSION['user']->getEmployeeID() . "'";
  $rs = execSQL($sql);
  foreach ($rs as $r) { if ($r[0] == "") { continue; } array_push($securityEmployeeArray, $r[0]); }

  $_SESSION['position_security'] = $securityPositionArray;
  $_SESSION['location_security'] = $securityLocationArray;
  $_SESSION['employee_security'] = $securityEmployeeArray;
}

function printScheduleWidget() {
  echo "<div class='button-group' style='display:inline;'><select id='sHours' name='sHours' style='height:30px;width:150px;display:inline;margin:0 auto;'>";

  for ($x = 13.5; $x > 4; $x--) {
    echo "<option value='" . $x . "'>" . $x . " hour shifts</option>";
    echo "<option value='" . ($x - .5) . "'>" . ($x - .5) . " hour shifts</option>";
  }

  echo "</select>  &nbsp;&nbsp; <button type='button' class='btn btn-default btn-sm dropdown-toggle' data-toggle='dropdown' style='background-color: #efefef'><span class='glyphicon glyphicon-calendar'></span> <span class='caret'></span></button><ul class='dropdown-menu'><li>&nbsp;&nbsp; <input type='checkbox' id='sSat' />&nbsp;Saturday</li><li>&nbsp;&nbsp; <input type='checkbox' id='sSun' />&nbsp;Sunday</li><li>&nbsp;&nbsp; <input type='checkbox' id='sMon' />&nbsp;Monday</li><li>&nbsp;&nbsp; <input type='checkbox' id='sTue' />&nbsp;Tuesday</li><li>&nbsp;&nbsp; <input type='checkbox' id='sWed' />&nbsp;Wednesday</li><li>&nbsp;&nbsp; <input type='checkbox' id='sThu' />&nbsp;Thursday</li><li>&nbsp;&nbsp; <input type='checkbox' id='sFri' />&nbsp;Friday</li></ul> &nbsp; <img alt='copy value' title='copy value' style='cursor:pointer;' id='shift_copy' border='0' src='/images/copy.png' /> &nbsp; <img title='calculate row values' alt='calculate row values' style='cursor:pointer;' id='shift_calc' border='0' src='/images/report.png' /> &nbsp; <img title='clear row values' alt='clear row values' style='cursor:pointer;' id='shift_delete' border='0' src='/images/delete.png' /> &nbsp;<div id='schedule_create_mode_div' style='display:none;'></div> <div id='supersede_div' style='display: inline; padding-left: 40px'><input style='display: inline; vertical-align: text-bottom; _vertical-align: bottom; padding: 0 !important; margin: 0 !important;' type='checkbox' name='supersede' id='supersede' checked /> <font size='3' face='Arial'>Make permanent?</font> </div></div>";
}

function printAttendanceHeader($displayDate, $controlMode) {
  $cout = "<div id='shift_date_div'></div><table id='sa_table_id' class='ui celled table' style='width:70%; border-collapse: collapse;'><thead><tr><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Employee Name</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Position</font></th><th style='background-color: #cce5ff; text-align: center; width: 180px;' id='location_code_column'><font size='3' face='Arial'>Location Code</font></th><th style='background-color: #cce5ff; width: 160px; text-align: center; cursor: pointer;' title='" . $displayDate . "'><font size='3' face='Arial'>Scheduled Hours</font></th><th style='background-color: #cce5ff; width: 160px; text-align: center; cursor: pointer;' title='" . $displayDate . "'><font size='3' face='Arial'>Clock Hours</font></th>";

  if (!$controlMode) {
    $cout .= "<th style='background-color: #cce5ff; width: 160px; text-align: center; cursor: pointer;' title='" . $displayDate . "'><font size='3' face='Arial'>Approved Hours</font></th>";
  }

  $cout .= "<th style='background-color: #cce5ff; text-align: center; width: 110px;' id='variance_column'><font size='3' face='Arial'>Status</font></th></tr></thead><tbody>";
  return $cout;
}

function printScheduleHeader($scheduleDate) {
  global $scheduleWeekDisplay;
  global $debugMode;

  if ($debugMode) { echo "<br />printScheduleHeader(" . $scheduleDate . ")<br />"; }

  $d0 = $scheduleDate;
  $d1 = (DateTime::createFromFormat('Y-m-d', $d0))->modify('+1 day')->format('Y-m-d');
  $d2 = (DateTime::createFromFormat('Y-m-d', $d1))->modify('+1 day')->format('Y-m-d');
  $d3 = (DateTime::createFromFormat('Y-m-d', $d2))->modify('+1 day')->format('Y-m-d');
  $d4 = (DateTime::createFromFormat('Y-m-d', $d3))->modify('+1 day')->format('Y-m-d');
  $d5 = (DateTime::createFromFormat('Y-m-d', $d4))->modify('+1 day')->format('Y-m-d');
  $d6 = (DateTime::createFromFormat('Y-m-d', $d5))->modify('+1 day')->format('Y-m-d');
  $scheduleWeekDisplay = "&nbsp;&nbsp;<font size='3'>Week of <b>" . $d0 . "</b> to <b>" . $d6 . "</b></font>";

  $cout = "<table id='sc_table_id' class='ui celled table' style='width:90%; border-collapse: collapse;'><thead><tr><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Employee Name</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Position</font></th><th style='background-color: #cce5ff;' id='location_code_column'><font size='3' face='Arial'>Location Code</font></th><th style='background-color: #cce5ff; width: 110px; text-align: center; cursor: pointer;' title='" . $d0 . "'><font size='3' face='Arial'>Saturday</font></th><th style='background-color: #cce5ff; width: 110px; text-align: center; cursor: pointer;' title='" . $d1 . "'><font size='3' face='Arial'>Sunday</font></th><th style='background-color: #cce5ff; width: 110px; text-align: center; cursor: pointer;' title='" . $d2 . "'><font size='3' face='Arial'>Monday</font></th><th style='background-color: #cce5ff; width: 110px; text-align: center; cursor: pointer;'><font size='3' face='Arial' title='" . $d3 . "'>Tuesday</font></th><th style='background-color: #cce5ff; width: 110px; text-align: center; cursor: pointer;' title='" . $d4 . "'><font size='3' face='Arial'>Wednesday</font></th><th style='background-color: #cce5ff; width: 110px; text-align: center; cursor: pointer;'title='" . $d5 . "'><font size='3' face='Arial'>Thursday</font></th><th style='background-color: #cce5ff; width: 110px; text-align: center; cursor: pointer;' title='" . $d6 . "'><font size='3' face='Arial'>Friday</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Total Hours</font></th></tr></thead><tbody>";
  return $cout;
}

function loadScheduleArrays($location, $locationCode, $requestDate, $lockScheduleView, $scheduleModify, $scheduleChange = NULL, $updateAttendance = NULL, $detailsMode = NULL, $overrideMode = NULL) {
  global $airportCodeDefault;
  global $scheduleArray;
  global $scheduleDate;
  global $staffArray;
  global $debugMode;

  $locationStaffArray = array();
  $tableName = null;
  $project = null;

  if ($debugMode) { echo "<br />loadScheduleArrays(): " . $location . ", " . $locationCode . ", " . $requestDate . ", " . $scheduleDate . ", " . $updateAttendance . ", " . $tableName . ", Override: " . $overrideMode . "<br />"; }
  if ($location == "") { $location = $locationCode; }
  if ($scheduleDate == $requestDate) { $tableName = "shift_schedule"; } else { $tableName = "shift_schedule_future"; }
  if (($scheduleDate == $requestDate) && $detailsMode) { $tableName = "shift_schedule_staging"; }
  if (is_null($overrideMode) && $updateAttendance) { $location = $airportCodeDefault; $tableName = "shift_schedule"; }

  if (is_null($updateAttendance)) {
    if (securityCheck("view_global_schedule", false, true)) {
      if ($location != $locationCode) {
        $sql = "SELECT LocationCode FROM Locations WHERE LocationID = " . $locationCode . " AND ActiveFlag = 1";
        $rs = execSQL($sql);

        foreach ($rs as $r) {
          if ($r[0] == "") { continue; }
          $project = $r[0];
          $location = explode(" ", $r[0])[0];
        }
      }
    }
  }

  $sql = "SELECT ssn_nid, UPPER(first_name) AS first_name, UPPER(last_name) AS last_name, business_title FROM census WHERE location_description LIKE '" . $location . "%' AND status = 'Active' AND business_title != 'Trusted Advisor' ORDER BY last_name";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r[0] == "") { continue; }
    array_push($staffArray, $r);
  }

  if ($lockScheduleView) {
      $sql = "SELECT s.*, l.LocationCode FROM " . $tableName . " s, Locations l WHERE ";
      if ($tableName == "shift_schedule_future") { $sql .= "start_date = '" . $requestDate . "' AND "; }
      $sql .= "s.location_id IN (SELECT LocationID FROM Locations WHERE ActiveFlag = 1 AND LocationCode LIKE '" . $location . "%') AND s.active = 1 AND s.location_id = l.LocationID";
  } else {
    if ($updateAttendance) {
      $sql = "SELECT * FROM " . $tableName . " WHERE location_id IN (SELECT LocationID FROM Locations WHERE ActiveFlag = 1 AND LocationCode LIKE '" . $airportCodeDefault . "%') AND active = 1";
    } else {
      $sql = "SELECT * FROM " . $tableName . " WHERE location_id = " . $locationCode . " AND '" . $requestDate . "' BETWEEN start_date AND end_date;";
    }
  }

  $rs = execSQL($sql);
  if (sizeof($rs) == 1 && $tableName == "shift_schedule_future") {
    $sql = "SELECT s.*, l.LocationCode FROM shift_schedule s, Locations l WHERE s.location_id IN (SELECT LocationID FROM Locations WHERE ActiveFlag = 1 AND LocationCode LIKE '" . $location . "%') AND s.active = 1 AND s.location_id = l.LocationID";
    $rs = execSQL($sql);
  }

  foreach ($rs as $r) {
    if ($r[0] == "") { continue; }
    array_push($scheduleArray, $r);
  }

  if ($debugMode) {
    echo "<br /><br />StaffArray: " . var_dump($staffArray) . "<br />";
    echo "<br /><br />ScheduleArray: " . var_dump($scheduleArray) . "<br />";
  }
  return $project;
}

function generateScheduleRow($rowIndex, $tableName, $effectiveDate = NULL) {
  $d = null;
  if (is_null($effectiveDate)) { $d = "NOW()"; } else { $d = "'" . $effectiveDate . "'"; }

  $sql = "INSERT INTO " . $tableName . " (location_id, start_date, ssn_nid, sat, sun, mon, tue, wed, thu, fri, active, comments) VALUES (" . $_REQUEST['location_code'][$rowIndex] . ", " . $d . ", '" . $_REQUEST['staff'][$rowIndex] . "', '" . $_REQUEST['sat'][$rowIndex] .  "', '" . $_REQUEST['sun'][$rowIndex] .  "', '" . $_REQUEST['mon'][$rowIndex] .  "', '" . $_REQUEST['tue'][$rowIndex] .  "', '" . $_REQUEST['wed'][$rowIndex] .  "', '" . $_REQUEST['thu'][$rowIndex] .  "', '" . $_REQUEST['fri'][$rowIndex] .  "', 1, NULL)";
  return execSQL($sql, true);
}

function generateScheduleNotification($l, $d) {
  $id = null;

  $sql = "INSERT INTO wf_requests (wf_class_id, employee_id, description, created_date, table_name, location, effective_date) VALUES ((SELECT wf_class_id FROM wf_classes WHERE description = 'Shift Change'), '" . $_SESSION['user']->getEmployeeID() . "', 'Schedule change request', NOW(), 'shift_schedule_future', '" . $l . "', '" . $d . "')";
  $id = execSQL($sql, true);

  generateWorkFlowNotifications($id, $l);
}

function generateWorkFlowNotifications($id, $l) {
  global $operationsEmployeeID;
  $director = null;

  $sql = "INSERT INTO wf_processes (wf_request_id, employee_id, required_action, notification, created_date) VALUES (" . $id . ", '" . $operationsEmployeeID . "', 'Shift schedule change: (" . $l . ")', 1, NOW())";
  execSQL($sql, true);

  $sql = "SELECT Director FROM Locations WHERE LocationCode LIKE '" . $l . "%'";
  $rs = execSQL($sql);
  foreach ($rs as $r) {
    if ($r[0] == "") { continue; }
    $director = $r[0];
  }

  $sql = "INSERT INTO wf_processes(wf_request_id, employee_id, required_action, notification, created_date) VALUES (" . $id . ", '" . $director . "', 'Shift schedule change: (" . $l . ")', 1, NOW())";
  execSQL($sql, true);

  if ($_SESSION['user']->getSupervisorID() != $director) {
    $sql = "INSERT INTO wf_processes(wf_request_id, employee_id, required_action, notification, created_date) VALUES (" . $id . ", '" . $_SESSION['user']->getSupervisorID() . "', 'Shift schedule change: (" . $l . ")', 1, NOW())";
    execSQL($sql, true);
  }

  $sql = "INSERT INTO wf_processes(wf_request_id, employee_id, required_action, notification, created_date) VALUES (" . $id . ", '" . $_SESSION['user']->getEmployeeID() . "', 'Shift schedule change: (" . $l . ")', 1, NOW())";
  execSQL($sql, true);
}

function generateScheduleDelta($changeID, $baseID, $l, $d) {
  global $operationsEmployeeID;

  if ($operationsEmployeeID == $_SESSION['user']->getEmployeeID()) { return; }
  $sql = "INSERT INTO shift_schedule_delta (base_id, change_id) VALUES (" . $baseID . ", " . $changeID . ")";
  $id = execSQL($sql, true);

  $sql = "INSERT INTO shift_schedule_changes (shift_schedule_delta_id, employee_id, update_ts) VALUES (" . $id . ", '" . $_SESSION['user']->getEmployeeID() . "', NOW())";
  $id = execSQL($sql, true);

  $sql = "INSERT INTO wf_requests (wf_class_id, employee_id, description, created_date, table_name, table_id, location, effective_date) VALUES ((SELECT wf_class_id FROM wf_classes WHERE description = 'Shift Change'), '" . $_SESSION['user']->getEmployeeID() . "', 'Schedule change request: (" . $l . ")', NOW(), 'shift_schedule_changes', " . $id . ", '" . $l . "', '" . $d . "')";
  $id = execSQL($sql, true);

  generateWorkFlowNotifications($id, $l);
}

function changeSchedule($location, $locationCode, $requestDate, $detailsMode = NULL) {
  global $operationsEmployeeID;
  global $scheduleCreateMode;
  global $weeklyHourlyLimit;
  global $scheduleArray;
  global $scheduleDate;
  global $currentDate;
  global $staffArray;
  global $debugMode;

  $buildingMaintenance = false;
  $operationsEditMode = false;
  $scheduleCreateMode = true;
  $scheduleEditMode = true;
  $futureSchedule = false;
  $suspended = false;
  $notes = true;
  $rowIndex = 0;

  $locationHoursArray = array();
  $locationCodeArray = array();
  $totalHoursArray = array();

  $limitDate = (DateTime::createFromFormat('Y-m-d', $requestDate))->modify('+6 day')->format('Y-m-d');
  $d = null;

  if ($_SESSION['user']->getEmployeeID() == $operationsEmployeeID) { $operationsEditMode = true; }
  loadScheduleArrays($location, $locationCode, $requestDate, true, false, true, NULL, $detailsMode);
  $cout = printScheduleHeader($requestDate);
  $cout .= "<input type='hidden' value='SCHEDULE-MODIFY' name='action' />";
  $cout .= "<input type='hidden' id='l' value='" . $_REQUEST['l'] . "' name='l' />";
  $cout .= "<input type='hidden' id='lc' value='" . $_REQUEST['lc'] . "' name='lc' />";
  $cout .= "<input type='hidden' id='lOCATION' value='" . $_REQUEST['l'] . "' name='LOCATION' />";
  $cout .= "<input type='hidden' id='LOCATIONCODE' value='" . $_REQUEST['lc'] . "' name='LOCATIONCODE' />";

  $sql = "SELECT LocationID, LocationCode From Locations WHERE LocationCode LIKE '" . $location . "%'";
  $rs1 = execSQL($sql);

  foreach ($staffArray as $s) {
    $suspended = false;
    $sql = "SELECT Description FROM Positions WHERE PositionID = (SELECT PositionID FROM EmploymentStatus WHERE Active = 1 AND EmploymentID = (SELECT EmploymentID FROM Employments WHERE EmployeeID = (SELECT EmployeeID FROM Employees WHERE SSN = '" . formatSSN(str_pad($s[0], 9, '0', STR_PAD_LEFT)) . "') LIMIT 1))";
    $rs = execSQL($sql);

    $sql = "SELECT s.suspension_date FROM suspensions s, Users u, Employees e WHERE e.EmployeeID = s.employee_id AND e.SSN = '" . formatSSN(str_pad($s[0], 9, '0', STR_PAD_LEFT)) . "' AND u.EmployeeID = s.employee_id AND u.ActiveFlag = 0";
    $rs2 = execSQL($sql);
    if (sizeof($rs2) > 1) { $suspended = true; }

    $position = null;
    foreach ($rs as $r) {
      if ($r[0] == "") { continue; }
      $position = $r[0];
    }

    if (is_null($position)) { $position = $s[3]; }
    $newScheduleRow = true;

    if ((strpos($position, "Building") >= 0) || (strpos($position, "Floor") >= 0)) { $buildingMaintenance = true; } else { $buildingMaintenance = false; }
    if ($debugMode) { echo "Building maintenance flag: " . $buildingMaintenance . "<br />"; }

    foreach ($scheduleArray as $w) {
      if (in_array($s[0], $w)) {
        $totalTime = 0;

        if (!in_array($w['LocationCode'], $locationCodeArray)) { array_push($locationCodeArray, $w['LocationCode']); }

        for ($z = 0; $z < 7; $z++) {
          $wTime = 0;
          if ($w[($z + 5)] != "") {
            $t0 = trim(explode("-", $w[($z + 5)])[0]);
            $t1 = trim(explode("-", $w[($z + 5)])[1]);
            $wTime = calculateShiftTime($t0, $t1);
            $totalTime += $wTime;
          }
          $locationHoursArray[$z] = $wTime;
        }

        $locationHoursArray[7] = $w['LocationCode'];
        array_push($totalHoursArray, $locationHoursArray);

        if ($buildingMaintenance) {
          if (checkOvertimeLimit($w[5]) || checkOvertimeLimit($w[6]) || checkOvertimeLimit($w[7]) || checkOvertimeLimit($w[8]) || checkOvertimeLimit($w[9]) || checkOvertimeLimit($w[10]) || checkOvertimeLimit($w[11])) { $suspended = true; }
        }
        if ($totalTime > $weeklyHourlyLimit && $buildingMaintenance) { $suspended = true; }

        if ($suspended) { $cout .= "<tr class='suspended'>"; } else { $cout .= "<tr>"; }
        $cout .= "<td><input type='hidden' name='staff[]' value='" . $s[0] . "' />" . $s[1] . " " . $s[2] . "</td><td>" . strtoupper($position) . "</td><td style='text-align: center;'><select name='location_code[]'>";

        foreach($rs1 as $r1) {
          if ($r1[0] == "") { continue; }
          if ($r1[0] == $w['location_id']) {
            $cout .= "<option value='" . $r1[0] . "' selected>" . $r1[1] . "</option>";
          } else {
            $cout .= "<option value='" . $r1[0] . "'>" . $r1[1] . "</option>";
          }
        }

        $cout .= "</select></td><td><center><input ";
        $d = $requestDate;
        if ($d > $currentDate) { $futureSchedule = true; }

        if ($debugMode) { $cout .= "OperationsEditMode: " . $operationsEditMode . ", currentDate: " . $currentDate . ", limitDate: " . $limitDate . ", d: " . $d . "<br />"; }
        if (!$operationsEditMode && ($currentDate <= $limitDate) && ($d < $currentDate) && !$futureSchedule) { $cout .= "readonly "; }
        $cout .= "type='text' size='10' name='sat[]' id='sat_" . $rowIndex . "' value='" . $w[5] . "' /></center></td><td><center><input ";

        $d = (DateTime::createFromFormat('Y-m-d', $scheduleDate))->modify('+1 day')->format('Y-m-d');
        if (!$operationsEditMode && ($currentDate <= $limitDate) && ($d < $currentDate) && !$futureSchedule) { $cout .= "readonly "; }
        $cout .= "type='text' size='10' name='sun[]' id='sun_" . $rowIndex . "' value='" . $w[6] . "' /></center></td><td><center><input ";

        $d = (DateTime::createFromFormat('Y-m-d', $scheduleDate))->modify('+2 day')->format('Y-m-d');
        if (!$operationsEditMode && ($currentDate <= $limitDate) && ($d < $currentDate) && !$futureSchedule) { $cout .= "readonly "; }
        $cout .= "type='text' size='10' name='mon[]' id='mon_" . $rowIndex . "' value='" . $w[7] . "' /></center></td><td><center><input ";

        $d = (DateTime::createFromFormat('Y-m-d', $scheduleDate))->modify('+3 day')->format('Y-m-d');
        if (!$operationsEditMode && ($currentDate <= $limitDate) && ($d < $currentDate) && !$futureSchedule) { $cout .= "readonly "; }
        $cout .= "type='text' size='10' name='tue[]' id='tue_" . $rowIndex . "' value='" . $w[8] . "' /></center></td><td><center><input ";

        $d = (DateTime::createFromFormat('Y-m-d', $scheduleDate))->modify('+4 day')->format('Y-m-d');
        if (!$operationsEditMode && ($currentDate <= $limitDate) && ($d < $currentDate) && !$futureSchedule) { $cout .= "readonly "; }
        $cout .= "type='text' size='10' name='wed[]' id='wed_" . $rowIndex . "' value='" . $w[9] . "' /></center></td><td><center><input ";

        $d = (DateTime::createFromFormat('Y-m-d', $scheduleDate))->modify('+5 day')->format('Y-m-d');
        if (!$operationsEditMode && ($currentDate <= $limitDate) && ($d < $currentDate) && !$futureSchedule) { $cout .= "readonly "; }
        $cout .= "type='text' size='10' name='thu[]' id='thu_" . $rowIndex . "' value='" . $w[10] . "' /></center></td><td><center><input ";

        $d = (DateTime::createFromFormat('Y-m-d', $scheduleDate))->modify('+6 day')->format('Y-m-d');
        if (!$operationsEditMode && ($currentDate <= $limitDate) && ($d < $currentDate) && !$futureSchedule) { $cout .= "readonly "; }
        $cout .= "type='text' size='10' name='fri[]' id='fri_" . $rowIndex . "' value='" . $w[11] . "' /></center></td><td><center><div class='ttd' id='total_time_div_" . $rowIndex . "' style='display: inline;'>" . $totalTime . "</div></center></td></tr>";
        $newScheduleRow = false;
        break;
      }
    }

    if ($newScheduleRow) {
      $cout .= "<tr><td><input type='hidden' name='staff[]' value='" . $s[0] . "' />" . $s[1] . " " . $s[2] . "</td><td>" . strtoupper($position) . "</td><td style='text-align: center;'><select name='location_code[]'>";

        foreach($rs1 as $r1) {
          if ($r1[0] == "") { continue; }
          $cout .= "<option value='" . $r1[0] . "'>" . $r1[1] . "</option>";
        }

        $cout .= "</select></td><td><center><input type='text' size='10' name='sat[]' id='sat_" . $rowIndex . "' /></center></td><td><center><input type='text' size='10' name='sun[]' id='sun_" . $rowIndex . "' /></center></td><td><center><input type='text' size='10' name='mon[]' id='mon_" . $rowIndex . "' /></center></td><td><center><input type='text' size='10' name='tue[]' id='tue_" . $rowIndex . "' /></center></td><td><center><input type='text' size='10' name='wed[]' id='wed_" . $rowIndex . "' /></center></td><td><center><input type='text' size='10' name='thu[]' id='thu_" . $rowIndex . "' /></center></td><td><center><input type='text' size='10' name='fri[]' id='fri_" . $rowIndex . "' /></center></td><td><center><div class='ttd' id='total_time_div_" . $rowIndex . "' style='display: inline;'>0</div></center></td></tr>";
    }
    $rowIndex++;
  }

  foreach ($locationCodeArray as $l) {
    $w0 = $w1 = $w2 = $w3 = $w4 = $w5 = $w6 = $w7 = 0;
    $cout .= "<tr bgcolor='#efefef'><td colspan='3' style='text-align: right; border: 1px solid #000;'>Total Hours (<b>" . $l . "</b>):</td>";
    foreach ($totalHoursArray as $t) {
      if ($t[7] == $l) {
        $w0 += ($t[0]);
        $w1 += ($t[1]);
        $w2 += ($t[2]);
        $w3 += ($t[3]);
        $w4 += ($t[4]);
        $w5 += ($t[5]);
        $w6 += ($t[6]);
      }
    }
    $cout .= "<td style='text-align: center;'><div id='sat_tt'>" . $w0 . "</div></td><td style='text-align: center;'><div id='sun_tt'>" . $w1 . "</div></td><td style='text-align: center;'><div id='mon_tt'>" . $w2 . "</div></td><td style='text-align: center;'><div id='tue_tt'>" . $w3 . "</div></td><td style='text-align: center;'><div id='wed_tt'>" . $w4 . "</div></td><td style='text-align: center;'><div id='thu_tt'>" . $w5 . "</div></td><td style='text-align: center;'><div id='fri_tt'>" . $w6 . "</div></td><td style='text-align: center;'><div id='total_schedule_hours_div'>";
    $cout .= ($w0 + $w1 + $w2 + $w3 + $w4 + $w5 + $w6) . "</div></td></tr>";
  }
  $cout .= "</tbody></table>";

  $sql = "SELECT shift_schedule_notes_id, description FROM shift_schedule_notes WHERE location_code = '" . $location . "'";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r[0] == "") { continue; }
    $notes = false;
    $cout .= "<center><table width='600' border='0' style='border-collapse: collapse; border: 1px solid black;'><tr><td class='navtitle'>National Aviation Services Shift Schedule Notes</td></tr><tr><td><textarea name='shift_schedule_notes' id='shift_schedule_notes' rows='3' cols='150'>" . $r['description'] . "</textarea></td></tr></table>";
  }

  if ($notes) {
    $cout .= "<center><table width='600' border='0' style='border-collapse: collapse; border: 1px solid black;'><tr><td class='navtitle'>National Aviation Services Shift Schedule Notes</td></tr><tr><td><textarea name='shift_schedule_notes' id='shift_schedule_notes' rows='3' cols='150'></textarea></td></tr></table>";
  }

  $cout .= "<input type='hidden' id='scm' value='" . $scheduleCreateMode . "' name='scm' />";
  return $cout;
}

function checkOvertimeLimit($d) {
  global $dailyHourlyLimit;

  if ($d == "") { return false; }
  $t0 = trim(explode("-", $d)[0]);
  $t1 = trim(explode("-", $d)[1]);
  $wTime = calculateShiftTime($t0, $t1);
  if ($wTime > $dailyHourlyLimit) { return true; }
  return false;
}

function checkClockLimit($v) {
  global $dailyHourlyLimit;

  if ($v == "") { return false; }
  if ($v > $dailyHourlyLimit) { return true; }
  return false;
}

function calculateHours($d) {
  if ($d == "") { return 0; }
  $t0 = trim(explode("-", $d)[0]);
  $t1 = trim(explode("-", $d)[1]);
  return calculateShiftTime($t0, $t1);
}

function checkWeeklyHours($sat, $sun, $mon, $tue, $wed, $thu, $fri) {
  global $weeklyHourlyLimit;
  if ((calculateHours($sat) + calculateHours($sun) + calculateHours($mon) + calculateHours($tue) + calculateHours($wed) + calculateHours($thu) + calculateHours($fri)) > $weeklyHourlyLimit) {
    return true;
  }
  return false;
}

function printScheduleLocation() {
  global $projectOptionDefault;

  $l = $projectOptionDefault;
  if (isset($_REQUEST['l'])) { $l = $_REQUEST['l']; }
  $sql = "SELECT Name FROM ArptRsrvCntr WHERE AirportCode = (SELECT SUBSTRING_INDEX(LocationCode, \" \", 1) From Locations WHERE LocationID = " . $l . ")";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r[0] == "") { continue; }
    echo "<font size='5' face='Arial'><b>" . $r[0] . "</b></font>";
  }
}

function printAttendanceLocation() {
  global $airportOptionDefault;
  global $debugMode;

  if ($debugMode) { echo "<br />printAttendanceLocation()<br />"; }
  $sql = "SELECT Name FROM ArptRsrvCntr WHERE ArptRsrvCntrID = " . $airportOptionDefault;
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r[0] == "") { continue; }
    echo "<font size='5' face='Arial'><b>" . $r[0] . "</b></font>";
  }
}

function shiftReportAvailable($a) {
  global $debugMode;

  if ($debugMode) { echo "Invoked shiftReportAvailable(" . $a . ")<br />"; }

  $sql = "SELECT shift_report_id FROM shift_report WHERE location = (SELECT AirportCode FROM ArptRsrvCntr WHERE ArptRsrvCntrID = " . $a . ") AND DATE(shift_date) = (CURDATE() - 1) AND reported = 1";
  $rs = execSQL($sql);

  if ($debugMode && sizeof($rs) == 1) { echo "shiftReportAvailable(" . $a . ") returning true<br />"; }
  if (sizeof($rs) == 1) { return true; }
  return false;
}

function generateOutput($s, $coutMode) {
  if ($coutMode) { return $s; }
  echo $s;
}

function generateAttendance($locationCode, $controlMode, $coutMode = NULL, $locationOverride = NULL) {
  global $stationAttendanceArray;
  global $updateAttendanceMode;
  global $operationsEmployeeID;
  global $viewGlobalSchedule;
  global $airportCodeDefault;
  global $scheduleCreateMode;
  global $scheduleEditMode;
  global $shiftDisplayDate;
  global $overrideDisplay;
  global $shiftBudgetMode;
  global $baseServiceRow;
  global $scheduleArray;
  global $scheduleDate;
  global $serviceRow;
  global $staffArray;
  global $debugMode;
  global $location;
  global $manager;

  $userShiftArray = array();
  $userRowArray = array();
  $approvedHours = null;
  $updateMode = false;
  $override = false;
  $position = null;
  $project = null;
  $mapping = "0";
  $aTime = null;
  $cout = null;

  if ($locationOverride) { $location = $locationOverride; }
  if ($_SESSION['user']->getEmployeeID() == $operationsEmployeeID) { $controlMode = true; $updateAttendanceMode = true; }
  if ($debugMode) {  $cout .= generateOutput("<br />generateAttendance() ... location: " . $location . ", locationCode: " . $locationCode . ", controlMode: " . $controlMode . "<br />", $coutMode); }
  if (securityCheck("view_global_schedule", false, true)) { $viewGlobalSchedule = true; }

  $currentDate = date('Y-m-d', strtotime("yesterday"));
  $cn = (intval(date('w', strtotime($currentDate))) + 6);
  if ($cn == 12) { $cn = 5; }

  $project = loadScheduleArrays($location, $locationCode, $currentDate, false, false, NULL, true, NULL, $locationOverride);
  if ($location == "Office") { $location = $airportCodeDefault; }
  if ($manager) { $attendanceEditMode = true; }

  if (in_array($location, $stationAttendanceArray)) {
    if (sizeof($scheduleArray) == 0) {
      $cout .= generateOutput("No schedule exists for this location.<br />", $coutMode);
    } else {

      $overrideDisplay = true;
      $updateMode = true;
      $shiftDisplayDate = $currentDate;
      $cout .= generateOutput(printAttendanceHeader($shiftDisplayDate, $controlMode), $coutMode);

      if ($debugMode) {
        $cout .= generateOutput("<br />StaffArray:<br />", $coutMode);
        $cout .= generateOutput(var_dump($staffArray) . "<br /><br />", $coutMode);

        $cout .= generateOutput("<br />ScheduleArray:<br />", $coutMode);
        $cout .= generateOutput(var_dump($scheduleArray) . "<br /></br>", $coutMode);
      }

      foreach ($staffArray as $s) {
        $sql = "SELECT Description FROM Positions WHERE PositionID = (SELECT PositionID FROM EmploymentStatus WHERE Active = 1 AND EmploymentID = (SELECT EmploymentID FROM Employments WHERE EmployeeID = (SELECT EmployeeID FROM Employees WHERE SSN = '" . formatSSN(str_pad($s[0], 9, '0', STR_PAD_LEFT)) . "') LIMIT 1))";
        $rs = execSQL($sql);

        $position = null;
        foreach ($rs as $r) { if ($r[0] == "") { continue; } $position = $r[0]; }
        if (is_null($position)) { $position = $s[3]; }

        foreach ($scheduleArray as $w) {
          if ($s['ssn_nid'] == $w['ssn_nid']) {

            $sql = "SELECT location_id, TIMEDIFF(clock_out, clock_in) AS wTime, TIMEDIFF(NOW(), clock_in) AS sTime FROM shift_clock WHERE ssn_nid = '" . $w['ssn_nid'] . "' AND DATE(shift_date) = (CURDATE() - 1)";
            $rs = execSQL($sql);

            $sql = "SELECT LocationID, LocationCode From Locations WHERE LocationCode LIKE '" . $location . "%'";
            $rs1 = execSQL($sql);

            if (sizeof($rs) == 1) {
              if ($w[$cn] == "") {
                $wTime = 0;
              } else {
                $t0 = trim(explode("-", $w[$cn])[0]);
                $t1 = trim(explode("-", $w[$cn])[1]);
                $wTime = calculateShiftTime($t0, $t1);
              }

              $cout .= generateOutput("<tr><td><input type='hidden' name='staff[]' value='" . $s[0] . "' />" . $s[1] . " " . $s[2] . "</td><td>" . strtoupper($position) . "</td><td style='text-align: center;'>", $coutMode);

              if ($viewGlobalSchedule) { $cout .= generateOutput("<select name='location_code[]'>", $coutMode); } else { $cout .= generateOutput("<input type='hidden' name='location_code[]' ", $coutMode); }
              foreach($rs1 as $r1) {
                if ($r1[0] == "") { continue; }
                if ($r1[0] == $w['location_id']) {
                  if ($viewGlobalSchedule) {
                    $cout .= generateOutput("<option value='" . $r1[0] . "' selected>" . $r1[1] . "</option>",  $coutMode);
                  } else {
                    $cout .= generateOutput("value = '" . $r1[0] . "' />" . $r1[1], $coutMode);
                  }
                } else {
                  if ($viewGlobalSchedule) { $cout .= generateOutput("<option value='" . $r1[0] . "'>" . $r1[1] . "</option>", $coutMode); }
                }
              }

              //if (strtoupper($position) == "STATION MANAGER") { $aTime = number_format($wTime, 1); } else { $aTime = 0; }
              $aTime = 0;
              
              if ($viewGlobalSchedule) {
                $cout .= generateOutput("</select></td><td style='text-align: center;'><input id='scheduled_" . mt_rand() . "' type='text' size='3' name='scheduled[]' value='" . $wTime . "' /></td><td style='text-align: center;'><input id='actual_" . mt_rand() . "' type='text' size='3' name='actual[]' value='0' /></td>", $coutMode);
                if (!$controlMode) { $cout .= generateOutput("<td style='text-align: center;'><input id='actual_" . mt_rand() . "' type='text' size='3' name='approved[]' value='0' /></td>", $coutMode); }
              }  else {
                $cout .= generateOutput("</td><td style='text-align: center;'><input id='scheduled_" . mt_rand() . "' type='hidden' name='scheduled[]' value='" . $wTime . "' />" . $wTime . "</td><td style='text-align: center;'><input id='actual_" . mt_rand() . "' type='hidden' name='actual[]' value='" . $aTime . "' />" . $aTime . "</td>", $coutMode);
                if (!$controlMode) { $cout .= generateOutput("<td style='text-align: center;'><input id='approved_" . mt_rand() . "' type='hidden' name='approved[]' value='0' />0</td>", $coutMode); }
              }

              if ($wTime > 0 && $aTime == 0) {
                $cout .= generateOutput("<td style='text-align: center;'><img border='0' title='Employee scheduled but did not clock in' src='/images/flag.gif' width='24' /><input type='hidden' name='override' value='1' />", $coutMode);
              } else {
                $cout .= generateOutput("<td>&nbsp;", $coutMode);
              }
              
              $cout .= generateOutput("</td></tr>", $coutMode);
              break;

            } else {

              foreach ($rs as $r) {
                if ($r[0] == "") { continue; }

                if ($w[$cn] == null) {
                  $scheduledTime = 0;
                } else {
                  $t0 = trim(explode("-", $w[$cn])[0]);
                  $t1 = trim(explode("-", $w[$cn])[1]);
                  $scheduledTime = calculateShiftTime($t0, $t1);
                }

                if ($r['wTime'] == "") {
                  $wTime = explode(":", $r['sTime']);
                } else {
                  $wTime = explode(":", $r['wTime']);
                }

                $sh = intval($wTime[0]);
                $sm = intval($wTime[1]);
                $ss = intval($wTime[2]);

                if ($ss > 30) { $sm++; }
                $sm = number_format(($sm / 60), 1);
                $wTime = number_format(($sh + $sm), 1);

                $userShiftLoaded = false;
                foreach ($userShiftArray as $u) { if ($u->getSSN() == $w['ssn_nid']) { $userShiftLoaded = true; $u->setWTime($wTime); }}
                 if (sizeof($userShiftArray) == 0 || !$userShiftLoaded) {
                  $u = new UserShift($wTime, $scheduledTime, $w['ssn_nid']);
                  array_push($userShiftArray, $u);
                }
              }

              foreach ($rs as $r) {
                if ($r[0] == "") { continue; }

                if (in_array($w['ssn_nid'], $userRowArray)) { continue; }
                array_push($userRowArray, $w['ssn_nid']);

                foreach ($userShiftArray as $u) {
                  if ($u->getSSN() == $w['ssn_nid']) {
                    $scheduledHours = $u->getSTime();
                    $wTime = $u->getWTime();
                    break;
                  }
                }

                $cout .= generateOutput("<tr><td><input type='hidden' name='staff[]' value='" . $s[0] . "' />" . $s[1] . " " . $s[2] . "</td><td>" . strtoupper($position) . "</td><td style='text-align: center;'>", $coutMode);
                if ($viewGlobalSchedule) { $cout .= generateOutput("<select name='location_code[]'>", $coutMode); } else { $cout .= generateOutput("<input type='hidden' name='location_code[]' ", $coutMode); }

                foreach($rs1 as $r1) {
                  if ($r1[0] == "") { continue; }
                  if ($r1[0] == $w['location_id']) {
                    if ($viewGlobalSchedule) {
                      $cout .= generateOutput("<option value='" . $r1[0] . "' selected>" . $r1[1] . "</option>", $coutMode);
                    } else {
                      $cout .= generateOutput("value = '" . $r1[0] . "' />" . $r1[1], $coutMode);
                    }
                  } else {
                    if ($viewGlobalSchedule) { $cout .= generateOutput("<option value='" . $r1[0] . "'>" . $r1[1] . "</option>", $coutMode); }
                  }
                }

                $approvedHours = "0";
                $sql = "SELECT approved_hours FROM shift_hours WHERE shift_report_id = (SELECT shift_report_id FROM shift_report WHERE location = '" . $location . "' AND shift_date = (CURDATE() - 1)) AND ssn_nid = '" . $s['ssn_nid'] . "'";
                $rs2 = execSQL($sql);
                foreach ($rs2 as $r2) {
                  if ($r2[0] == "") { continue; }
                  $approvedHours = $r2[0];
                }

                if ($viewGlobalSchedule) {
                  $cout .= generateOutput("</select></td><td style='text-align: center;'><input id='scheduled_" . mt_rand() . "' type='text' size='3' name='scheduled[]' value='" . $scheduledTime . "' /></td><td style='text-align: center;'><input id='actual_" . mt_rand() . "' type='text' size='3' name='actual[]' value='" . $wTime . "' /></td>", $coutMode);
                  if (!$controlMode) { $cout .= generateOutput("<td style='text-align: center;'><input id='actual_" . mt_rand() . "' type='text' size='3' name='approved[]' value='" . rtrim($approvedHours, "0") . "' /></td>", $coutMode); }
                } else {
                  $cout .= generateOutput("</td><td style='text-align: center;'><input id='scheduled_" . mt_rand() . "' type='hidden' name='scheduled[]' value='" . $scheduledTime . "' />" . $scheduledTime . "</td><td style='text-align: center;'><input id='actual_" . mt_rand() . "' type='hidden' name='actual[]' value='" . $wTime . "' />" . $wTime . "</td>", $coutMode);
                  if (!$controlMode) { $cout .= generateOutput("<td style='text-align: center;'><input id='approved_" . mt_rand() . "' type='hidden' name='approved[]' value='" . rtrim($approvedHours, "0") . "' />0</td>", $coutMode); }
                }

                $cout .= generateOutput("<td style='text-align: center;'>", $coutMode);
                if (($wTime && $w[$cn] == "") || $r['wTime'] == "") {
                  if ($wTime && $w[$cn] == "") { echo "<img border='0' title='Employee not scheduled today' src='/images/calendar.gif' width='24' /><input type='hidden' name='override' value='1' />"; }
                  if ($r['wTime'] == "") { echo " &nbsp; <img border='0' title='Employee still on the clock' src='/images/clock.gif' width='24' /><input type='hidden' name='override' value='1' />"; }
                } else { 
                  $cout .= generateOutput("&nbsp;", $coutMode); 
                }

                $cout .= generateOutput("</td></tr>", $coutMode);
              }
            }
          }
        }
      }
      $cout .= generateOutput("</table>", $coutMode);
    }
  } else {
    $shiftBudgetMode = true;
  }

  $cout .= generateOutput("<center><p>&nbsp;</p><div id='override_div' style='display: none;'><table width='30%' border='1' bordercolor='#000000' style='border-collapse: collapse; border: 1px solid black;'><tr><td class='navtitle'>National Aviation Services Override Description <div style='float: right;'></td></tr><tr><td><textarea cols='150' rows='3' id='override_description' name='override_description'></textarea></td></tr></table><p>&nbsp;</p></div><table width='30%' border='1' bordercolor='#000000' style='border-collapse: collapse; border: 1px solid black;' id='shift_services'><tr><td colspan='4' class='navtitle'>National Aviation Services Budget Hours ", $coutMode);

  if ($controlMode) { $cout .= generateOutput("<div style='float: right;'><span style='cursor: pointer;' id='add_service' class='glyphicon glyphicon-plus' /></div>", $coutMode); }
  $cout .= generateOutput("</td></tr>", $coutMode);

  $serviceRow = "";
  $sql = "SELECT location_code FROM services_mapping WHERE location_code = '" . $location . "'";
  $rs = execSQL($sql);

  $sql = "SELECT n.name, s.service, b.aircraft, b.aircrafttype FROM ns_customers n, services s, shift_budget b WHERE b.shift_report_id = (SELECT shift_report_id FROM shift_report WHERE location = '" . $_SESSION['user']->getLocation() . "' AND shift_date = (CURDATE() - 1)) AND n.ns_customer_id = b.ns_customer_id AND s.service_id = b.service_id";
  $rs3 = execSQL($sql);
  if (sizeof($rs3) > 1 && ($_SESSION['user']->getEmployeeID() == $operationsEmployeeID)) { $updateMode = true; }

  if (sizeof($rs) == 1) {
    $cout .= generateOutput("<tr bgcolor='#efefef'><td><b>Customer</b></td><td><b>Aircraft Type</b></td><td><b>Service</b></td><td><b>Tail Number</b></td></tr>", $coutMode);

    foreach ($rs3 as $r3) {
      if ($r3[0] == "") { continue; }
      $serviceRow .= "<tr bgcolor='#ffffff'><td style='padding: 7px; text-align: left;'><select class='customersel' name='customer[]'>";

      $sql = "SELECT ns_customer_id, name FROM ns_customers ORDER BY name";
      $rs = execSQL($sql);

      foreach ($rs as $r) {
        if ($r[0] == "") { continue; }
        $serviceRow .= "<option value='" . $r[0] . "'";
        if ($r3['name'] == $r[1]) { $serviceRow .= " selected"; }
        $serviceRow .= ">" . $r[1] . "</option>";
      }

      $serviceRow .= "</select></td><td style='padding: 7px; text-align: left;'><select class='aircrafttypesel' name='aircraftType[]'>";
      $sql = "SELECT distinct(type) from fleet_list ORDER BY type";
      $fleet_rs = execSQL($sql);
  
      foreach ($fleet_rs as $r) {
        if ($r[0] == "") { continue; }
        $serviceRow .= "<option value='" . $r[0] . "'";
        if ($r3['aircrafttype'] == $r[0]) { $serviceRow .= " selected"; }
        $serviceRow .= ">" . $r[0];
        $serviceRow .= "</option>";
      }
  
      $serviceRow .= "</select></td><td style='padding: 7px; text-align: left;'><select name='service[]'>";

      $sql = "SELECT service_id, service, description FROM services ORDER BY service";
      $rs = execSQL($sql);

      foreach ($rs as $r) {
        if ($r[0] == "") { continue; }
        $serviceRow .= "<option value='" . $r[0] . "'";
        if ($r3['service'] == $r[1]) { $serviceRow .= " selected"; }
        $serviceRow .= ">" . $r[1];

        if ($r[2] != "") { $serviceRow .= " (" . $r[2] . ")"; }
        $serviceRow .= "</option>";
      }
      $serviceRow .= "</select></td><td style='padding: 7px; text-align: left;'>";

      if ($_SESSION['user']->getEmployeeID() == $operationsEmployeeID) {
        error_log("tje rs is " . $r3['name'] . "asdfasd"  . $r3['aircrafttype']);
        $serviceRow .= genTailNumberSelection($r3['name'], $r3['aircrafttype'], $r3['aircraft']) . "</td></tr>";
      } else {
        $serviceRow .= $r3['aircraft'] . "<input type='hidden' name='tail_number[]' value='" . $r3['aircraft'] . "' /></td></tr>";
      }
    }


    $baseServiceRow = "<tr bgcolor='#ffffff'><td style='padding: 7px; text-align: left;'><select class='customersel' name='customer[]'>";
    $sql = "SELECT ns_customer_id, name FROM ns_customers ORDER BY name";
    $rs = execSQL($sql);

    foreach ($rs as $r) {
      if ($r[0] == "") { continue; }
      $baseServiceRow .= "<option value='" . $r[0] . "'>" . $r[1] . "</option>";
    }

    $baseServiceRow .= "</select></td><td style='padding: 7px; text-align: left;'><select class='aircrafttypesel' name='aircraftType[]'>";
    $sql = "SELECT distinct(type) from fleet_list ORDER BY type";
    $fleet_rs = execSQL($sql);

    foreach ($fleet_rs as $r) {
      if ($r[0] == "") { continue; }
      $baseServiceRow .= "<option value='" . $r[0] . "'";
      //if ($r3['service'] == $r[1]) { $serviceRow .= " selected"; }
      $baseServiceRow .= ">" . $r[0];

      //if ($r[2] != "") { $serviceRow .= " (" . $r[2] . ")"; }
      $baseServiceRow .= "</option>";
    }

    $baseServiceRow .= "</select></td><td style='padding: 7px; text-align: left;'><select name='service[]'>";

    $sql = "SELECT service_id, service, description FROM services ORDER BY service";
    $rs = execSQL($sql);

    foreach ($rs as $r) {
      if ($r[0] == "") { continue; }
      $baseServiceRow .= "<option value='" . $r[0] . "'>" . $r[1];

      if ($r[2] != "") { $baseServiceRow .= " (" . $r[2] . ")"; }
      $baseServiceRow .= "</option>";
    }
    $baseServiceRow .= "</select></td><td style='padding: 7px; text-align: left;'><select style='width:100px' class='tailsel' name='tail_number[]'/></td></tr>";

    if ($controlMode && $serviceRow == "") { $serviceRow .= $baseServiceRow; }
    $cout .= generateOutput($serviceRow, $coutMode);

  } else {
    $mapping = "1";
    foreach ($rs3 as $r3) {
      if ($r3[0] == "") { continue; }
      $serviceRow .= "<tr bgcolor='#ffffff'><td style='padding: 7px; text-align: left;'><b>Service:</b> RON</td><td colspan='2' style='padding: 7px; text-align: left;'><b>Tail Number:</b> ";
      if ($_SESSION['user']->getEmployeeID() == $operationsEmployeeID) {
        $serviceRow .= "<input type='text' size='8' name='tail_number[]' value='" . $r3['aircraft'] . "' /></td></tr>";
      } else {
        $serviceRow .= $r3['aircraft'] . "<input type='hidden' name='tail_number[]' value='" . $r3['aircraft'] . "' /></td></tr>";
      }
    }

    $baseServiceRow .= "<tr bgcolor='#ffffff'><td style='padding: 7px; text-align: left;'><b>Service:</b> RON</td><td colspan='2' style='padding: 7px; text-align: left;'><b>Tail Number:</b> <input type='text' size='8' name='tail_number[]' /></td></tr>";

    if ($controlMode && $serviceRow == "") { $serviceRow .= $baseServiceRow; }
    $cout .= generateOutput($serviceRow, $coutMode);
  }

  $cout .= generateOutput("</table><input type='hidden' name='mapping' value='" . $mapping . "' />", $coutMode);
  if ($updateMode) { $cout.= generateOutput("<input type='hidden' name='update_mode' value='1' />", $coutMode); }
  return $cout;
}

function genTailNumberSelection($customer, $aircrafttype, $tailnumber) {
  $sql = "SELECT tail from fleet_list where INSTR('" . $customer . "', customer) and type='" . $aircrafttype . "'";
  //error_log("sql is " . $sql);
  $rs = execSQL($sql);
  $out="<select style='width:100px' class='tailsel'>";
  if ( is_null($tailnumber) ) {
    $out .= "<option value=''>   &nbsp;&nbsp;&nbsp;&nbsp;     </option>";
  }

  foreach ($rs as $r) {
    if ($r[0] == "") { continue; }
    $out .= "<option value='" . $r['tail'] . "'";
    if ( $r['tail'] == $tailnumber) { $out .= " selected"; }
    $out .= ">" . $r['tail'] . "</option>";
  }
  $out .= "</select>";
  return $out;
}

function printTimeClock() {
  global $shiftClockTime;
  global $shiftTotalTime;

  //if ($_SESSION['user']->getPayType() != "Hourly") { return; }
  if ($_SESSION['user']->getLocation() == "Office" || is_null($_SESSION['user']->getShiftDate())) { return; }
  $shiftTotalTime = 0;

  $sql = "SELECT shift_clock_id, clock_in FROM shift_clock WHERE ssn_nid = '" . $_SESSION['user']->getSSN() . "' AND DATE(shift_date) = " . $_SESSION['user']->getShiftDate() . " AND reported = 0 AND clock_out IS NULL";
  $rs = execSQL($sql);
  if (sizeof($rs) == 1) {
    echo "<a href='#' id='punch_clock' class='w3-bar-item w3-button'><div id='clock_link'>Clock in</div></a>";
  } else {

    foreach ($rs as $r) { if ($r[0] == "") { continue; } $shiftClockTime = $r['clock_in']; }
    $sql = "SELECT shift_clock_id, TIMEDIFF(clock_out, clock_in) AS clock_time FROM shift_clock WHERE ssn_nid = '" . $_SESSION['user']->getSSN() . "' AND DATE(shift_date) = " . $_SESSION['user']->getShiftDate() . " AND reported = 0 AND clock_out IS NOT NULL";
    $rs = execSQL($sql);
    if (sizeof($rs) != 1) {
      foreach ($rs as $r) {
        if ($r[0] == "") { continue; }
        $shiftTotalTime += calculateShiftTotalTime($r['clock_time']);
      }
    }
    echo "<a href='#' id='punch_clock' class='w3-bar-item w3-button'><div id='clock_link'>Clock out</div></a>";
  }
}

function calculateShiftTotalTime($t) {
  $t = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $t);
  sscanf($t, "%d:%d:%d", $hours, $minutes, $seconds);
  return $hours * 3600 + $minutes * 60 + $seconds;
}

function printSchedule($locationCode, $requestDate, $lockScheduleView, $detailsMode = NULL) {
  global $scheduleCurrentWeek;
  global $scheduleCreateMode;
  global $weeklyHourlyLimit;
  global $scheduleEditMode;
  global $scheduleArray;
  global $scheduleDate;
  global $staffArray;
  global $debugMode;
  global $location;
  global $manager;

  $currentDate = date('Y-m-d', strtotime("last Saturday"));
  $buildingMaintenance = false;
  $available = false;
  $suspended = false;
  $position = null;
  $project = null;
  $rowIndex = 0;

  $locationHoursArray = array();
  $locationCodeArray = array();
  $totalHoursArray = array();

  if ($debugMode) { echo "<br />printSchedule()<br />"; }
  if ($debugMode) { echo "requestDate: " . $requestDate . ", currentDate: " . $currentDate . ", available: " . $available . "<br >"; }
  if (date('Y-m-d', strtotime(" . $requestDate . ")) >= $currentDate) { $available = true; $scheduleCurrentWeek = true; }
  $project = loadScheduleArrays($location, $locationCode, $requestDate, $lockScheduleView, false, NULL, NULL, $detailsMode);
  if ($location == "") { $location = $locationCode; }

  if ($debugMode) {
    echo "<br /><br />StaffArray: " . var_dump($staffArray) . "<br />";
    echo "<br /><br />ScheduleArray: " . var_dump($scheduleArray) . "<br />";
  }

  if ($location == "Office") { $available = false; }
  if ($debugMode) { echo "location: " . $location . ", available: " . $available . "<br />"; }

  if (sizeof($scheduleArray) == 0) {
    echo printScheduleHeader($currentDate);

    $sql = "SELECT LocationID, LocationCode From Locations WHERE LocationCode LIKE '" . $location . "%'";
    $rs1 = execSQL($sql);

    if ($manager && $available) {
      $scheduleCreateMode = true;
      echo "<input type='hidden' value='SCHEDULE-CREATE' name='action' />";
      foreach ($staffArray as $s) {
        $sql = "SELECT Description FROM Positions WHERE PositionID = (SELECT PositionID FROM EmploymentStatus WHERE Active = 1 AND EmploymentID = (SELECT EmploymentID FROM Employments WHERE EmployeeID = (SELECT EmployeeID FROM Employees WHERE SSN = '" . formatSSN(str_pad($s[0], 9, '0', STR_PAD_LEFT)) . "') LIMIT 1))";
        $rs = execSQL($sql);

        $position = null;
        foreach ($rs as $r) {
          if ($r[0] == "") { continue; }
          $position = $r[0];
        }

        $sql = "SELECT s.suspension_date FROM suspensions s, Users u, Employees e WHERE e.EmployeeID = s.employee_id AND e.SSN = '" . formatSSN(str_pad($s[0], 9, '0', STR_PAD_LEFT)) . "' AND u.EmployeeID = s.employee_id AND u.ActiveFlag = 0";
        $rs2 = execSQL($sql);
        if (sizeof($rs2) == 1) { $suspended = false; } else { $suspended = true; }


        if (is_null($position)) { $position = $s[3]; }
        if ($suspended) { echo "<tr class='suspended'>"; } else { echo "<tr>"; }
        echo "<td><input type='hidden' name='staff[]' value='" . $s[0] . "' />" . $s[1] . " " . $s[2] . "</td><td>" . strtoupper($position) . "</td><td style='text-align: center;'><select name='location_code[]'>";

        foreach($rs1 as $r1) {
          if ($r1[0] == "") { continue; }
          echo "<option value='" . $r1[0] . "'>" . $r1[1] . "</option>";
        }

        echo "</select><td><center><input type='text' size='10' name='sat[]' id='sat_" . $rowIndex . "' /></center></td><td><center><input type='text' size='10' name='sun[]' id='sun_" . $rowIndex . "' /></center></td><td><center><input type='text' size='10' name='mon[]' id='mon_" . $rowIndex . "' /></center></td><td><center><input type='text' size='10' name='tue[]' id='tue_" . $rowIndex . "' /></center></td><td><center><input type='text' size='10' name='wed[]' id='wed_" . $rowIndex . "' /></center></td><td><center><input type='text' size='10' name='thu[]' id='thu_" . $rowIndex . "' /></center></td><td><center><input type='text' size='10' name='fri[]' id='fri_" . $rowIndex . "' /></center></td><td><div id='hours_" . $rowIndex . "'>0</div></td></tr>";
        $rowIndex++;
      }

      echo "</tbody></table><center><table width='600' border='0' style='border-collapse: collapse; border: 1px solid black;'><tr><td class='navtitle'>National Aviation Services Shift Schedule Notes</td></tr><tr><td><textarea name='shift_schedule_notes' id='shift_schedule_notes' rows='3' cols='150'></textarea></td></tr></table>";
    } else {
      echo "<tr><td colspan='10'><center>NO SCHEDULE EXISTS FOR THIS WEEK</center></td></tr></tbody></table>";
    }

  } else {

    echo printScheduleHeader($requestDate);
    if ($manager && $available) { $scheduleEditMode = true; }
    foreach ($staffArray as $s) {
      $sql = "SELECT Description FROM Positions WHERE PositionID = (SELECT PositionID FROM EmploymentStatus WHERE Active = 1 AND EmploymentID = (SELECT EmploymentID FROM Employments WHERE EmployeeID = (SELECT EmployeeID FROM Employees WHERE SSN = '" . formatSSN(str_pad($s[0], 9, '0', STR_PAD_LEFT)) . "') LIMIT 1))";
      $rs = execSQL($sql);

      $position = null;
      foreach ($rs as $r) {
        if ($r[0] == "") { continue; }
        $position = $r[0];
      }

      if (is_null($position)) { $position = $s[3]; }

      if (sizeof($scheduleArray) == 0) {
        for ($z = 0; $z < 8; $z++) { echo "<td></td>"; } 
        echo "</tr>";
        continue; 
      }

      $newScheduleRow = true;
      foreach ($scheduleArray as $w) {
        $suspended = false;
        if (in_array($s[0], $w)) {
          $totalTime = 0;

          if (!in_array($w['LocationCode'], $locationCodeArray)) { array_push($locationCodeArray, $w['LocationCode']); }

          for ($z = 0; $z < 7; $z++) {
            $wTime = 0;
            if ($w[($z + 5)] != "") {
              $t0 = trim(explode("-", $w[($z + 5)])[0]);
              $t1 = trim(explode("-", $w[($z + 5)])[1]);
              $wTime = calculateShiftTime($t0, $t1);
              $totalTime += $wTime;
            }
            $locationHoursArray[$z] = $wTime;
          }

          $locationHoursArray[7] = $w['LocationCode'];
          array_push($totalHoursArray, $locationHoursArray);

          $sql = "SELECT s.suspension_date FROM suspensions s, Users u, Employees e WHERE e.EmployeeID = s.employee_id AND e.SSN = '" . formatSSN(str_pad($s[0], 9, '0', STR_PAD_LEFT)) . "' AND u.EmployeeID = s.employee_id AND u.ActiveFlag = 0";
          $rs2 = execSQL($sql);
          if (sizeof($rs2) > 1) { $suspended = true; }

          if ((strpos($position, "Building") >= 0) || (strpos($position, "Floor") >= 0)) { $buildingMaintenance = true; } else { $buildingMaintenance = false; }
          if ($debugMode) { echo "Building maintenance flag: " . $buildingMaintenance . "<br />"; }

          if ($buildingMaintenance) {
            if (checkOvertimeLimit($w[5]) || checkOvertimeLimit($w[6]) || checkOvertimeLimit($w[7]) || checkOvertimeLimit($w[8]) || checkOvertimeLimit($w[9]) || checkOvertimeLimit($w[10]) || checkOvertimeLimit($w[11])) { $suspended = true; }
          }
          if ($totalTime > $weeklyHourlyLimit && $buildingMaintenance) { $suspended = true; }

          if ($suspended) { echo "<tr class='suspended'>"; } else { echo "<tr>"; }
          echo "<td>" . $s[1] . " " . $s[2] . "</td><td>" . strtoupper($position) . "</td>";
          echo "<td>" . $w['LocationCode'] . "</td><td style='text-align: center;'>" . $w[5] . "</td><td style='text-align: center;'>" . $w[6] . "</td><td style='text-align: center;'>" . $w[7] . "</td><td style='text-align: center;'>" . $w[8] . "</td><td style='text-align: center;'>" . $w[9] . "</td
        }
      }><td style='text-align: center;'>" . $w[10] . "</td><td style='text-align: center;'>" . $w[11] . "</td><td style='text-align: center;'>" . $totalTime . "</td></tr>";
          $newScheduleRow = false;
          break;
        }
      }
    }
  }

  foreach ($locationCodeArray as $l) {
    $w0 = $w1 = $w2 = $w3 = $w4 = $w5 = $w6 = $w7 = 0;
    echo "<tr bgcolor='#efefef'><td colspan='3' style='text-align: right; border: 1px solid #000;'>Total Hours (<b>" . $l . "</b>):</td>";
    foreach ($totalHoursArray as $t) {
      if ($t[7] == $l) {
        $w0 += ($t[0]);
        $w1 += ($t[1]);
        $w2 += ($t[2]);
        $w3 += ($t[3]);
        $w4 += ($t[4]);
        $w5 += ($t[5]);
        $w6 += ($t[6]);
      }
    }
    echo "<td style='text-align: center;'>" . $w0 . "</td><td style='text-align: center;'>" . $w1 . "</td><td style='text-align: center;'>" . $w2 . "</td><td style='text-align: center;'>" . $w3 . "</td><td style='text-align: center;'>" . $w4 . "</td><td style='text-align: center;'>" . $w5 . "</td><td style='text-align: center;'>" . $w6 . "</td><td style='text-align: center;'><div id='total_schedule_hours_" . str_replace(" ", "_", $l) . "'>";
    echo ($w0 + $w1 + $w2 + $w3 + $w4 + $w5 + $w6) . "</div></td></tr>";
  }
  echo "</tbody></table>";

  $sql = "SELECT shift_schedule_notes_id, description FROM shift_schedule_notes WHERE location_code = '" . $location . "'";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r[0] == "") { continue; }
    echo "<center><table width='600' border='0' style='border-collapse: collapse; border: 1px solid black;'><tr><td class='navtitle'>National Aviation Services Shift Schedule Notes</td></tr><tr><td>" . nl2br($r['description']) . "</td></tr></table>";
  }
}

function convertDate($d) {
  $date = explode(" ", $d);
  return date("Y-m-d", strtotime($date[0]));
}

function stripBrackets($g) {
  $g = str_replace("{", "", $g);
  return str_replace("}", "", $g);
}

function sessionCheck() {
  if (!isset($_SESSION['user'])) { header("Location: /?xapp=SESSION-TIMEOUT"); }
}

function initCheck($s) {
  $id = null;

  if (!isset($_SESSION['initpwd'])) {
    if (isset($_SESSION['user'])) { $id = $_SESSION['user']->getEmployeeID(); }
    $sql = "INSERT INTO security_log (employee_id, ip_address, action, result) VALUES ('" . $id . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $s . "', 'REDIRECT TO LOGIN')";
    execSQL($sql, true);
    header("Location: index.php"); 
  }
}

function locationCheck() {
  global $location;
  if (is_null($location)) { header("Location: http://w3.nataviation.com/contact.php"); }
}

function loadAirportOptions() {
  global $airportOptionDefault;
  global $airportCodeDefault;
  global $viewGlobalSchedule;
  global $debugMode;
  global $location;

  $currentAirportID = null;
  $tmpAirportID = null;
  $cout = "";


  if ($debugMode) { echo "<br />loadAirportOptions()<br />"; }
  if (isset($_REQUEST['l'])) { $tmpAirportID = $_REQUEST['l']; }

  if (is_null($tmpAirportID)) {
    $tmpAirportID = $_SESSION['user']->getLocationID();
    if ($debugMode) { echo "tmpAirportID assigned to user getLocationID: " . $tmpAirportID . "<br />"; }

    if ($tmpAirportID == 104) {
      $tmpAirportID = 109;
      $location = "ABQ";
    } else {
      $sql = "SELECT ArptRsrvCntrID FROM ArptRsrvCntr WHERE AirportCode = (SELECT SUBSTRING_INDEX(LocationCode, \" \", 1) From Locations WHERE LocationID = " . $tmpAirportID . ")";
      $rs = execSQL($sql);

      foreach ($rs as $r) {
        if ($r[0] == "") { continue; }
        $tmpAirportID = $r[0];
      }
    }
  }

  if ($debugMode) { echo "<br />tmpAirportID: " . $tmpAirportID . "<br />"; }

  if (securityCheck("view_global_schedule", false, true)) {
    $viewGlobalSchedule = true;
    $sql = "SELECT ArptRsrvCntrID, Name, AirportCode FROM ArptRsrvCntr ORDER BY AirportCode";
  } else {
    $sql = "SELECT ArptRsrvCntrID, Name, AirportCode FROM ArptRsrvCntr WHERE AirportCode = '" . explode(" ", $_SESSION['user']->getLocation())[0] . "';";
  }
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r['ArptRsrvCntrID'] == "") { continue; }
    if ($tmpAirportID == $r['ArptRsrvCntrID']) {
      $currentAirportID = $tmpAirportID;
      $airportCodeDefault = $r['AirportCode'];
      $cout .= "<option selected value='" . $r['ArptRsrvCntrID'] . "'>" . $r['Name'] . "</option>";
    } else {
      $cout .= "<option value='" . $r['ArptRsrvCntrID'] . "'>" . $r['Name'] . "</option>";
    }
  }

  if ($debugMode) { echo "<br />AirportCodeDefault: " . $airportCodeDefault . "<br />"; }
  if (is_null($airportOptionDefault)) { $airportOptionDefault = $currentAirportID; }
  if ($debugMode) { echo "<br />AirportCodeDefault: " . $airportCodeDefault . "<br />"; }
  return $cout;
}

function loadProjectOptions() {
  global $projectOptionDefault;
  global $viewGlobalSchedule;
  global $editGlobalSchedule;
  global $debugMode;
  global $location;

  if ($debugMode) { echo "<br />loadProjectOptions<br />"; }
  $locationArray = getDirectorLocations();
  $currentLocationID = null;
  $tmpLocationID = null;
  $cout = "";
  $ld = null;

  if ($debugMode) { echo "<br />" . var_dump($locationArray) . "<br />"; }
  if (securityCheck("view_global_schedule", false, true)) { $viewGlobalSchedule = true; }
  if (securityCheck("edit_global_schedule", false, true)) { $editGlobalSchedule = true; }

  if ($_SESSION['user']->checkDirector()) { $viewGlobalSchedule = false; }
  if ($_SESSION['user']->checkExecutive()) { $viewGlobalSchedule = true; }

  if ($viewGlobalSchedule || $_SESSION['user']->checkDirector()) {
    $sql = "SELECT LocationID, LocationCode FROM Locations WHERE ActiveFlag = 1 ORDER BY LocationCode";
  } else {
    $sql = "SELECT LocationID, LocationCode FROM Locations WHERE ActiveFlag = 1 AND SUBSTRING_INDEX(LocationCode, \" \", 1) LIKE '" . explode(" ", $_SESSION['user']->getLocation())[0] . "%';";
  }
  $rs = execSQL($sql);

  if ($debugMode) {
    echo "<br />viewGlobalSchedule: " . $viewGlobalSchedule . "<br />";
    echo "Director: " . $_SESSION['user']->checkDirector() . "<br />";
  }

  foreach ($rs as $r) {
    if ($r['LocationID'] == "") { continue; }

    if (isset($_REQUEST['l'])) { $tmpLocationID = $_REQUEST['l']; }
    if (is_null($tmpLocationID)) { $tmpLocationID = $_SESSION['user']->getLocationID(); }

    if ($tmpLocationID == $r['LocationID']) {
      $currentLocationID = $tmpLocationID;
      $cout .= "<option selected value='" . $r['LocationID'] . "'>" . explode(" ", $r['LocationCode'])[0] . "</option>";
      //if (!$viewGlobalSchedule) { break; }
    } else {
      if (explode(" ", $r['LocationCode'])[0] == $ld) { continue; }
      if ($viewGlobalSchedule || ($_SESSION['user']->checkDirector() && in_array($r['LocationID'], $locationArray))) {
        $cout .= "<option value='" . $r['LocationID'] . "'>" . explode(" ", $r['LocationCode'])[0] . "</option>";
      }
      $ld = explode(" ", $r['LocationCode'])[0];
    }
  }
  if (is_null($projectOptionDefault)) { $projectOptionDefault = $currentLocationID; }
  return $cout;
}

function openWorkFlowCheck() {
  global $debugMode;

  if ($debugMode) { return false; }
  $sql = "SELECT wf_process_id FROM wf_processes WHERE employee_id = '" . $_SESSION['user']->getEmployeeID() . "' AND closed_date IS NULL";
  $rs = execSQL($sql);
  if (sizeof($rs) > 1) { return true; }

  if (securityCheck("edit_hr_messages", false, true)) {
    $sql = "SELECT wf_process_id FROM wf_processes WHERE hr_action = 1 AND closed_date IS NOT NULL";
    $rs = execSQL($sql);
    if (sizeof($rs) > 1) { return true; }
  }
  return false;
}

function scheduleOpenWorkFlowCheck($location) {
  global $location;
  global $scheduleCreateMode;
  global $scheduleOpenWorkFlow;

  $sql = "SELECT wf_process_id FROM wf_processes WHERE wf_request_id IN (SELECT wf_request_id FROM wf_requests WHERE location = '" . $location . "') AND closed_date IS NULL";
  $rs = execSQL($sql);
  if (sizeof($rs) > 1) { $scheduleOpenWorkFlow = true; $scheduleCreateMode = false; }
}

function managerCheck() {
  global $viewGlobalSchedule;
  global $debugMode;
  global $location;
  global $manager;

  $employeeID = $_SESSION['user']->getEmployeeID();
  $location = $_SESSION['user']->getLocation();

  if ($debugMode) { echo "<br />managerCheck()<br />"; }
  $sql = "SELECT DISTINCT l.Manager AS LManager, tl.Manager AS TLManager FROM Locations l, TopLevelLocation tl WHERE l.ActiveFlag = 1 AND l.LocationCode LIKE '" . explode(" ", $_SESSION['user']->getLocation())[0] . "%' AND l.TopLevelID = tl.TopLevelID;";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r['TLManager'] == "") { continue; }
    if ($employeeID == $r['LManager'] || $employeeID == $r['TLManager'] || $viewGlobalSchedule) {
      $manager = true;
      break;
    }
  }
  if ($debugMode) { echo "Manager: " . $manager . "<br />"; }
}

function init() {
  global $dbconn;
  global $debugMode;
  global $domainRoot;
  $dbconn = new mysqli("localhost", "nas_web", "2018N@tion@l!!", "nas");
}

function clean() {
  global $dbconn;
  $dbconn->close();
}

function loadUser($employeeID, $password, $impersonate = NULL) {
  $u = new User($employeeID, $password, $impersonate);
  if ($u->getEmployeeID() == null) { return null; }
  return $u;
}

function getGUID() {
  if (function_exists('com_create_guid')) {
      return com_create_guid();
  }
  else {
    mt_srand((double)microtime()*10000);
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = chr(45);
    $uuid = chr(123)
      .substr($charid, 0, 8).$hyphen
      .substr($charid, 8, 4).$hyphen
      .substr($charid,12, 4).$hyphen
      .substr($charid,16, 4).$hyphen
      .substr($charid,20,12)
      .chr(125);
    return $uuid;
  }
}

function convertIssue($v) {
  if ($v == "1") { return "No"; }
  return "Yes";
}

function convertRace($r) {
  $r = strtoupper($r);
  if (strPos($r, "WHITE") >= 0) { return "1"; }
  if (strPos($r, "BLACK") >= 0) { return "2"; }
  if (strPos($r, "HISPANIC") >= 0) { return "3"; }
  if (strPos($r, "ASIAN") >= 0) { return "4"; }
  if (strPos($r, "ALASKA") >= 0) { return "5"; }
  if (strPos($r, "HAWAIIAN") >= 0) { return "6"; }
  return "7";
}

function convertGenderID($v) {
  if ($v == "1") { return "Male"; }
  return "Female";
}

function convertGender($s) {
  if (strtoupper($s) == "MALE") { return "1"; }
  return "2";
}

function convertBinary($v) {
  if ($v == "0") { return "No"; }
  return "Yes";
}

function convertStatus($s) {
  if (strtoupper($s) == "ACTIVE") { return "1"; }
  return "0";
}

function convertWorkStatusID($id) {
  if ($id == "2") { return "Full Time"; }
  return "Part Time";
}

function convertWorkStatus($s) {
  if (strtoupper(str_replace("-", "", $s)) == "FULL TIME") { return "2"; }
  return "1";
}

function convertPayID($id) {
  if ($id == "1") { return "Hourly"; }
  return "Salary";
}

function convertPayType($p) {
  if (strtoupper($p) == "HOURLY") { return "2"; }
  return "1";
}

function loadCensusList() {
  if (isset($_REQUEST['view_delta'])) {
    $sql = "SELECT census_sequence_id FROM census_sequence WHERE DATE(effective_date) = '" . $_REQUEST['view_delta_date'] . "'";
    $rs = execSQL($sql);
    foreach ($rs as $r) {
      if ($r[0] == "") { continue; }
      $census_sequence_id = $r[0];
    }

    $sql = "SELECT census_delta_id, employee_id, LPAD(ssn_nid,9,0) as ssn_nid, employee_name, business_title, location_description, trinet_hire_date FROM census_delta WHERE census_sequence_id = " . $census_sequence_id . " AND modified = " . $_REQUEST['view_delta'];
  } else {
    $sql = "SELECT census_id, employee_id, LPAD(ssn_nid,9,0) as ssn_nid, employee_name, business_title, location_description, trinet_hire_date FROM census WHERE status = 'Active' ORDER BY employee_name";
  }

  $rs = execSQL($sql);
  foreach ($rs as $r) {
    if ($r[0] == "") { continue; }
     echo "<tr><td>" . $r[0] . "</td><td>" . $r[1] . "</td><td>" . $r[2] . "</td><td>" . strtoupper($r[3]) . "</td><td>" . strtoupper($r[4]) . "</td><td>" . $r[5] . "</td><td>" . $r[6] . "</td></tr>";
  }
}

function loadRawCensusList() {
  $sql = "SELECT census_id, employee_id, record_num, ssn_nid, employee_name, first_name, middle_name, last_name, business_title, department_id, department, location_code, location_description, work_address_1, work_address_2, work_address_3, work_address_4, work_city, work_state, work_zip, work_county, work_country, sex, race, home_address_1, home_address_2, home_address_3, home_address_4, home_city, home_state, home_zip, home_county, home_country, status, full_part, reg_temp, variable_hours, pay_group, pay_frequency, pay_type, std_hours_week, eeo_class, eeo_code, eeo_description, nid_country, nid_description, marital_status, home_phone, cell_phone, work_phone, work_phone_ext, age, dob, trinet_hire_date, rehire_date, term_date, term_reason, last_date, company_hire_date, service_date, workers_comp_code, current_annual_rt, current_monthly_rt, current_hourly_rt, last_increase_date, increase_amount, prev_annual_rt, job_code_description, action, eff_date, action_date, action_reason, entry_date, own_5_pct, vis_pmt_type, expire_date, officer_cd, bonus, bonus_date, bonus_reason, ytd_bonus, addl_pay, shift_rate, shift_fctr, cobra_action, as_of_date, alter_employee_id, group1, group2, supervisor_id, supervisor_name, flsa_status, i9, military_status, email_address FROM census";
  $rs = execSQL($sql);
  foreach ($rs as $r) {
    if ($r['census_id'] == "") { continue; }
    echo "<tr><td>" . $r['census_id'] . "</td><td>" . $r['employee_id'] . "</td><td>" . $r['record_num'] . "</td><td>" . $r['ssn_nid'] . "</td><td>" . $r['employee_name'] . "</td><td>" . $r['first_name'] . "</td><td>" . $r['middle_name'] . "</td><td>" . $r['last_name'] . "</td><td>" . $r['business_title'] . "</td><td>" . $r['department_id'] . "</td><td>" . $r['department'] . "</td><td>" . $r['location_code'] . "</td><td>" . $r['location_description'] . "</td><td>" . $r['work_address_1'] . "</td><td>" . $r['work_address_2'] . "</td><td>" . $r['work_address_3'] . "</td><td>" . $r['work_address_4'] . "</td><td>" . $r['work_city'] . "</td><td>" . $r['work_state'] . "</td><td>" . $r['work_zip'] . "</td><td>" . $r['work_county'] . "</td><td>" . $r['work_country'] . "</td><td>" . $r['sex'] . "</td><td>" . $r['race'] . "</td><td>" . $r['home_address_1'] . "</td><td>" . $r['home_address_2'] . "</td><td>" . $r['home_address_3'] . "</td><td>" . $r['home_address_4'] . "</td><td>" . $r['home_city'] . "</td><td>" . $r['home_state'] . "</td><td>" . $r['home_zip'] . "</td><td>" . $r['home_county'] . "</td><td>" . $r['home_country'] . "</td><td>" . $r['status'] . "</td><td>" . $r['full_part'] . "</td><td>" . $r['reg_temp'] . "</td><td>" . $r['variable_hours'] . "</td><td>" . $r['pay_group'] . "</td><td>" . $r['pay_frequency'] . "</td><td>" . $r['pay_type'] . "</td><td>" . $r['std_hours_week'] . "</td><td>" . $r['eeo_class'] . "</td><td>" . $r['eeo_code'] . "</td><td>" . $r['eeo_description'] . "</td><td>" . $r['nid_country'] . "</td><td>" . $r['nid_description'] . "</td><td>" . $r['marital_status'] . "</td><td>" . $r['home_phone'] . "</td><td>" . $r['cell_phone'] . "</td><td>" . $r['work_phone'] . "</td><td>" . $r['work_phone_ext'] . "</td><td>" . $r['age'] . "</td><td>" . $r['dob'] . "</td><td>" . $r['trinet_hire_date'] . "</td><td>" . $r['rehire_date'] . "</td><td>" . $r['term_date'] . "</td><td>" . $r['term_reason'] . "</td><td>" . $r['last_date'] . "</td><td>" . $r['company_hire_date'] . "</td><td>" . $r['service_date'] . "</td><td>" . $r['workers_comp_code'] . "</td><td>" . $r['current_annual_rt'] . "</td><td>" . $r['current_monthly_rt'] . "</td><td>" . $r['current_hourly_rt'] . "</td><td>" . $r['last_increase_date'] . "</td><td>" . $r['increase_amount'] . "</td><td>" . $r['prev_annual_rt'] . "</td><td>" . $r['job_code_description'] . "</td><td>" . $r['action'] . "</td><td>" . $r['eff_date'] . "</td><td>" . $r['action_date'] . "</td><td>" . $r['action_reason'] . "</td><td>" . $r['entry_date'] . "</td><td>" . $r['own_5_pct'] . "</td><td>" . $r['vis_pmt_type'] . "</td><td>" . $r['expire_date'] . "</td><td>" . $r['officer_cd'] . "</td><td>" . $r['bonus'] . "</td><td>" . $r['bonus_date'] . "</td><td>" . $r['bonus_reason'] . "</td><td>" . $r['ytd_bonus'] . "</td><td>" . $r['addl_pay'] . "</td><td>" . $r['shift_rate'] . "</td><td>" . $r['shift_fctr'] . "</td><td>" . $r['cobra_action'] . "</td><td>" . $r['as_of_date'] . "</td><td>" . $r['alter_employee_id'] . "</td><td>" . $r['group1'] . "</td><td>" . $r['group2'] . "</td><td>" . $r['supervisor_id'] . "</td><td>" . $r['supervisor_name'] . "</td><td>" . $r['flsa_status'] . "</td><td>" . $r['i9'] . "</td><td>" . $r['military_status'] . "</td><td>" . $r['email_address'] . "</td></tr>";
  }
}

function loadCustomersList() {
  $sql = "SELECT * FROM ns_customers ORDER BY ns_customer_id;";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r['ns_customer_id'] == "") { continue; }
    echo "<tr><td>" . $r['ns_customer_id'] . "</td><td>" . $r['netsuite_id'] . "</td><td>" . $r['name'] . "</td></tr>";
  }
}

function loadPermissionMapping() {
  $sql = "SELECT permissions_id, permission, description FROM permissions ORDER BY permission";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r[0] == "") { continue; }
    echo "<tr><td>" . $r['permissions_id'] . "</td><td>" . $r['permission'] . "</td><td>" . $r['description'] . "</td></tr>";
  }
}

function loadPositionMapping() {
  $sql = "SELECT p.PositionID, p.Description, (SELECT s.ShirtStyle FROM ShirtStyles s WHERE s.ShirtStyleID = p.ShirtStyleID) as ShirtStyle, (SELECT COUNT(e.EmpStatusID) AS c FROM EmploymentStatus e WHERE e.Active = 1 AND e.PositionID = p.PositionID) AS EmployeeCount, (SELECT COUNT(pm.position_mapping_id) AS c FROM position_mapping pm WHERE pm.position_id = p.PositionID) AS CensusCount FROM Positions p WHERE p.ActiveFlag = 1;";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r[0] == "") { continue; }
    echo "<tr><td>" . $r['PositionID'] . "</td><td>" . $r['Description'] . "</td><td>" . $r['ShirtStyle'] . "</td><td>" . $r['EmployeeCount'] . "</td><td>" . $r['CensusCount'] . "</td></tr>";
  }
}

function loadFutureScheduleData($l, $d) {
  global $operationsEmployeeID;
  global $debugMode;

  $override = false;
  $rowDiff = false;

  if ($operationsEmployeeID == $_SESSION['user']->getEmployeeID()) { return; }
  $sql = "SELECT * FROM shift_schedule_future WHERE start_date = '" . $d . "' AND active = 1 AND location_id IN (SELECT LocationID FROM Locations WHERE SUBSTRING_INDEX(LocationCode, \" \", 1) LIKE '" . $l . "%')";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r[0] == "") { continue; }
    if ($override) { break; }
    $rowDiff = false;

    if (checkOverTimeLimit($r['sat']) || 
        checkOverTimeLimit($r['sun']) || 
        checkOverTimeLimit($r['mon']) || 
        checkOverTimeLimit($r['tue']) || 
        checkOverTimeLimit($r['wed']) || 
        checkOverTimeLimit($r['thu']) || 
        checkOverTimeLimit($r['fri']) ||
        checkWeeklyHours($r['sat'], $r['sun'], $r['mon'], $r['tue'], $r['wed'], $r['thu'], $r['fri'])) { $rowDiff = true; }
    if ($rowDiff) { generateScheduleDelta($r[0], 0, $l, $d); $override = true; }
  }
}

function loadScheduleData($requestDate, $staffArray, $scheduleArray, $rowIndex, $initialLoad, $l) {
  global $operationsEmployeeID;
  global $debugMode;

  $newScheduleRow = true;
  $override = false;
  $rowDiff = false;

  if ($_SESSION['user']->getEmployeeID() != $operationsEmployeeID) {
    foreach ($staffArray as $s) { generateScheduleRow($rowIndex++, "shift_schedule_analysis"); }
    $sql = "SELECT * FROM shift_schedule_analysis WHERE active = 1 AND location_id IN (SELECT LocationID FROM Locations WHERE SUBSTRING_INDEX(LocationCode, \" \", 1) LIKE '" . $l . "%')";
    $rs = execSQL($sql);

    foreach ($rs as $r) {
      if ($r[0] == "") { continue; }
      if ($override) { break; }
      $rowDiff = false;

      if ($initialLoad) {
        if (checkOverTimeLimit($r['sat']) || 
            checkOverTimeLimit($r['sun']) || 
            checkOverTimeLimit($r['mon']) || 
            checkOverTimeLimit($r['tue']) || 
            checkOverTimeLimit($r['wed']) || 
            checkOverTimeLimit($r['thu']) || 
            checkOverTimeLimit($r['fri']) ||
            checkWeeklyHours($r['sat'], $r['sun'], $r['mon'], $r['tue'], $r['wed'], $r['thu'], $r['fri'])) { $rowDiff = true; }
        if ($rowDiff) { generateScheduleDelta($r[0], 0, $l, $requestDate); $override = true; }
      } else {
        foreach ($scheduleArray as $w) {
          if ($r[4] == $w[4]) {
            if (checkOverTimeLimit($r['sat']) || 
                checkOverTimeLimit($r['sun']) || 
                checkOverTimeLimit($r['mon']) || 
                checkOverTimeLimit($r['tue']) || 
                checkOverTimeLimit($r['wed']) || 
                checkOverTimeLimit($r['thu']) || 
                checkOverTimeLimit($r['fri']) ||
                checkWeeklyHours($r['sat'], $r['sun'], $r['mon'], $r['tue'], $r['wed'], $r['thu'], $r['fri'])) { $rowDiff = true; }
            if ($rowDiff) { generateScheduleDelta($r[0], $w[0], $l, $requestDate); $override = true; }
            $newScheduleRow = false;
            break;
          }
        }
      }
    }
  }

  $rowIndex = 0;
  if ($override) {
    foreach ($staffArray as $s) { generateScheduleRow($rowIndex++, "shift_schedule_staging"); }
    if ($initialLoad) { $rowIndex = 0; foreach ($staffArray as $s) { generateScheduleRow($rowIndex++, "shift_schedule"); }}
  } else {
    $sql = "UPDATE shift_schedule SET active = 0, end_date = NOW() WHERE location_id IN (SELECT LocationID FROM Locations WHERE SUBSTRING_INDEX(LocationCode, \" \", 1) LIKE '" . $l . "%')";
    execSQL($sql, true);
    foreach ($staffArray as $s) { generateScheduleRow($rowIndex++, "shift_schedule"); }
  }

  $sql = "DELETE FROM shift_schedule_analysis WHERE SUBSTRING_INDEX(LocationCode, \" \", 1) LIKE '" . $l . "%'";
  execSQL($sql,true);
}

function loadStationLog() {
  global $manager;

  $message = null;
  if ($_SESSION['user']->getPositionID() == 25) { $manager = true; }
  $sql = "SELECT message FROM station_log WHERE location = '" . $_SESSION['user']->getLocation() . "'";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r[0] == "") { continue; }
    $message = $r[0];
  }

  if ($manager) {
    echo "<input type='hidden' id='l' name='l' value='" . $_SESSION['user']->getLocation() . "' /><textarea id='message' name='message' cols='150' rows='4'>" . $message . "</textarea><br /><input type='button' style='float:right;height:30px;width:150px;display:inline;margin:0 auto;' name='station_log_button' id='station_log_button' value='UPDATE' />";
  } else {
    echo $message;
  }
}

function loadMessageHeader($hr) {
  if ($hr) {
    echo "<thead><tr><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Request ID</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Lock Owner</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Required Action</font></th><th style='text-align: center;background-color: #cce5ff;'><font size='3' face='Arial'>Locked</font></th><th style='text-align: center;background-color: #cce5ff;'><font size='3' face='Arial'>Date Created</font></th><th style='text-align: center;background-color: #cce5ff;'><font size='3' face='Arial'>Date Started</font></th><th style='text-align: center;background-color: #cce5ff;'><font size='3' face='Arial'>Date Closed</font></th><th style='text-align: center;background-color: #cce5ff;'><font size='3' face='Arial'>Completed</font></th></tr></thead>";
  } else {
    echo "<thead><tr><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Request ID</font></th><th style='background-color: #cce5ff;'><font size='3' face='Arial'>Message</font></th><th style='text-align: center;background-color: #cce5ff;'><font size='3' face='Arial'>Date Created</font></th><th style='text-align: center;background-color: #cce5ff;'><font size='3' face='Arial'>Date Closed</font></th><th style='text-align: center;background-color: #cce5ff;'><font size='3' face='Arial'>Completed</font></th></tr></thead>";
  }
}

function displayLock($v) {
  if ($v == 1) { return "<center><span class='glyphicon glyphicon-lock'></span></center>"; }
  return null;
}

function displayApproved($d, $v) {
  if ($v == 1) { return "<center><span class='glyphicon glyphicon-thumbs-up'></span><center>"; }
  if ($d != "") { return "<center><span class='glyphicon glyphicon-thumbs-down'></span><center>"; }
  return null;
}

function loadMessages() {
  global $debugMode;

  $viewFilter = false;
  $idArray = array();
  $hr = false;

  if ($debugMode) { echo "<br />loadMessages<br />"; }
  if (securityCheck("view_hr_messages", false, true)) { $hr = true; }
  if (isset($_REQUEST['message_filter'])) { $viewFilter = true; }

  if ($hr) {
    loadMessageHeader(true);
    $sql = "SELECT wf_request_id, (SELECT CONCAT (FirstName, \" \", LastName) FROM Employees WHERE EmployeeID = employee_id) AS employee_id, required_action, locked, created_date, started_date, closed_date, approved FROM wf_processes WHERE (hr_action = 1 OR employee_id = '" . $_SESSION['user']->getEmployeeID() . "')";
    if ($viewFilter) { $sql .= " AND closed_date IS NULL"; }
    $rs = execSQL($sql);

    foreach ($rs as $r) {
      if ($r[0] == "") { continue; }
      array_push ($idArray, $r[0]);
      echo "<tr><td>" . $r['wf_request_id'] . "</td><td>" . $r['employee_id'] . "</td><td>" . $r['required_action'] . "</td><td>" . displayLock($r['locked']) . "</td><td style='text-align: center;'>" . $r['created_date'] . "</td><td style='text-align: center;'>" . displayDate($r['started_date']) . "</td><td style='text-align: center;'>" . displayDate($r['closed_date']) . "</td><td style='text-align: center;'>" . displayApproved($r['closed_date'], $r['approved']) . "</td></tr>";
    }
  } else {
    loadMessageHeader(false);
    $sql = "SELECT wf_request_id, required_action, created_date, started_date, closed_date, approved FROM wf_processes WHERE employee_id = '" . $_SESSION['user']->getEmployeeID() . "'";
    if ($viewFilter) { $sql .= " AND closed_date IS NULL"; }
    $rs = execSQL($sql);

    foreach ($rs as $r) {
      if ($r[0] == "") { continue; }
      array_push ($idArray, $r[0]);
      echo "<tr><td>" . $r['wf_request_id'] . "</td><td>" . $r['required_action'] . "</td><td style='text-align: center;'>" . $r['created_date'] . "</td><td style='text-align: center;'>" . explode(" ",$r['closed_date'])[0] . "</td><td style='text-align: center;'>" . displayApproved($r['closed_date'], $r['approved']) . "</td></tr>";
    }
  }

  foreach ($idArray as $i) {
    $sql = "INSERT INTO wf_views (wf_request_id, employee_id, view_date) VALUES (" . $i . ", '" . $_SESSION['user']->getEmployeeID() . "', NOW())";
    execSQL($sql, true);
  }
}

function displayDate($d) {
  if (strPos($d, "00:00:00:") >= 0) { return explode(" ", $d)[0]; }
  return $d;
}

function loadLocationMapping() {
  global $locationsArray;

  $sql = "SELECT a.ArptRsrvCntrID, a.Name, a.AirportCode, tl.TopLevelID, tl.Manager AS 'TLManager', e.LastName AS 'TLLastName', e.FirstName AS 'TLFirstName', s.StateID, s.StateAbbrv, l.LocationID, l.Manager AS 'LManager', (SELECT CONCAT(e.FirstName, \" \", e.LastName) AS LManagerName FROM Employees e WHERE EmployeeID = l.Manager) AS 'LManagerName', l.Director AS 'Director', (SELECT CONCAT(e.FirstName, \" \", e.LastName) AS DirectorName FROM Employees e WHERE EmployeeID = l.Director) AS 'DirectorName', l.LocationCode, l.DTLastModified, (SELECT name FROM ns_locations WHERE ns_location_id = (SELECT ns_location_id FROM location_mapping WHERE LocationID = l.LocationID LIMIT 1)) AS 'NSLocationCode', l.Burden FROM ArptRsrvCntr a, TopLevelLocation tl, States s, Locations l, Employees e WHERE a.ArptRsrvCntrID = tl.ArptRsrvCntrID AND tl.TopLevelID = l.TopLevelID AND s.StateID = tl.StateID AND tl.Manager = e.EmployeeID;";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r[0] == "") { continue; }
    array_push($locationsArray, $r);
    echo "<tr><td>" . $r['Name'] . "</td><td>" . $r['AirportCode'] . "</td><td>" . $r['DirectorName'] . "</td><td>" . $r['TLFirstName'] . " " . $r['TLLastName'] . "</td><td>" . $r['LManagerName'] . "</td><td>" . $r['StateAbbrv'] . "</td><td>" . $r['LocationCode'] . "</td><td>" . $r['NSLocationCode'] . "</td><td>" . $r['Burden'] . "</td><td>" . $r['DTLastModified'] . "</td></tr>";
  }
}

function loadRulesList() {
  $sql = "SELECT r.rules_engine_id, CONCAT(e.FirstName, \" \", e.LastName) AS Employee, r.rule_name, r.rule_description, r.created_date FROM rules_engine r, Employees e WHERE r.active = 1 AND r.employee_id = e.EmployeeID";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r['rules_engine_id'] == "") { continue; }
    echo "<tr><td>" . $r['rules_engine_id'] . "</td><td>" . $r['Employee'] . "</td><td>" . $r['rule_name'] . "</td><td>" . $r['rule_description'] . "</td><td>" . $r['created_date'] . "</td></tr>";
  }
}

function loadIssuesList() {
  $sql = "SELECT i.issues_id, CONCAT(e.FirstName, \" \", e.LastName) AS Employee, i.description, i.created_date, i.active FROM issues i, Employees e WHERE i.employee_id = e.EmployeeID ORDER BY i.created_date DESC";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r['issues_id'] == "") { continue; }
    echo "<tr><td>" . $r['issues_id'] . "</td><td>" . $r['Employee'] . "</td><td>" . $r['description'] . "</td><td>" . $r['created_date'] . "</td><td>" . convertIssue($r['active']) . "</td></tr>";
  }
}

function loadAircraftTypes() {
  $sql = "SELECT * FROM aircraft ORDER BY aircraft_type";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r['aircraft_id'] == "") { continue; }
    echo "<tr><td>" . $r['aircraft_id'] . "</td><td>" . $r['aircraft_type'] . "</td></tr>";
  }
}

function loadServices() {
  $sql = "SELECT * FROM services ORDER BY service";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r['service_id'] == "") { continue; }
    echo "<tr><td>" . $r['service_id'] . "</td><td>" . $r['service'] . "</td><td>" . $r['description'] . "</td></tr>";
  }
}

function loadLocationsList() {
  $sql = "SELECT * FROM ns_locations ORDER BY ns_location_id;";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r['ns_location_id'] == "") { continue; }
    echo "<tr><td>" . $r['ns_location_id'] . "</td><td>" . $r['netsuite_id'] . "</td><td>" . $r['name'] . "</td></tr>";
  }
}

function loadItemsList() {
  $sql = "SELECT * FROM ns_items WHERE active = 1 ORDER BY ns_item_id;";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r['ns_item_id'] == "") { continue; }
    echo "<tr><td>" . $r['ns_item_id'] . "</td><td>" . $r['netsuite_id'] . "</td><td>" . $r['name'] . "</td><td>" . $r['price'] . "</td><td>" . $r['location'] . "</td><td>" . $r['start_date'] . "</td></tr>";
  }
}

function loadEmployees($op) {
  $sql = "SELECT  e.* FROM nas.Employees e, census c where LPAD(c.ssn_nid,9,0) = REPLACE(e.SSN,'-','') AND c.status " . $op . " 'Active'";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r[0] == "") { continue; }
    echo "<tr><td>" . $r['EmployeeID'] . "</td><td>" . $r['LastName'] . "</td><td>" . $r['FirstName'] . "</td><td>" . $r['MiddleName'] . "</td><td>" . explode(" ", $r['DOB'])[0] . "</td><td>" . $r['DTLastModified'] . "</td></tr>";
  }
}

function loadEmployeeApplications() {
  $sql = "SELECT employee_application_id, last_name, first_name, position, airport, home_email, home_phone FROM employee_application";
  $rs = execSQL($sql);

  foreach ($rs as $r) {
    if ($r[0] == "") { continue; }
    echo "<tr><td>" . $r['employee_application_id'] . "</td><td>" . $r['last_name'] . "</td><td>" . $r['first_name'] . "</td><td>" . $r['position'] . "</td><td>" . $r['airport'] . "</td><td>" . $r['home_email'] . "</td><td>" . $r['home_phone'] . "</td></tr>";
  }
}

function checkDirector($positionID) {
  global $director;

  $sql = "SELECT PositionID From Positions WHERE Description LIKE '%Director%'";
  $rs = execSQL($sql);

  foreach($rs as $r) {
    if ($r[0] == "") { continue; }
    if ($r[0] == $positionID) { $director = true; break; }
  }
  return $director;
}

function getDirectorLocations() {
  $locationArray = array();

  $sql = "SELECT LocationID FROM Locations WHERE ActiveFlag = 1 AND Director = '" . $_SESSION['user']->getEmployeeID() . "'";
  $rs = execSQL($sql);
  foreach($rs as $r) {
    if ($r[0] == "") { continue; }
    array_push($locationArray, $r[0]);
  }
  return $locationArray;
}

class UserShift {
  private $sTime = null;
  private $wTime = null;
  private $ssn = null;

  public function __construct($w, $s, $ssn) {
    $this->sTime = $s;
    $this->wTime = $w;
    $this->ssn = $ssn;
  }

  public function setWTime($h) { $this->wTime += $h; }
  public function getSTime() { return $this->sTime; }
  public function getWTime() { return $this->wTime; }
  public function getSSN() { return $this->ssn; }
}

class User {
  private $impersonate = false;
  private $supervisorID = null;
  private $employeeID = null;
  private $positionID = null;
  private $locationID = null;
  private $executive = false;
  private $director = false;
  private $lastLogin = null;
  private $firstName = null;
  private $shiftDate = null;
  private $censusID = null;
  private $password = null;
  private $lastName = null;
  private $location = null;
  private $payType = null;
  private $cache = "v1";
  private $ssn = null;

  private function initialize() {
    if ($this->ssn == "") { return false; }

    if ($this->impersonate) {
        $sql = "SELECT c.census_id, c.last_name, c.first_name, c.pay_type, c.supervisor_id, e.EmployeeID FROM census c, Employees e WHERE LPAD(c.ssn_nid,9,0) = REPLACE(e.SSN,'-','') AND c.ssn_nid = '" . str_replace('-', '', $this->ssn)  . "'";
    } else {
      $sql = "SELECT c.census_id, c.last_name, c.first_name, c.pay_type, c.supervisor_id, e.EmployeeID, u.Password, u.LastAccess FROM census c, Employees e, Users u WHERE LPAD(c.ssn_nid,9,0) = REPLACE(e.SSN,'-','') AND u.EmployeeID = e.EmployeeID AND u.ActiveFlag = 1 AND c.ssn_nid = '" . str_replace('-', '', $this->ssn)  . "'";
    }

    $rs = execSQL($sql);
    if (sizeof($rs) == 1) {

      $sql = "SELECT e.EmployeeID FROM census c, Employees e, Users u WHERE LPAD(c.ssn_nid,9,0) = REPLACE(e.SSN,'-','') AND u.EmployeeID = e.EmployeeID AND u.ActiveFlag = 0 AND c.ssn_nid = '" . str_replace('-', '', $this->ssn)  . "'";
      $rs = execSQL($sql);

      if (sizeof($rs) == 1) {
        $_SESSION['initpwd'] = true; 
        return false;
      } else {
        foreach ($rs as $r) {
          if ($r[0] == "") { continue; }
          securityCheck("suspension", false, NULL, $r['EmployeeID']);
        }
      }
    }

    foreach($rs as $r) {
      if ($r['EmployeeID'] == "") { continue; }

      if ($this->impersonate) {
        $this->password = "IMPERSONATED IDENTITY";
      } else {
        if (!password_verify($this->password, $r['Password'])) { $_SESSION['passwd'] = "FAIL"; return false; }
        $this->lastLogin =   $r['LastAccess'];
      }
      
      $this->censusID =    $r['census_id'];
      $this->employeeID =  $r['EmployeeID'];
      $this->supervisorID = $r['supervisor_id'];
      $this->lastName =    $r['last_name'];
      $this->firstName =   $r['first_name'];
      $this->payType =     $r['pay_type'];
    }

    if ($this->supervisorID != "") {
      $sql = "SELECT e.EmployeeID FROM Employees e, census c WHERE CAST(c.employee_id AS SIGNED) = " . $this->supervisorID . " AND LPAD(c.ssn_nid,9,0) = REPLACE(e.SSN,'-','')";
      $rs = execSQL($sql);

      foreach ($rs as $r) {
        if ($r[0] == "") { continue; }
        $this->supervisorID = $r[0];
      }
    }

    $sql = "SELECT es.PositionID FROM EmploymentStatus es, Employments e WHERE e.EmployeeID = '" . $this->employeeID . "' AND es.active = 1 AND es.EmploymentID = e.EmploymentID;";
    $rs = execSQL($sql);

    foreach($rs as $r) {
      if ($r['PositionID'] == "") { continue; }
      $this->positionID = $r['PositionID'];
      if ($this->positionID == "70" || $this->positionID == "35" || $this->positionID == "46") { $this->executive = true; }
    }

    if (checkDirector($this->positionID)) {
      $this->locationID = "104";
      $this->location = "Office";
      $this->director = true;
    } else {
      $sql = "SELECT l.LocationID, c.location_description FROM Locations l, census c WHERE l.ActiveFlag = 1 AND SUBSTRING_INDEX(l.LocationCODE, \" \", 1) = SUBSTRING_INDEX(c.location_description, \" \", 1) AND c.ssn_nid = '" . str_replace('-', '', $this->ssn)  . "' LIMIT 1";
      $rs = execSQL($sql);

      foreach ($rs as $r) {
        if ($r['LocationID'] == "") { continue; }
        $this->locationID = $r['LocationID'];
        $this->location = explode(" ", $r['location_description'])[0];
        break;
      }
    }

    $sql = "SELECT shift_clock_id, shift_date FROM shift_clock WHERE ssn_nid = '" . $this->ssn . "' AND reported = 0";
    $rs = execSQL($sql);

    if (sizeof($rs) ==  1) {
      $date = new DateTime();
      $date->add(DateInterval::createFromDateString('yesterday'));
      $y = strtolower(substr($date->format("l"),0,3));
      $z = null;

      $sql = "SELECT shift_schedule_id, start_date, " . $y . " FROM shift_schedule WHERE ssn_nid = '" . $this->ssn . "' AND active = 1";
      $rs1 = execSQL($sql);

      foreach ($rs1 as $r1) {
        if ($r1[0] == "") { continue; }

        $sd = date('Y-m-d', strtotime($r1['start_date']));
        if (($scheduleDate == $currentDate) && ($sd == $currentDate)) {
          $sql = "SELECT shift_schedule_id, " . $y . " FROM shift_schedule WHERE ssn_nid = '" . $this->ssn . "' AND Date(end_date) = '" . $sd . "' AND active = 0";
          $rs2 = execSQL($sql);
          foreach ($rs2 as $r2) {
            if ($r2[0] == "") { continue; }
            $z = $r2[$y];
          }
        } else {
          $z = $r1[$y];
        }

        $s0 = intval(explode(" - ", $z)[0]);
        $s1 = intval(explode(" - ", $z)[1]);
        $t = intval(Date("Hi"));
        if (($s1 < $s0) && ($s1 > $t)) { $this->shiftDate = "(CURDATE()- 1)"; } else { $this->shiftDate = "CURDATE()"; }
      }

    } else {
      foreach ($rs as $r) {
        if ($r[0] == "") { continue; }
        $this->shiftDate = "'" . $r['shift_date'] . "'";
      }
    }

    if ($this->supervisorID == "") {
      $sql = "SELECT Director FROM Locations WHERE LocationCode LIKE '" . $this->location . "%'";
      $rs = execSQL($sql);

      foreach ($rs as $r) {
        if ($r[0] == "") { continue; }
        $this->supervisorID = $r['Director'];
      }
    }
  }

  public function __construct($ssn, $password, $impersonate = NULL) {
    $this->ssn = $ssn;
    $this->password = $password;
    if ($impersonate) { $this->impersonate = true; }
    if (!$this->initialize()) { return false; }
  }

  public function getSupervisorID() { return $this->supervisorID; }
  public function setLocationID($id) { $this->locationID = $id; }
  public function getImpersonation() { return $this->password; }
  public function getPositionID() { return $this->positionID; }
  public function getEmployeeID() { return $this->employeeID; }
  public function getLocationID() { return $this->locationID; }
  public function checkExecutive() { return $this->executive; }
  public function checkDirector() { return $this->director; }
  public function getFirstName() { return $this->firstName; }
  public function getLastLogin() { return $this->lastLogin; }
  public function getShiftDate() { return $this->shiftDate; }
  public function getCensusID() { return $this->censusID; }
  public function getLastName() { return $this->lastName; }
  public function setLocation($l) { $this->location = $l; }
  public function getLocation() { return $this->location; }
  public function getPayType() { return $this->payType; }
  public function getSSN() { return $this->ssn; }
}

//##########################
//##########################
init();
//##########################
//##########################

?>