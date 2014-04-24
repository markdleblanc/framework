<?php
/**
 * Object representations of records in our persistence layer.
 *
 * @copyright 2014 Mark LeBlanc
 * @author  Mark LeBlanc <mark@compute.systems>
 */
namespace framework\application\entities;

use framework\core\Resource;

/**
 * Defines the fields and relationships for all Users.
 */
class User extends Resource {
	/**
	 * Unique identifier used to reference this resource.
	 * @var int
	 */
	private $uid;

	/**
	 * The current email address associated to this User. All email addresses in the platform must
	 * be both valid email addresses as per http://tools.ietf.org/html/rfc6530 and unique across 
	 * the platform.
	 * @var string
	 */
	private $emailAddress;

	/**
	 * The current mechanism to validate that a person has access to this User object. This is string is both encrypted,
	 * and salted.
	 * @var string
	 */
	private $password;

	/**
	 * The unique salt for this user's password. 
	 * @var string
	 */
	private $salt;

	/**
	 * Creates a new instance of a User object.
	 */
	public function __construct() {}

	/**
	 * Validates and assigns a uid to this user, returning true on success or
	 * false if the uid is invalid.
	 *
	 * @todo Validate the uid prior to assigning it.
	 * @param int uid The unique identifier associated to this User.
	 * @return boolean
	 */
	public function setUid($uid) {
		if(!is_numeric($uid))
			return false;

		$this->dirty('uid');
		$this->uid = $uid;
		return true;
	}

	/**
	 * The unique identifier used to reference this resource.
	 * @return int 
	 */
	public function getUid() {
		return $this->uid;
	}

	/**
	 * The current email address associated to this User.
	 * @return string
	 */
	public function getEmailAddress() {
		return $this->emailAddress;
	}

	/**
	 * Validates and assigns an email address to this user, returning true on success or
	 * false if the email address is invalid.
	 *
	 * @todo Validate the email address prior to assigning it.
	 * @param string email The new email address to associate to the User.
	 * @return boolean
	 */
	public function setEmailAddress($emailAddress) {
		// Flag the email field for the ORM to update it on the next save.
		$this->dirty('email');
		$this->emailAddress = $emailAddress;
		return true;
	}

	/**
	 * The encrypted password used to validate that a person has access to this User object
	 * @return string
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * Validates and assigns a password to this user, returning true on success or false
	 * if the password is invalid.
	 * 
	 * @todo Validate the password prior to assigning it.
	 * @param string password The encrypted password used to validate that a person has access to this User object
	 * @return boolean
	 */
	public function setPassword($password) {
		// Flag the password field for the ORM to update it on the next save.
		$this->dirty('password');
		$this->password = $password;
		return true;
	}

	/**
	 * The unique salt for this User's password. 
	 * @return string
	 */
	public function getSalt() {
		return $this->salt;
	}

	/**
	 * Validates and sets the current salt to the User's password. Returns true on success or false on failure to
	 * validate the salt.
	 *
	 * @param string salt The unique salt for this User's password.
	 * @return boolean 
	 */
	public function setSalt($salt) {
		$this->dirty('salt');
		$this->salt = $salt;
		return true;
	}
}