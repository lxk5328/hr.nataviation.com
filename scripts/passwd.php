<?php $loginScreen = false; ?>
<?php require_once("e.php"); ?>
<?php

$q = array();
array_push($q, "What is your pet's name?");
array_push($q, "What color was your first car?");
array_push($q, "What was your favorite sports team when you were in high school?");
array_push($q, "What is your favorite ice cream flavor?");
array_push($q, "What was the name of the city where you were born?");
array_push($q, "What city did you live in when you were 10 years old?");
array_push($q, "What is your favorite aircraft?");
array_push($q, "What is your favorite color?");
array_push($q, "Who is your favorite actor, musician, or artist?");
array_push($q, "What is the name of your first school?");
array_push($q, "What was your favorite place to visit as a child?");
array_push($q, "Which is your favorite web browser?");


if (isset($_REQUEST['xargs']) && $_REQUEST['xargs'] == "NAS2018") {
	if ($_REQUEST['action'] == "PASSWORD-RESET") {
		$n = rand(1, 3);
		$cout = "";
		$x = 1;

		$sql = "SELECT q FROM passwd_reset WHERE UserID = (SELECT UserID FROM Users WHERE EmployeeID = (SELECT EmployeeID FROM Employees WHERE SSN = '" . formatSSN(formatQuotes($_REQUEST['eid'])) . "'))";
		$rs = execSQL($sql);

		foreach($rs as $r) {
			if ($r[0] == "") { continue; }
			if ($x++ == $n) {
				$cout .= "<p>&nbsp;</p><br /></td></tr><tr><td colspan='2'><b>Password Challenge</b></td></tr><tr><td colspan='2'>";
				$cout .= "<table width='100'><tr><td colspan='2' style='padding: 7px; text-align: left;'><font size='2' face='Arial'>Question: " . $q[$r[0]] . "<input type='hidden' value='" . $r[0] . "' name='q' /></td></tr><tr><td style='padding: 7px; text-align: left;'><font size='2' face='Arial'>Answer:</font></td><td style='padding: 7px; text-align: left;'><input type='text' size='47' name='a' id='a' required /></td></tr></table>";
				break;
			}
		}
		echo $cout;

	}

	//######################################
	//######################################
	clean();
	//######################################
	//######################################
}


?>