<?php namespace Syn\Framework\Forms;

use Syn\Framework\Exceptions\MissingFormTemplateException;
use View, Session;

class FormGenerator
{

	public function open($id = null, $method = 'post', $action = null, $class = null)
	{
		$token = Session::token();
		return $this -> view('open', compact('id', 'method', 'action', 'class', 'token'));
	}
	public function text($name, $placeholder = null, $default = null, $icon = null, $error = null, $validation = null, $help = null)
	{
		return $this -> view('text', compact('name', 'placeholder', 'default', 'icon', 'error', 'validation', 'help'));
	}
	public function checkbox()
	{

	}
	public function close()
	{

	}
	/**
	 * @param $method
	 * @param $arguments
	 * @return mixed
	 * @throws \Syn\Framework\Exceptions\MissingFormTemplateException
	 */
	public function __call($method, $arguments)
	{
		// fallback on existing methods, eg for overruling etc.
		if(method_exists($this, $method))
			return call_user_func_array([$this, $method], $arguments);
		else
			throw new MissingFormTemplateException("No method for element {$method} found");
	}

	/**
	 * Parses template
	 * @param       $template
	 * @param array $params
	 * @return \Illuminate\View\View
	 * @throws \Syn\Framework\Exceptions\MissingFormTemplateException
	 */
	protected function view($template, $params = [])
	{
		// otherwise we can use $this -> text, $this -> submit etc to parse elements
		if(View::exists("framework::form.{$template}"))
			return view("framework::form.{$template}", $params);
		elseif(View::exists("framework::form.elements.{$template}"))
			return view("framework::form.elements.{$template}", $params);
		else
			throw new MissingFormTemplateException("No template for element {$method} found");
	}
}