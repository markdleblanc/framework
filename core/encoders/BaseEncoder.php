<?php
/**
 * @copyright 2014 Mark LeBlanc
 * @author  Mark LeBlanc <mark@compute.systems>
 */
namespace framework\core\encoders;

use framework\core\encoders\Encoder;

/**
 *
 */
class BaseEncoder extends Encoder {

	public function __construct() {
		parent::__construct();
	}

	public function encode($entity, $payload) {
		return sprintf('<pre>%s</pre>', print_r($payload, true));
	}
}