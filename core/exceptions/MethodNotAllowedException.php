<?php
/**
 * @copyright 2014 Mark LeBlanc
 * @author  Mark LeBlanc <mark@compute.systems>
 */
namespace framework\core\exceptions;

use  framework\core\exceptions\ResponseException;

/**
 * Represents an HTTP/1.1 405 error, in which the method used was not allowed.
 */
class MethodNotAllowedException extends ResponseException {

	public function __construct($message, $format = 'text/plain', $status = 405) {
		parent::__construct($message, $format, $status);
	}
}