<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

if (!$session->isSignedIn()) {
	$ui->viewLogin();
	exit;
}

$ui->viewIndex();

?>
