<?php
/**
 * @copyright 2014 Mark LeBlanc
 * @author  Mark LeBlanc <mark@compute.systems>
 */
namespace framework\application\controllers;

use framework\core\Request,
	framework\core\Response,
	framework\core\presentation\Presenter,

	/* Application level logic. */
	framework\application\repositories\UserRepository,
	framework\application\facades\UserFacade;

/**
 *
 */
class AuthenticationController extends Controller {

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
		list($username, $password) = array_values($request->getData());
		
		$repository = new UserRepository(/* Database Pool */);
		$service = new UserFacade($repository);

		if(!$service->authenticate($username, $password))
			return new Response(404, $request->getResponseFormat(), null, array('Location: /'));

		$user = $service->retrieve($username);

		$session = $request->getSession();
		$session->set('authenticated', true);
		$session->set('username', $user->getEmailAddress());
		$session->set('password', $user->getPassword());

		return new Response(303, $request->getResponseFormat(), null, array('Location: /overview'));
	}

	/**
	 *
	 * @param Request $request
	 * @param string $identifier
	 * @return Response
	 */
	public function get(Request $request, $identifier = null) {
		return new Response(200, $request->getResponseFormat(), null);
	}
}