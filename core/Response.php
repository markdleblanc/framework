<?php
/**
 * @copyright 2014 Mark LeBlanc
 * @author  Mark LeBlanc <mark@compute.systems>
 */
namespace framework\core;

/**
 * Immutable response object.
 */
class Response {
	/**
	 * Http response status id.
	 */
	private $status;

	/**
	 * Http header fields.
	 */
	private $headers;

	/**
	 * Data container with results of the request.
	 */
	private $data;

	/**
	 * Mime-type of content.
	 */
	private $format;

	public function __construct($status = 200, $format = 'text/html', $data = null, $headers = null) {
		$this->status = $status;
		$this->format = $format;
		$this->data = $data;
		$this->headers = $headers;
	}

	/**
	 * [getStatus description]
	 * @return [type]
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * [getFormat description]
	 * @return [type]
	 */
	public function getFormat() {
		return $this->format;
	}

	/**
	 * [hasContent description]
	 * @return boolean
	 */
	public function hasData() {
		return $this->data == null ? false : true;
	}

	/**
	 * [getContent description]
	 * @return [type]
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * [getHeaders description]
	 * @return [type]
	 */
	public function getHeaders() {
		return $this->headers;
	}

	/**
	 * Stores 'n' headers in a cloned response object.
	 * @param  mixed $headers Either a single string, or an array of strings.
	 * @return clone of this.
	 */
	public function withHeaders($headers) {
		$replica = clone $this;
		if(is_array($headers))
			$replica->headers = array_merge(array($replica->headers, $headers));
		else $replica->headers[] = $header;
		return $replica;
	}
}