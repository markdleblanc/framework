<?php
/**
 * @copyright 2014 Mark LeBlanc
 * @author  Mark LeBlanc <mark@compute.systems>
 */
namespace framework\core\exceptions;

use framework\core\Response,
	\Exception;

/**
 * An exception that can be represented as a Response object.
 */
class ResponseException extends Exception {
	/**
	 * A descriptive message about the error that has occured.
	 */
	protected $message;

	/**
	 * The format of the message.
	 */
	private $format;

	/**
	 * The HTTP status code.
	 */
	private $status;

	public function __construct($message, $format, $status) {
		$this->message = $message;
		$this->format = $format;
		$this->status = $status;
	}

	public function getFormat() {
		return $this->format;
	}

	public function getStatus() {
		return $this->status;
	}

	public function getResponse() {
		return new Response($this->status, $this->format, array('locations' => array('home' => '/index'), 'entity' => 'platform.errors.' . $this->status, 'message' => $this->message, 'trace' => $this->getTraceAsString()));
	}
}