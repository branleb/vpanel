<?php

define("VPANEL_ROOT",				dirname(__FILE__));
define("VPANEL_CORE",				VPANEL_ROOT . "/core");
define("VPANEL_STORAGE",			VPANEL_CORE . "/storage");
define("VPANEL_MITGLIEDERMATCHER",		VPANEL_CORE . "/mitgliedermatcher");
define("VPANEL_MITGLIEDERFILTERACTIONS",	VPANEL_CORE . "/mitgliederfilteractions");
define("VPANEL_DOKUMENTTEMPLATES",		VPANEL_CORE . "/dokumenttemplates");
define("VPANEL_DOKUMENTTRANSITIONEN",		VPANEL_CORE . "/dokumenttransitionen");
define("VPANEL_SENDMAILBACKEND",		VPANEL_CORE . "/sendmailbackend");
define("VPANEL_PROCESSES",			VPANEL_CORE . "/processes");
define("VPANEL_UI",				VPANEL_ROOT . "/ui");
define("VPANEL_LANGUAGE",			VPANEL_UI . "/language");
define("VPANEL_FILES",				VPANEL_ROOT . "/files");

require_once(VPANEL_UI . "/phpsession.class.php");
require_once(VPANEL_UI . "/apisession.class.php");

class DefaultConfig {
	public function getSession($api = false) {
		if ($api) {
			return new APISession($this);
		} else {
			return new PHPSession($this);
		}
	}

	/** Fuer GlobaleIDs **/
	public function getHostPart() {
		return "example.org";
	}
	public function generateGlobalID() {
		return uniqid("", true) . "@" . $this->getHostPart();
	}

	/** Mehrsprachen-Support **/
	private $langs = array();
	public function getLang($lang = null) {
		if (isset($this->langs[$lang])) {
			return $this->langs[$lang];
		}
		return current($this->langs);
	}
	public function registerLang($name, Language $lang) {
		$this->langs[$name] = $lang;
	}

	private $pages;
	public function getLink() {
		$params = func_get_args();
		$name = array_shift($params);
		return vsprintf($this->pages[$name], $params);
	}
	public function registerPage($name, $link) {
		$this->pages[$name] = $link;
	}

	private $storage;
	public function setStorage($storage) {
		$this->storage = $storage;
	}
	public function getStorage() {
		return $this->storage;
	}

	private $sendmailbackend;
	public function setSendMailBackend($sendmailbackend) {
		$this->sendmailbackend = $sendmailbackend;
	}
	public function getSendMailBackend() {
		return $this->sendmailbackend;
	}
	protected function getBounceAddress($email) {
		return "bounce+" . $email->getEMailID() . "@" . $this->getHostPart();
	}
	protected function getFromMailAddress() {
		return "vpanel@" . $this->getHostPart();
	}
	public function createMail($email) {
		$mail = new Mail();
		$mail->setRecipient($email);
		$mail->setBounceAddress($this->getBounceAddress($email));
		$mail->setHeader("From", $this->getFromMailAddress());
		$mail->setHeader("X-VPanel", $this->getHostPart());
		return $mail;
	}
}

?>
