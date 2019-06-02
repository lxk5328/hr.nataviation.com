<?php $loginScreen = false; ?>
<?php require_once("scripts/e.php"); ?>
<?php
//header('Content-type: application/json');
if ( $_REQUEST['action'] == 'type') {
    echo genAircraftTypeSelection($_REQUEST['customer'], null);
}
else {
    echo genTailNumberSelection($_REQUEST['customer'], $_REQUEST['aircrafttype'], $_REQUEST['tailnumber']);
}
?>