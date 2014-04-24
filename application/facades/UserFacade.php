<?php
/**
 * Provides domain-level logic for Entities.
 *
 * @copyright 2014 Mark LeBlanc
 * @author  Mark LeBlanc <mark@compute.systems>
 */
namespace framework\application\facades;

use framework\application\repositories\UserRepository,

	framework\application\repositories\AssetRepository,
	framework\application\facades\AssetFacade,

	framework\application\repositories\LiabilityRepository,
	framework\application\facades\LiabilityFacade,

	framework\application\entities\User;

/**
 * Defines mechanisms by which to manipulate Users.
 */
class UserFacade {

	/**
	 * A database abstraction layer utilized to perform IO manipulations to targetted Users.
	 * @var UserRepository
	 */
	private $repository;

	/**
	 * Initializes the service which provides domain logic for the User entity.
	 * @param UserRepository repository Database abstraction layer for User entities.
	 */
	public function __construct(UserRepository $repository) {
		$this->repository = $repository;
	}

	/**
	 * Retrieves a User entity based on either a numeric identifier, or the User's email address.
	 * @param mixed identifier Either a numeric index or an email address.
	 * @throws InvalidArgumentException If the identifier passed in isn't either numeric, or a string.
	 * @return User
	 */
	public function retrieve($identifier) {
		if(is_numeric($identifier)) {
			return $this->repository->loadById($identifier);
		} else if (is_string($identifier)) {
			return $this->repository->loadByEmail($identifier);
		}
		throw new \InvalidArgumentException("Identifier must be either a numeric index, or a valid Email address.");
	}

	/**
	 * Attempts to create a new User entity.
	 * @param string email The user's associated email address.
	 * @param string password The user's encrypted password.
	 * @param string salt The unique salt utilized to encrypt the password.
	 * @throws InvalidArgumentException If one or more fields are invalid.
	 * @return User
	 */
	public function create($email, $password, $salt) {
		return $this->repository->create($email, $password, $salt);
	}

	/**
	 * Returns whether or not the supplied password matches the User's current password.
	 * @param mixed identifier Either a numeric index or an email address associated to the User.
	 * @param string password The alleged password associated to the User.
	 * @throws InvalidArgumentException If the identifier isn't either an Email address or UID.
	 * @return boolean
	 */
	public function authenticate($identifier, $password) {
		try {
			$user = $this->retrieve($identifier);
		} catch (InvalidArgumentException $exception) {
			throw $exception;
		}

		if(!is_object($user) || ($user->getPassword() != $password && ($user->getPassword() != crypt($password, $user->getSalt()))))
			return false;
		return true;
	}

	/**
	 *
	 */
	public function calculate_net_worth(User $user) {
		$asset_repository = new AssetRepository(/* Database Pool */);
		$liability_repository = new LiabilityRepository(/* Database Pool */);

		$asset_facade = new AssetFacade($asset_repository);
		$liability_facade = new LiabilityFacade($liability_repository);

		$liabilities = $liability_facade->retrieve_all($user->getUid());
		$assets = $asset_facade->retrieve_all($user->getUid());

		$net_worth = 0;
		foreach($assets as $asset) {
			$net_worth += $asset->getValue();
		}

		foreach($liabilities as $liability) {
			$net_worth -= $liability->getValue();
		}

		return $net_worth;
	}
}