<?php
/**
 * @copyright 2014 Mark LeBlanc
 * @author  Mark LeBlanc <mark@compute.systems>
 */
namespace framework\core\exceptions;

use framework\core\exceptions\ResponseException;

/**
 * Represents an HTTP/1.1 500 error, in which the resource requested was invalid.
 */
class InvalidSessionException extends ResponseException {

	public function __construct($message, $format = 'text/plain', $status = 400) {
		parent::__construct($message, $format, $status);
	}
}