<?php

require_once(VPANEL_CORE . "/storageobject.class.php");
require_once(VPANEL_CORE . "/mail.class.php");
require_once(VPANEL_CORE . "/mailtemplateheader.class.php");

class MailTemplate extends StorageClass {
	private $templateid;
	private $gliederungid;
	private $label;
	private $body;

	private $gliederung;
	private $headers;
	private $attachments;

	public static function factory(Storage $storage, $row) {
		$mailtemplate = new MailTemplate($storage);
		$mailtemplate->setTemplateID($row["templateid"]);
		$mailtemplate->setGliederungID($row["gliederungid"]);
		$mailtemplate->setLabel($row["label"]);
		$mailtemplate->setBody($row["body"]);
		return $mailtemplate;
	}

	public function getTemplateID() {
		return $this->templateid;
	}

	public function setTemplateID($templateid) {
		$this->templateid = $templateid;
	}

	public function getGliederungID() {
		return $this->gliederungid;
	}

	public function setGliederungID($gliederungid) {
		if ($this->gliederungid != $gliederungid) {
			$this->gliederung = null;
		}
		$this->gliederungid = $gliederungid;
	}

	public function getGliederung() {
		if ($this->gliederung == null) {
			$this->gliederung = $this->getStorage()->getGliederung($this->getGliederungID());
		}
		return $this->gliederung;
	}

	public function setGliederung($gliederung) {
		$this->setGliederungID($gliederung->getGliederungID());
		$this->gliederung = $gliederung;
	}

	public function getLabel() {
		return $this->label;
	}

	public function setLabel($label) {
		$this->label = $label;
	}

	public function getBody() {
		return $this->body;
	}

	public function setBody($body) {
		$this->body = $body;
	}

	public function save(Storage $storage = null) {
		if ($storage === null) {
			$storage = $this->getStorage();
		}
		$this->setTemplateID( $storage->setMailTemplate(
			$this->getTemplateID(),
			$this->getGliederungID(),
			$this->getLabel(),
			$this->getBody() ));

		if (isset($this->headers)) {
			$headerfields = array();
			$headervalues = array();
			foreach ($this->headers as $header) {
				$headerfields[] = $header->getField();
				$headervalues[] = $header->getValue();
			}
			$storage->setMailTemplateHeaderList($this->getTemplateID(), $headerfields, $headervalues);
		}

		if (isset($this->attachments)) {
			$storage->setMailTemplateAttachmentList($this->getTemplateID(), array_keys($this->attachments));
		}
	}

	public function delete(Storage $storage = null) {
		if ($storage === null) {
			$storage = $this->getStorage();
		}
		$storage->setMailTemplateHeaderList($this->getTemplateID(), array(), array());
		$storage->setMailTemplateAttachmentList($this->getTemplateID(), array());
		$storage->delMailTemplate($this->getTemplateID());
	}

	public function getHeaders() {
		if ($this->headers == null) {
			$this->headers = $this->getStorage()->getMailTemplateHeaderList($this->getTemplateID());
		}
		return $this->headers;
	}

	public function delHeader($header) {
		unset($this->headers[strtolower($header)]);
	}

	public function getHeader($header) {
		return $this->headers[strtolower($header)];
	}
	
	public function setHeader($field, $value) {
		$this->headers[strtolower($field)] = new MailTemplateHeader($this->getStorage(), $this->getTemplateID(), $field, $value);
	}

	public function getAttachments() {
		if ($this->attachments === null) {
			$attachments = $this->getStorage()->getMailTemplateAttachmentList($this->getTemplateID());
			$this->attachments = array();
			foreach ($attachments as $attachment) {
				$this->addAttachment($attachment);
			}
		}
		return $this->attachments;
	}

	public function getAttachment($attachmentid) {
		$this->getAttachments();
		return $this->attachments[$attachmentid];
	}

	public function delAttachment($attachmentid) {
		$this->getAttachments();
		unset($this->attachments[$attachmentid]);
	}
	
	public function addAttachment($attachment) {
		$this->getAttachments();
		$this->attachments[$attachment->getFileID()] = $attachment;
	}
	
	public function generateMail(Mitglied $mitglied) {
		global $config;
		$mail = $config->createMail($mitglied->getLatestRevision()->getKontakt()->getEMail());
		
		$headers = array();
		foreach ($this->getHeaders() as $header) {
			$mail->setHeader($header->getField(), $mitglied->replaceText($header->getValue()));
		}

		$body = $this->getBody();
		$mail->setBody($mitglied->replaceText($body));

		foreach ($this->getAttachments() as $attachment) {
			$mail->addAttachment($attachment);
		}
		
		return $mail;
	}
}

?>
