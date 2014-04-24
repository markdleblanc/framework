<?php
/**
 * @copyright 2014 Mark LeBlanc
 * @author  Mark LeBlanc <mark@compute.systems>
 */
namespace framework\core;

use framework\core\Request,
	framework\core\Response,
	framework\core\Resource,	
	framework\core\Urn,
	framework\core\storage\Storage,
	/* Content encoding. */
	framework\core\encoders\Encoder,
	framework\core\encoders\JSONEncoder,
	framework\core\encoders\XMLEncoder,
	framework\core\encoders\HTMLEncoder,
	framework\core\encoders\BaseEncoder,
	/* Exception handling. */
	framework\core\exceptions\InvalidServiceException,
	framework\core\exceptions\ResourceNotFoundException,
	framework\core\exceptions\MethodNotAllowedException;

/**
 * Acts as a facade to a more complicated restful service.
 */
class Gateway {
	/**
	 * The communication method used.
	 */
	private $method;

	/**
	 * The referenced service name.
	 */
	private $entity;

	public function __construct() {

	}

	/**
	 * Gets the response of a restful query to the underlying service.
	 * @param  Urn    $urn The resource identifier that is being operated on.
	 * @throws  ResourceNotFoundException If no resource can be found using the urn parameter.
	 * @throws  MethodNotAllowedException If the request method was not permitted for the resource.
	 * @return Response      The response of a restful operation.
	 */
	public function recieve(Request $request, Urn $urn) {
		if(!$urn->existsAsResource()) throw new InvalidServiceException("No valid service matching your terms was found.");

		$class 		= $urn->getService();
		$controller = new $class;
		$method		= $urn->getAction();
		$identifier = $urn->getIdentifier();

		$this->entity = $urn->getEntity();

		$this->method = $request->getMethod();

		$address = $_SERVER['REMOTE_ADDR'];
		$page = $_SERVER['REQUEST_URI'];
		$timestamp = new \DateTime('NOW', new \DateTimeZone('GMT'));

		try {
			// Method doesn't exist, pass the name as a parameter to the index function.
			if(isset($controller) && is_callable(array($controller, $this->method))) 
				return $controller->{$this->method}($request, $identifier);
		} catch (Exception $exception) {
			throw new ResponseException($exception->getMessage(), 'text/html', 500);
		}
		throw new MethodNotAllowedException("This service does not accept <{$method}> requests.");
	}

	public function send(Response $response) {
		/**
		 * Response will define an appropriate encoding type to respond with.
		 * This can be either HTML, XML, or JSON.
		 */
		$headers = $response->getHeaders();
		$status = $response->getStatus();
		$message = $this->getStatusMessage($status);

		$format = $response->getFormat();
		$data = $response->getData();

		$encoder = null;
		switch($format) {
			case 'application/json':
				$encoder = new JSONEncoder();
			break;

			case 'text/xml':
				$encoder = new XMLEncoder();
			break;

			case 'text/html':
				$encoder = new HTMLEncoder();
			break;

			default:
				$encoder = new BaseEncoder();
			break;
		}

		$entity = null;
		if($this->entity != null) {
			$entity = $this->entity . '.' . $this->method;
		} else if (isset($data['entity'])) {
			$entity = $data['entity'];
		}

		/* Browsers won't redirect if content is returned, this fix prevents that. */
		if($status < 200 || $status >= 300 && $format == 'text/html' && $status < 400)
			$content = null;
		else $content = $encoder->encode($entity, $data);

		// Encode the data.
		$length = mb_strlen($content); # If, and only if content is UTF8

		$headers[] = "Content-Length: $length";
		$headers[] = "Content-Type: $format";
		$headers[] = "HTTP/1.1 $status $message";

		// Push our generated headers to the first.
		// I'm not 100% positive that we need to send the http protocol / status first.
		$headers = array_reverse($headers);

		foreach($headers as $header)
			header($header);

		if($length > 0) echo $content;
		exit;
	}

	/**
	 * @TODO MOVE ME!
	 */
	public function getStatusMessage($status) {
		$messages = array
		(
			# Informational
			100 => 'Continue',
			101 => 'Switching Protocols',
			102 => 'Processing',
			# Success
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			207 => 'Multi-Status',
			226 => 'IM Used',
			# Redirection
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => 'Switch Proxy',
			307 => 'Temporary Redirect',
			308 => 'Permanent Redirect',
			# Client Error
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			418 => 'I\'m a teapot',
			422 => 'Unprocessable Entity',
			# Server Error
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported'
		);

		return $messages[$status];
	}
}