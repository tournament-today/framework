<?php namespace Syn\Framework\Forms;

use Syn\Framework\Abstracts\Model;
use Syn\Framework\Exceptions\OverwriteProtectedPropertyException;

class Wizard
{
	/**
	 * Definition of the steps
	 *
	 * @var array
	 */
	protected $_steps;

	/**
	 * Holds summary information; if empty no summary is given
	 * @var array
	 */
	protected $_summary;

	public function __construct()
	{
		
	}

	public function registerStep($name, Form $form, $nextCallback)
	{
		if(array_get($this -> _steps, $name))
			throw new OverwriteProtectedPropertyException("Step {$name} already defined in wizard");

		$this -> _steps[$name] = [
			'form' => $form,
			'next' => $nextCallback
		];
	}

	/**
	 * Registers a summary page
	 * @param Model $model
	 * @param array $summary_items
	 */
	public function registerSummary(Model $model, $summary_items = [])
	{
		$this -> _summary = compact('model', 'summary_items');
	}
}