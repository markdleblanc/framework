<?php
/**
 * @copyright 2014 Mark LeBlanc
 * @author  Mark LeBlanc <mark@compute.systems>
 */
namespace framework\core;

/**
 * An immutable request from a remote end-point.
 */
class Request
{
	# All headers recieved.
	private $headers;

	# The remote end point.
	private $remote;

	# The method by which the client sent the incoming data.
	private $method;

	# The time at which the request was recieved.
	private $time;

	# An associative array of data from an external source.
	private $data;

	# The format in which the data will be encoded on return.
	private $format;

	/**
	 * @var Session
	 */
	private $session;

	# Exists solely to defeat instantiation.
	private function __construct() { }

	/*
	 * Decodes information pertaining to an incoming request.
	 * Returns an immutable decoded Request object.
	 */
	public static function decode()
	{
		$request = new Request();
		
		$request->remote = $_SERVER['REMOTE_ADDR'];
		$request->method = strtolower($_SERVER['REQUEST_METHOD']);
		$request->time = $_SERVER['REQUEST_TIME'];

		$formats = explode(',', $_SERVER['HTTP_ACCEPT']);
		$request->format = $formats[0];

		if(isset($_SERVER['CONTENT_TYPE'])) {
			$contentTypes = $_SERVER['CONTENT_TYPE'];
			$content_type_tokens = explode(';', $contentTypes);
			$contentType = array_shift($content_type_tokens);
		} else {
			$contentType = 'text/html';
		}
		switch($request->method)
		{
			case 'get':
				$request->data = $_GET;
			break;
			case 'post':
				if(isset($_POST['_method'])) $request->method = strtolower($_POST['_method']);
				$request->data = $_POST;
			break;
			case 'put':
				if($contentType == 'application/json')
					$request->data = json_decode(file_get_contents('php://input'), true);
				else $request->data = parse_str(file_get_contents('php://input'), $_PUT);

                // $request->data = file_get_contents('php://input');//$data;
			break;
			default:
				# Perhaps throw an exception.
				$request->data = array();
			break;
		}

		try {
			$request->session = new Session();
		} catch (InvalidSessionException $exception) {
			throw $exception;
		}
		return $request;
	}

	public function getSession() {
		return $this->session;
	}

	public function getRemoteEndPoint() {
		return $this->remote;
	}

	public function getMethod()
	{
		return $this->method;
	}

	public function getTime()
	{
		return $this->time;
	}

	public function getData()
	{
		return $this->data;
	}

	public function getResponseFormat()
	{
		return $this->format;
	}

	/**
	 * Supports both Authentication header, and session based authentication.
	 */
	public function getCredentials()
	{
	    if($this->session->get('authenticated') == true) {
    		return array('username' => $this->session->get('username'), 'password' => $this->session->get('password'));
	    } else if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PASS']) && $_SERVER['PHP_AUTH_USER'] != null) {
		    return array('username' => $_SERVER['PHP_AUTH_USER'], 'password' => $_SERVER['PHP_AUTH_PASS']);
	    }
		return false;
	}
}