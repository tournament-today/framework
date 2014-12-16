<?php namespace Syn\Framework\Forms;

use Session;
use Syn\Framework\Abstracts\Model;
use Syn\Framework\Exceptions\MissingConfigurationException;
use Syn\Framework\Exceptions\MissingImplementationException;
use Syn\Framework\Interfaces\FormGeneratorInterface;
use Validator;

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
	 * The action to execute
	 * @var string
	 */
	protected $_action = "save";

	protected $_possible_actions = ['save', 'delete'];

	/**
	 * Form generator instance
	 * @var
	 */
	protected $_generator;

	/**
	 * @param Model $model
	 * @param array $columns
	 * @param array $options
	 * @throws \Syn\Framework\Exceptions\MissingImplementationException
	 */
	public function __construct(Model $model = null, $columns = [], $options = [])
	{
		$this -> _model = $model;
		$this -> _columns = $columns;
		$this -> _options = $options;

		if(array_get($options, 'action') && in_array(array_get($options, 'action'), $this->_possible_actions))
			$this -> _action = array_get($options, 'action');
		elseif(array_get($options, 'action'))
			throw new MissingImplementationException("Unknown action");

		$this -> _generator = new FormGenerator();

	}

	/**
	 *
	 */
	public function getValidator()
	{
		// use the model validator
		if($this->_model)
			return $this->_model->getValidator($this->_model->getAttributes(), $this->_columns);
		// otherwise construct a new validator
		if(array_get($this->_options, 'validation'))
			return Validator::make($this->_columns, array_get($this->_options, 'validation'));

		throw new MissingConfigurationException("Cannot get Validator for form");
	}

	/**
	 * Sets the form generator
	 * @param FormGeneratorInterface $generator
	 */
	public function setFormGenerator(FormGeneratorInterface $generator)
	{
		$this -> _generator = $generator;
	}

	/**
	 * Gets the form generator
	 * @return FormGenerator
	 */
	public function getFormGenerator()
	{
		return $this -> _generator;
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