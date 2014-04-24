<?php
/**
 * Acts as a facade to reference User objects.
 *
 * @copyright 2014 Mark LeBlanc
 * @author  Mark LeBlanc <mark@compute.systems>
 */
namespace framework\application\repositories;

use framework\core\Resource;

/**
 * Defines mechanisms by which to manipulate Users.
 */
class UserRepository {

	public function __construct() {}

	/**
	 *
	 */
	public function loadById($identifier) {
		if(!is_numeric($identifier))
			throw new RuntimeException('Identifier must be a numeric value.');

		return Resource::build('User')->where('uid', $identifier)->find();
	}

	/**
	 *
	 */
	public function loadByEmail($email) {
		if(!is_string($email))
			throw new RuntimeException('Email must be a string.');

		return Resource::build('User')->where('emailAddress', $email)->find();
	}

	/**
	 *
	 */
	public function create($email, $password, $salt) {
		$user = Resource::build('User');
		$user->setEmailAddress($email);
		$user->setPassword($password);
		$user->setSalt($salt);
		return $user->create();
	}
}