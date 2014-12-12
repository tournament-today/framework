<?php
if ( ! function_exists('trans_has'))
{
	/**
	 * Translate the given message.
	 *
	 * @param  string  $id
	 * @param  array   $parameters
	 * @param  string  $domain
	 * @param  string  $locale
	 * @return string
	 */
	function trans_has($id, $parameters = array(), $domain = 'messages', $locale = null)
	{
		return app('translator')->has($id, $parameters, $domain, $locale);
	}
}