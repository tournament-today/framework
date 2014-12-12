<?php namespace Syn\Framework\Twig\Extensions;


use Twig_Extension;
use Twig_SimpleFunction;
use Twig_SimpleFilter;
use Syn\Framework\Classes\Timing as TimingClass;


class Timing extends Twig_Extension
{
	public function __construct()
	{
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName()
	{
		return 'Syn_Framework_Twig_Extensions_Timing';
	}

	public function getFilters()
	{
		return [
			new Twig_SimpleFilter(
				'remaining',
				function($value)
				{
					if(!($value instanceof \Carbon\Carbon))
						return $value;
					return TimingClass::remaining($value);

				}
			),
			new Twig_SimpleFilter(
				'ago',
				function($value)
				{
					if(!($value instanceof \Carbon\Carbon))
						return $value;
					return TimingClass::ago($value);

				}
			)
		];
	}
	/**
	 * {@inheritDoc}
	 */
	public function getFunctions()
	{
		return [];
	}
}
