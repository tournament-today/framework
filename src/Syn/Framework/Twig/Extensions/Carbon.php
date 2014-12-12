<?php namespace Syn\Framework\Twig\Extensions;

use Auth;
use Twig_Extension;
use Twig_SimpleFunction;
use Twig_SimpleFilter;
use Illuminate\Support\Str;
use Carbon\Carbon as CarbonClass;

/**
 * Access Laravels url class in your Twig templates.
 */
class Carbon extends Twig_Extension
{
	public function __construct()
	{
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName()
	{
		return 'Syn_Framework_Twig_Extensions_Carbon';
	}

	public function getFilters()
	{
		return [
			new Twig_SimpleFilter(
				'tz',
				function($value)
				{
					if(!($value instanceof \Carbon\Carbon))
						$value = new CarbonClass($value);
					// set timezone based on user
					if(Auth::check() && Auth::user()->timezone)
						return $value->setTimezone(Auth::user()->timezone);

					return $value;
				}
			)
		];
	}
	/**
	 * {@inheritDoc}
	 */
	public function getFunctions()
	{
		return [
			new Twig_SimpleFunction(
				'carbon_*',
				function ($name) {
					$arguments = array_slice(func_get_args(), 1);
					$name      = Str::camel($name);

					return call_user_func_array(['Carbon\Carbon', $name], $arguments);
				}
			),
		];
	}
}
