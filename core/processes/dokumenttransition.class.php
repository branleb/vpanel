<?php

require_once(VPANEL_CORE . "/process.class.php");

class DokumentTransitionProcess extends Process {
	private $dokumentid;
	private $gliederungids;
	private $kategorieid;
	private $statusid;
	private $nextkategorieid;
	private $nextstatusid;
	private $notizkommentar;

	public static function factory(Storage $storage, $row) {
		$process = new $row["class"]($storage);
		$process->setNextKategorieID($row["nextkategorieid"]);
		$process->setNextStatusID($row["nextstatusid"]);
		$process->setNotizKommentar($row["notizkommentar"]);
		if ($row["dokumentid"] != null) {
			$process->match($row["dokumentid"]);
		} else {
			$process->matchMulti($row["gliederungids"], $row["kategorieid"], $row["statusid"]);
		}
		return $process;
	}

	public function getNextKategorieID() {
		return $this->nextkategorieid;
	}

	public function setNextKategorieID($kategorieid) {
		$this->nextkategorieid = $kategorieid;
	}

	public function getNextStatusID() {
		return $this->nextstatusid;
	}

	public function setNextStatusID($statusid) {
		$this->nextstatusid = $statusid;
	}

	public function getNotizKommentar() {
		return $this->notizkommentar;
	}

	public function setNotizKommentar($kommentar) {
		$this->notizkommentar = $kommentar;
	}

	public function match($dokumentid) {
		$this->dokumentid = $dokumentid;
	}

	public function matchMulti($gliederungids, $kategorieid, $statusid) {
		$this->gliederungids = $gliederungids;
		$this->kategorieid = $kategorieid;
		$this->statusid = $statusid;
	}

	protected function getData() {
		$data = parent::getData();
		$data["class"] = get_class($this);
		$data["dokumentid"] = $this->dokumentid;
		$data["gliederungids"] = $this->gliederungids;
		$data["kategorieid"] = $this->kategorieid;
		$data["statusid"] = $this->statusid;
		$data["nextkategorieid"] = $this->nextkategorieid;
		$data["nextstatusid"] = $this->nextstatusid;
		$data["notizkommentar"] = $this->notizkommentar;
		return $data;
	}

	protected function runProcess() {
		if ($this->dokumentid != null) {
			$dokument = $this->getStorage()->getDokument($this->dokumentid);
			$this->initProcess();
			$this->runProcessStep($dokument);
			$this->finalizeProcess();
		} else {
			$result = $this->getStorage()->getDokumentResult($this->gliederungids, $this->kategorieid, $this->statusid);
			$max = $result->getCount();
			$i = 0;
			$stepwidth = ceil($max / 100);

			$this->initProcess();

			while ($item = $result->fetchRow()) {
				$this->runProcessStep($item);
continue;

				$notiz = new DokumentNotiz($this->getStorage());
				$notiz->setDokument($item);
				$notiz->setTimestamp(time());
				$notiz->setAuthorID($this->getUserID());
				$notiz->setKommentar($this->getNotizKommentar());

				if ($this->getNextKategorieID() != null) {
					$item->setDokumentKategorieID($this->getNextKategorieID());
					$notiz->setNextKategorieID($this->getNextKategorieID());
				}

				if ($this->getNextStatusID() != null) {
					$item->setDokumentStatusID($this->getNextStatusID());
					$notiz->setNextStatusID($this->getNextStatusID());
				}

				$notiz->save();
				$notiz->notify();
				$item->save();
				
				if ((++$i % $stepwidth) == 0) {
					$this->setProgress($i / $max);
					$this->save();
				}
			}

			$this->finalizeProcess();
		}
		
		$this->setProgress(1);
		$this->save();
	}

	public function initProcess() {}
	public function runProcessStep($item) {}
	public function finalizeProcess() {}
}

?>
