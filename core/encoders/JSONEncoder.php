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
class JSONEncoder extends Encoder {

	public function __construct() {
		parent::__construct();
	}

	public function encode($entity, $payload) {
		return json_encode($payload);
	}
}