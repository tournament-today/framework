<?php namespace Syn\Framework\Forms;

/**
 * Class FormWizardTrait
 * @info used to provide wizard functionality to Form extended classes
 *
 * @package Syn\Framework\Forms
 */
trait FormWizardTrait
{
	/**
	 * The current step
	 * @var int
	 */
	protected $step;

	/**
	 * Wizard object
	 * @var Wizard
	 */
	protected $wizard;

	/**
	 * @param $step_no
	 */
	public function setStep($step_no)
	{
		$this -> step = $step_no;
	}

	/**
	 * @param Wizard $wizard
	 */
	public function setWizard(Wizard $wizard)
	{
		$this -> wizard = $wizard;
	}
}