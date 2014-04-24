<?php
/**
 * @copyright 2014 Mark LeBlanc
 * @author  Mark LeBlanc <mark@compute.systems>
 */
namespace framework\core\encoders;

/**
 *
 */
abstract class Encoder {
	/**
	 *
	 */
	public function __construct() {

	}

	/**
	 *
	 */
	public abstract function encode($entity, $payload);
}