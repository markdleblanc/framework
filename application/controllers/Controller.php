<?php
/**
 * @copyright 2014 Mark LeBlanc
 * @author  Mark LeBlanc <mark@compute.systems>
 */
namespace framework\application\controllers;

use framework\core\Request,
	framework\core\Response,
	framework\core\presentation\Presenter,
	framework\core\Resource;

/**
 *
 */
abstract class Controller {

	/**
	 *
	 */
	public function __construct() {}

	/**
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function post(Request $request) {
		return new Response(501, $request->getResponseFormat(), null);
	}

	/**
	 *
	 * @param Request $request
	 * @param string $identifier
	 * @return Response
	 */
	public function get(Request $request, $identifier = null) {
		return new Response(501, $request->getResponseFormat(), null);
	}

	/**
	 *
	 * @param Request $request
	 * @param string $identifier
	 * @return Response
	 */
	public function put(Request $request, $identifier) {
		return new Response(501, $request->getResponseFormat(), null);
	}

	/**
	 *
	 * @param Request $request
	 * @param string $identifier
	 * @return Response
	 */
	public function delete(Request $request, $identifier = null) {
		return new Response(501, $request->getResponseFormat(), null);
	}

	/**
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function options(Request $request) {
		return new Response(501, $request->getResponseFormat(), null);
	}

	/**
	 *
	 * @param Request $request
	 * @param string $identifier
	 * @return Response
	 */
	public function head(Request $request, $identifier = null) {
		return new Response(501, $request->getResponseFormat(), null);
	}
}