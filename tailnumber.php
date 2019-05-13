<?php $loginScreen = false; ?>
<?php require_once("scripts/e.php"); ?>
<?php
//header('Content-type: application/json');
echo genTailNumberSelection($_REQUEST['customer'], $_REQUEST['aircrafttype'], $_REQUEST['tailnumber']);
?>