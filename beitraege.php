<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

if (!$session->isAllowed("beitraege_show")) {
	$ui->viewLogin();
	exit;
}

require_once(VPANEL_CORE . "/beitrag.class.php");

function parseBeitragFormular($session, &$beitrag = null) {
	$label = $session->getVariable("label");
	$hoehe = $session->getDoubleVariable("hoehe");
	$mailtemplateid = $session->getIntVariable("mailtemplateid");

	if ($beitrag == null) {
		$beitrag = new Beitrag($session->getStorage());
	}
	$beitrag->setLabel($label);
	$beitrag->setHoehe($hoehe);
	$beitrag->setMailTemplateID($mailtemplateid);
	$beitrag->save();
}

switch ($session->hasVariable("mode") ? $session->getVariable("mode") : null) {
case "details":
	$beitrag = $session->getStorage()->getBeitrag($session->getIntVariable("beitragid"));

	if ($session->getBoolVariable("save")) {
		if (!$session->isAllowed("beitraege_modify")) {
			$ui->viewLogin();
			exit;
		}
		
		parseBeitragFormular($session, $beitrag);
	}

	$pagesize = 20;
	$pagecount = ceil($session->getStorage()->getMitgliederBeitragByBeitragCount($beitrag->getBeitragID()) / $pagesize);
	$page = 0;
	if ($session->hasVariable("page") and $session->getVariable("page") >= 0 and $session->getVariable("page") < $pagecount) {
		$page = intval($session->getVariable("page"));
	}
	$offset = $page * $pagesize;
	$mitgliederbeitraglist = $session->getStorage()->getMitgliederBeitragByBeitragList($beitrag->getBeitragID(), $pagesize, $offset);
	$mailtemplates = $session->getStorage()->getMailTemplateList($session->getAllowedGliederungIDs("beitrag_modify"));

	$ui->viewBeitragDetails($beitrag, $mitgliederbeitraglist, $page, $pagecount, $mailtemplates);
	exit;
case "create":
	if ($session->getBoolVariable("save")) {
		if (!$session->isAllowed("beitraege_create")) {
			$ui->viewLogin();
			exit;
		}
		
		parseBeitragFormular($session, $beitrag);
		
		$ui->redirect($session->getLink("beitraege_details", $beitrag->getBeitragID()));
	}
	$mailtemplates = $session->getStorage()->getMailTemplateList($session->getAllowedGliederungIDs("beitrag_create"));

	$ui->viewBeitragCreate($mailtemplates);
	exit;
case "delete":
	if (!$session->isAllowed("beitraege_delete")) {
		$ui->viewLogin();
		exit;
	}
	$beitrag = $session->getStorage()->getBeitrag($session->getIntVariable("beitragid"));
	$beitrag->delete();
	$ui->redirect($session->getLink("beitraege"));
	exit;
default:
	$beitraege = $session->getStorage()->getBeitragList();

	$ui->viewBeitragList($beitraege);
	exit;
}

?>
