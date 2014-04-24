<?php
/**
 * @copyright 2014 Mark LeBlanc
 * @author  Mark LeBlanc <mark@compute.systems>
 */
namespace framework\core\exceptions;

use framework\core\exceptions\ResponseException;

/**
 * Represents an HTTP/1.1 404 error, in which an identified resource was not found.
 */
class ResourceNotFoundException extends ResponseException {

	public function __construct($message, $format = 'text/plain', $status = 404) {
		parent::__construct($message, $format, $status);
	}
}