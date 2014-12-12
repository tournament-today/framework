<?php namespace Syn\Framework\Twig\Extensions;

use Twig_Extension;
use Twig_SimpleFunction;
use Illuminate\Support\Str;

/**
 * Access Laravels url class in your Twig templates.
 */
class Route extends Twig_Extension
{
	public function __construct()
	{
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName()
	{
		return 'Syn_Framework_Twig_Extensions_Route';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFunctions()
	{
		return [
			new Twig_SimpleFunction(
				'route_*',
				function ($name) {
					$arguments = array_slice(func_get_args(), 1);
					$name      = Str::camel($name);

					return call_user_func_array(['Route', $name], $arguments);
				}
			),
		];
	}
}
