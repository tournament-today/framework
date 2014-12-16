<?php namespace Syn\Framework\Abstracts;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;
use Redirect, View, Auth;
use Input;
use Request;
use Response;
use Session;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

abstract class Controller extends BaseController
{

	/**
	 * Title of the page
	 * @var string
	 */
	protected $title;

	/**
	 * Icon for the page
	 * @var
	 */
	protected $icon;

	/**
	 * Throws a page that the object was not found
	 * @param $message
	 * @return mixed
	 */
	public function notFound($message)
	{
		return $this -> view('errors.not-found', compact('message'));
	}

	/**
	 * Throws a page that the user is not allowed to view it
	 * @param $what
	 * @param $why
	 * @return mixed
	 */
	public function notAllowed($what, $why)
	{
		$this -> title = trans('error.not-allowed-title');
		$this -> icon = 'error';
		return $this -> view('pages.errors.not-allowed', compact('what','why'));
	}

	protected function json($body)
	{
		return Response::json($body);
	}
	/**
	 * Renders the page
	 * @param       $view
	 * @param array $params
	 * @return mixed
	 */
	protected function view($view, $params = [])
	{
		$title = $this -> title;
		$icon = $this -> icon;

		$params = array_merge(compact('title','icon'), $params);

		return View::make($view, $params);
	}

	/**
	 * Central smart redirect method
	 * @param       $to
	 * @param array $params
	 * @return mixed
	 */
	protected function redirect($to, $params = [])
	{
		if($to == 'refresh')
			return Redirect::refresh();
		if($to == 'home')
			return Redirect::home();
		// backwards
		if($to == 'back')
			return Redirect::back();
		// direct URL
		if(preg_match('/^(https?)?\/\/:/', $to))
			return Redirect::away($to);
		// already a redirect response
		if($to instanceof RedirectResponse)
			return $to;
		// assume route
		return Redirect::route($to, $params);
	}

	/**
	 * Whether the call is post
	 *
	 * @param string $method
	 * @param        $callback
	 * @throws \Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException
	 * @return bool
	 */
	protected function onRequestMethod($method = 'post', $callback)
	{

		if(Request::isMethod($method))
		{
			if(Input::has('_token') && Input::get('_token') != Session::token())
				throw new InvalidCsrfTokenException('Token mismatch');
			return call_user_func($callback);
		}

		return null;
	}

	/**
	 * @return null|\User
	 */
	protected function getVisitor()
	{
		return Auth::check() ? Auth::user() : null;
	}

	/**
	 * Generic delete form
	 * @param $model
	 * @param $name
	 * @return mixed
	 */
	public function delete($model, $name)
	{
		if(!$model->allowDelete())
			return $this->notAllowed("delete {$name}", 'insufficient rights');

		$this -> title = trans($model->translatePrefix . '-form-delete');

		return $this->onRequestMethod('post', function() use ($model)
		{
			$model->delete();
			return Redirect::route('home');
		}) ?: $this -> view('pages.delete', compact('model'));
	}
}