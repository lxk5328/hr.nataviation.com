<?php $loginScreen = false; ?>
<?php require_once("e.php"); ?>
<?php

$sessionImpersonation = false;
if ($_SESSION['user']->getImpersonation() == "IMPERSONATED IDENTITY") { $sessionImpersonation = true; }

$_SESSION = array();
session_destroy();

if ($sessionImpersonation) {
	header("Location: /testing/?xsystem_logout=" . mt_rand());
} else {
	header("Location: /?xsystem_logout=" . mt_rand());
}

?>

