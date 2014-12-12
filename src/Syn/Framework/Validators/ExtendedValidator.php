<?php namespace Syn\Framework\Validators;

use Illuminate\Validation\Validator;

class ExtendedValidator extends Validator
{

	const TEAM_REGEX = '/^([a-z0-9_\- ]+)$/i';

	public function validateTeamName($attribute, $value, $parameters)
	{
		return preg_match(static::TEAM_REGEX, $value);
	}

	const CLAN_REGEX = '/^([a-z0-9_\- \*\(\)\&\^\#\@]+)$/i';

	public function validateClanName($attribute, $value, $parameters)
	{
		return preg_match(static::CLAN_REGEX, $value);
	}



	public function validateTime($attribute, $value, $parameters)
	{
		list($hour, $minute) = explode(":", $value);
		if(is_null($hour) || is_null($minute))
			return false;
		$hours = intval($hour);
		$minute = intval($minute);
		if($hour > 24 || ($hour == 24 && $minute > 0) || $minute > 59)
			return false;

		return true;
	}
}