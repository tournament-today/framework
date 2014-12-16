<?php namespace Syn\Framework\Forms;

use Syn\Framework\Exceptions\MissingFormTemplateException;
use Syn\Framework\Interfaces\FormGeneratorInterface;
use View, Session;

class FormGenerator implements FormGeneratorInterface
{
	public function input($type, $params = [])
	{
		return $this -> view($type, array_merge(compact('type'), $params));
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
		// catch a method like ->wysiwyg(.., ..)
		elseif(View::exists("framework::form.elements.{$method}"))
			return $this -> input($method, $arguments);
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
			throw new MissingFormTemplateException("No template for element {$template} found");
	}
}