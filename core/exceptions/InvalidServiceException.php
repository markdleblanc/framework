<?php
/**
 * @copyright 2014 Mark LeBlanc
 * @author  Mark LeBlanc <mark@compute.systems>
 */
namespace framework\core\exceptions;

use framework\core\exceptions\ResponseException;

/**
 * Represents an HTTP/1.1 400 error, in which the service requested was non-existent.
 */
class InvalidServiceException extends ResponseException {

	public function __construct($message, $format = 'text/html', $status = 400) {
		parent::__construct($message, $format, $status);
	}
}