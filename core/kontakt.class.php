<?php

require_once(VPANEL_CORE . "/globalobject.class.php");
require_once(VPANEL_CORE . "/ort.class.php");

class Kontakt extends GlobalClass {
	private $kontaktid;
	private $strasse;
	private $hausnummer;
	private $ortid;
	private $telefonnummer;
	private $handynummer;
	private $email;

	private $ort;
	
	public static function factory(Storage $storage, $row) {
		$kontakt = new Kontakt($storage);
		$kontakt->setKontaktID($row["kontaktid"]);
		$kontakt->setStrasse($row["strasse"]);
		$kontakt->setHausnummer($row["hausnummer"]);
		$kontakt->setOrtID($row["ortid"]);
		$kontakt->setTelefonnummer($row["telefonnummer"]);
		$kontakt->setHausnummer($row["handynummer"]);
		$kontakt->setEMail($row["email"]);
		return $kontakt;
	}

	public function getKontaktID() {
		return $this->kontaktid;
	}

	public function setKontaktID($kontaktid) {
		$this->kontaktid = $kontaktid;
	}

	public function getStrasse() {
		return $this->strasse;
	}

	public function setStrasse($strasse) {
		$this->strasse = $strasse;
	}

	public function getHausnummer() {
		return $this->hausnummer;
	}

	public function setHausnummer($hausnummer) {
		$this->hausnummer = $hausnummer;
	}

	public function getOrt() {
		if ($this->ort == null) {
			$this->ort = $this->getStorage()->getOrt($this->getOrtID());
		}
		return $this->ort;
	}

	public function getOrtID() {
		return $this->ortid;
	}

	public function setOrt(Ort $ort) {
		$this->setOrtID($ort->getOrtID());
		$this->ort = $ort;
	}

	public function setOrtID($ortid) {
		if ($ortid != $this->ortid) {
			$this->ort = null;
		}
		$this->ortid = $ortid;
	}

	public function getTelefonnummer() {
		return $this->telefonnummer;
	}
	
	public function setTelefonnummer($telefonnummer) {
		$this->telefonnummer = $telefonnummer;
	}

	public function getHandynummer() {
		return $this->handynummer;
	}

	public function setHandynummer($handynummer) {
		$this->handynummer = $handynummer;
	}

	public function getEMail() {
		return $this->email;
	}

	public function setEMail($email) {
		$this->email = $email;
	}

	public function save(Storage $storage = null) {
		if ($storage === null) {
			$storage = $this->getStorage();
		}
		$this->setKontaktID( $storage->setKontakt(
			$this->getKontaktID(),
			$this->getStrasse(),
			$this->getHausnummer(),
			$this->getOrtID(),
			$this->getTelefonnummer(),
			$this->getHandynummer(),
			$this->getEMail() ));
	}
}

?>
