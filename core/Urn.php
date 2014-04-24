<?php
/**
 * @copyright 2014 Mark LeBlanc
 * @author  Mark LeBlanc <mark@compute.systems>
 */
namespace framework\core;

/**
 * Identifies a named resource.
 */
class Urn {
	/**
	 * The service capable of handling this request.
	 * @var string
	 */
	private $service;

	/**
	 * @var string
	 */
	private $entity;

	/**
	 * The action to apply against the service.
	 * @var string
	 */
	private $action;

	/**
	 * Unique identifier to a specific resource available from a service.
	 * @var mixed
	 */
	private $identifier;

	/**
	 * [__construct description]
	 */
	public function __construct() {}

	/**
	 * [decode description]
	 * @param  [type] $request_uri [description]
	 * @return [type]              [description]
	 */
	public static function decode() {
		$instance = new Urn();
		$request_uri = $_SERVER['REQUEST_URI'];

		if(strlen($request_uri) === 1)
		{
			// @todo Remove hardcoded default values.
			$instance->service = 'framework\\application\\controllers\\IndexController';
			$instance->entity = 'index';

			// $instance->action = 'index';
			// $instance->identifier = 'welcome';
			return $instance;
		}

		$tokens = explode('/', substr($request_uri, 1));
		switch(count($tokens)) {
			case 2: # Service action request.
				$instance->identifier = $tokens[1];
				/* Falls through. */
			case 1: # Service request.
				$instance->service = 'framework\\application\\controllers\\' . ucwords($tokens[0]) . 'Controller';
				// if($instance->action == null)
					// $instance->action = 'index';
				$instance->entity = $tokens[0];
			break;

			default:
				/* Pop controller, and action off the tokens array. */
				$controller = array_shift($tokens);
				$action = array_shift($tokens);

				$instance->service = 'framework\\application\\controllers\\' . ucwords($controller) . 'Controller';
				$instance->action = $action;
				$instance->identifier = $tokens;
				$instance->entity = $controller;
			break;
		}

		return $instance;
	}

	/**
	 * The human readable name of the service.
	 * @return string The human readable name of the service.
	 */
	public function getEntity()
	{
		return $this->entity;
	}

	/**
	 * A controller provides functionality to operate on a Resource in a CRUD-like manner.
	 * @return string The absolute location to a Controller object.
	 */
	public function getService() {
		return $this->service;
	}

	/**
	 * The action to perform on the service provider.
	 * @return string The name of the action to perform.
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * A unique identifer that references as specific resource.
	 * @return mixed Either a numeric id, or unique name.
	 */
	public function getIdentifier() {
		return $this->identifier;
	}

	/**
	 * Checks if a resource controller exists.
	 * @return boolean Whether the controller exists.
	 */
	public function existsAsResource() {
		return class_exists($this->getService());
	}
}