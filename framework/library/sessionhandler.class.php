<?php
namespace XOMBO;
class sessionHandler extends dbModelDefaults implements \SessionHandlerInterface {
	static $savePath;
	static $name;
	private $dbResult;
	static $handler = NULL;
	public static function init () {
		session_name (SESSION_COOKIE);
		session_set_cookie_params (SESSION_LIFETIME, SESSION_PATH, SESSION_DOMAIN, FALSE, FALSE);
		self::$handler = new self ();
		self::$handler->dontSave ();
		session_set_save_handler (
			array (self::$handler, 'open'),
			array (self::$handler, 'close'),
			array (self::$handler, 'read'),
			array (self::$handler, 'write'),
			array (self::$handler, 'destroy'),
			array (self::$handler, 'gc')
		);
	}
	public static function start () {
		session_start ();
		return true;
	}
	public static function stop () {
		session_unset ();
		return true;
	}
	public function open ($savePath, $name) {
		self::$savePath = $savePath;
		self::$name = $name;
		$this->dbResult = NULL;
		return true;
	}
	public function close () {
		self::$savePath = NULL;
		self::$name = NULL;
		$this->dbResult = NULL;
		return true;
	}
	private function getData ($ID) {
		if (is_null ($this->dbResult)) {
			$sessions = self::select (array ('savePath' => self::$savePath, 'name' => self::$name, 'sessionId' => $ID));
			if ($sessions->valid ()) {
				$this->dbResult = $sessions->current ();
			} else {
				$this->dbResult = new self ();
				$this->dbResult->savePath = self::$savePath;
				$this->dbResult->name = self::$name;
				$this->dbResult->sessionId = $ID;
				$this->dbResult->updated = date ("Y-m-d H:i:s");
			}
		}
		return $this;
	}
	public function read ($ID) {
		$this->getData ($ID);
		if (!is_null ($this->dbResult))
			return $this->dbResult->data;
		return "";
	}
	public function write ($ID, $data) {
		$this->getData ($ID);
		if (!is_null ($this->dbResult)) {
			$this->dbResult->data = $data;
			$this->dbResult->save ();
			return true;
		}
		return false;
	}
	public function destroy ($ID) {
		$this->getData ($ID);
		if (!is_null ($this->dbResult)) {
			$this->dbResult->delete ();
			$this->dbResult = NULL;
			self::$sessionPath = NULL;
			self::$name = NULL;
			return true;
		}
		return false;
	}
	public function gc ($maxlifetime) {
		// garbage collection
		db::query ("DELETE FROM `" . self::getTable () . "` WHERE `updated` <= TIMESTAMP(DATE_SUB(NOW(), INTERVAL " . $maxlifetime . " SECOND));");
		return true;
	}
}
