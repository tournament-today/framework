<?php namespace Syn\Framework\Forms;

use Session;
use Syn\Framework\Abstracts\Model;

class Form
{
	/**
	 * Redirect parameters
	 * @var array
	 */
	protected $_redirects = [];
	/**
	 * Messages to show
	 * @var array
	 */
	protected $_messages = [];
	/**
	 * Columns selection to use in form building from object
	 * @var array
	 */
	protected $_columns = [];
	/**
	 * Other arbitrary options to consider in form building
	 * @var array
	 */
	protected $_options = [];

	/**
	 * Model to use for form building
	 * @var Model
	 */
	protected $_model;

	/**
	 * @param Model $model
	 * @param array $columns
	 * @param array $options
	 */
	public function __construct(Model $model = null, $columns = [], $options = [])
	{
		$this -> _model = $model;
		$this -> _columns = $columns;
		$this -> _options = $options;

		$this -> generator = new FormGenerator();

	}

	/**
	 * @param       $redirect
	 * @param array $properties
	 */
	public function setRedirectOnSuccess($redirect, $properties = [])
	{
		$this -> _redirects['success'] = compact('redirect', 'properties');
	}

	/**
	 * @param       $redirect
	 * @param array $properties
	 */
	public function setRedirectOnCancel($redirect, $properties = [])
	{
		$this -> _redirects['cancel'] = compact('redirect', 'properties');
	}

	/**
	 * @param       $trans
	 * @info trans can be translatable string or a string
	 * @param array $placeholders
	 */
	public function setMessageOnSuccess($trans, $placeholders = [])
	{
		$this -> _messages['success'] = compact('trans', 'placeholders');
	}

	/**
	 * @param       $trans
	 * @info trans can be translatable string or a string
	 * @param array $placeholders
	 */
	public function setMessageOnFail($trans, $placeholders = [])
	{
		$this -> _messages['fail'] = compact('trans', 'placeholders');
	}

	public function __call($method, $arguments)
	{
		// fallback on existing methods, eg for overruling etc.
		if(method_exists($this, $method))
			return call_user_func_array([$this, $method], $arguments);
		// otherwise try to call method on form generator (eg: $form -> text or $form -> button)
		return call_user_func([$this -> generator, $method], $arguments);
	}
}