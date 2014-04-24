<?php
/**
 * @copyright 2014 Mark LeBlanc
 * @author  Mark LeBlanc <mark@compute.systems>
 */
namespace framework\core\encoders;

use framework\core\encoders\Encoder,
	framework\core\presentation\Presenter;

/**
 *
 */
class HTMLEncoder extends Encoder {

	public function __construct() {
		parent::__construct();
	}

	public function encode($entity, $payload) {
		$presenter = new Presenter(array('response' => $payload));
		return $presenter->with($entity)->render(true);
	}
}