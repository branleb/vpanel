<?php

require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

abstract class LinkedLogicMitgliederMatcher extends MitgliederMatcher {
	protected $filters;

	public function __construct() {
		$this->filters = func_get_args();
	}

	public function getConditions() {
		return $this->filters;
	}
}

abstract class SingleLogicMitgliederMatcher extends MitgliederMatcher {
	protected $filter;

	public function __construct($filter) {
		$this->filter = $filter;
	}

	public function getCondition() {
		return $this->filter;
	}
}

class AndMitgliederMatcher extends LinkedLogicMitgliederMatcher {
	public function match(Mitglied $mitglied) {
		$m = true;
		foreach ($this->getConditions() as $filter) {
			$m = $m && $filter->match($mitglied);
		}
		return $m;
	}
}

class OrMitgliederMatcher extends LinkedLogicMitgliederMatcher {
	public function match(Mitglied $mitglied) {
		$m = false;
		foreach ($this->getConditions() as $filter) {
			$m = $m || $filter->match($mitglied);
		}
		return $m;
	}
}

class NotMitgliederMatcher extends SingleLogicMitgliederMatcher {
	public function match(Mitglied $mitglied) {
		return !$this->getCondition()->match($mitglied);
	}
}

?>
