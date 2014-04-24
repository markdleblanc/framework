<?php
/**
 * @copyright 2014 Mark LeBlanc
 * @author  Mark LeBlanc <mark@compute.systems>
 */
namespace framework;

use framework\core\Request,
	framework\core\Urn,
	framework\core\Gateway,
	framework\core\DynamicClassLoader,
	framework\core\storage\Storage,
	framework\core\exceptions\ResponseException;

require_once('core/DynamicClassLoader.php');

# Development mode only.
error_reporting(E_ALL);

ini_set('display_errors', true);
ini_set('log_errors', false);

# Needs to be tested on all operating systems.
define('PATH', getcwd() . DIRECTORY_SEPARATOR);
define('PRODUCTION', false);

/**
 * Registers an instance of the dynamic class loader onto the standard php library autoloader stack.
 */
if(!DynamicClassLoader::register(getcwd(), 'framework\\')) {
	die('Failed to register autoloader.<br/>');
}

$gateway = new Gateway();

// Temporarily initialize the storage facade with default values.
Storage::configure('provider', 'framework\\core\\storage\\driver\\SqlStorageDriver');
Storage::configure('username', 'username');
Storage::configure('password', 'password');
Storage::configure('host',     'localhost');
Storage::configure('database', 'database');

try {
	$storage = Storage::getInstance();
} catch (ResponseException $exception) {
	$gateway->send($exception->getResponse());
}

// Queries the underlying api for a response to a restful request.
try {
	if(($response = $gateway->recieve(Request::decode(), Urn::decode()))) {
		$gateway->send($response);
	}
} catch (InvalidSessionException $exception) {
	$gateway->send($exception->getResponse());
} catch (ResponseException $exception) {
	$gateway->send($exception->getResponse());
}

function seperateCamelCase($value) {
	$content = '';
	if(preg_match_all('/((?:^|[A-Z])[a-z0-9]+)/', $value, $matches) !== false) {
		foreach($matches[0] as $match) {
			if(end($matches[0]) != $match)
				$content .= lcfirst($match) . '_';
			else $content .= lcfirst($match);
		}
		return $content;
	}
	return $value;
}

function camelCase($value) {
	$value = str_replace('_', ' ', $value);
	$value = strtolower($value);
	$value = ucwords($value);
	$value = str_replace(' ', '', $value);
	$value = lcfirst($value);
	return $value;
}