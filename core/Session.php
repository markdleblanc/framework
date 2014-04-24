<?php
/**
 * @copyright 2014 Mark LeBlanc
 * @author  Mark LeBlanc <mark@compute.systems>
 */
namespace framework\core;

/**
 * An immutable request from a remote end-point.
 */
class Session {

	public function __construct() {
		if(@session_start() === false)
			throw new InvalidSessionException('Unable to start a new session. Please ensure cookies are enabled.');
	}


	public function set($key, $value) {
		$_SESSION[$key] = $value;
	}

	public function get($key) {
		if($this->has($key))
			return $_SESSION[$key];
		return false;
	}

	public function has($key) {
		return isset($_SESSION[$key]);
	}

	public function remove($key) {
		unset($_SESSION[$key]);
		return true;
	}
}