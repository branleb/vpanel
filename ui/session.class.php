<?php

require_once(VPANEL_CORE . "/user.class.php");
require_once(VPANEL_UI . "/template.class.php");

class Session {
	private $config;
	private $stor;

	public function __construct($config) {
		session_start();
		$this->config = $config;

		if (!isset($_SESSION["vpanel"])) {
			$_SESSION["vpanel"] = array();
		}
		$this->stor = &$_SESSION["vpanel"];
		$this->santisize();
	}

	public function santisize() {
		if ($this->stor["user"] != null) {
			$this->stor["user"]->setStorage($this->getStorage());
		}
	}

	public function isSignedIn() {
		return $this->stor["user"] != null;
	}
	public function login($username, $password) {
		$user = $this->getStorage()->getUserByUsername($username);
		if ($user == null or !$user->isValidPassword($password)) {
			throw new Exception("Login failed.");
		}
		$roles = $user->getRoles();
		$permissions = array();
		foreach ($roles as $role) {
			foreach ($role->getPermissions() as $permission) {
				$permissions[$permission->getPermissionID()] = $permission;
			}
		}
		$this->setUser($user);
		$this->setPermissions($permissions);
	}
	public function logout() {
		$this->setUser(null);
		$this->setPermissions(null);
	}
	public function setUser($user) {
		$this->stor["user"] = $user;
	}
	public function getUser() {
		if ($this->isSignedIn()) {
			return $this->stor["user"];
		}
		// TODO anonymous auth
		return new User($this->getStorage());
	}

	public function getPermissions() {
		return $this->stor["permissions"];
	}
	public function setPermissions($permissions) {
		$this->stor["permissions"] = $permissions;
	}
	public function isAllowed($permission) {
		if (!is_array($this->getPermissions())) {
			return false;
		}
		foreach ($this->getPermissions() as $perm) {
			if ($perm->getLabel() == $permission) {
				return true;
			}
		}
		return false;
	}

	public function setLang($lang) {
		$this->stor["lang"] = $lang;
	}
	public function getLang() {
		return $this->config->getLang($this->stor["lang"]);
	}

	public function getEncoding() {
		return "UTF-8";
	}

	public function getLink() {
		$params = func_get_args();
		return call_user_func_array(array($this->config, "getLink"), $params);
	}
	public function getStorage() {
		return $this->config->getStorage();
	}
	public function getTemplate() {
		return new Template($this);
	}
}

?>
