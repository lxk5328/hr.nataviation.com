<?php $loginScreen = false; ?>
<?php require_once("e.php"); ?>
<?php

echo "<h2>Mapped Positions</h2>";

$currentPosition = null;
$sql = "SELECT p.Description, pm.business_title FROM Positions p, position_mapping pm where p.ActiveFlag = 1 AND p.PositionID = pm.position_id GROUP BY p.Description, pm.business_title ORDER BY p.Description";
$rs = execSQL($sql);

foreach ($rs as $r) {
	if ($r['0'] == "") { continue; }
	array_push($positionArray, "'" . $r[0] . "'");

	if (is_null($currentPosition)) { 
		$currentPosition = $r['Description']; 
		echo "<p><b>" . $currentPosition . "</b><br />";
	}

	if ($r['Description'] != $currentPosition) { echo "<p><b>" . $r['Description'] . "</b><br />"; }
	echo "&nbsp;&nbsp;&nbsp;&nbsp; " . $r['business_title'] . "<br />";
	$currentPosition = $r['Description'];
}

echo "<h2>Unmapped Positions</h2>";

$sql = "SELECT Description FROM Positions WHERE ActiveFlag = 1 AND Description NOT IN (" . implode(',',$positionArray) . ")";
$rs = execSQL($sql);

foreach ($rs as $r) {
	if ($r[0] == "") { continue; }
	echo $r[0] . "<br />";
}

echo "<h2>Unmapped Census Business Titles</h2>";

$sql = "SELECT DISTINCT business_title FROM census WHERE business_title NOT IN (SELECT DISTINCT business_title FROM position_mapping)";
$rs = execSQL($sql);

foreach($rs as $r) {
	if ($r[0] == "") { continue; }
	echo $r[0] . "<br />";
}


//######################################
//######################################
clean();
//######################################
//######################################


?>