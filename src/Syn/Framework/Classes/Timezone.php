<?php namespace Syn\Framework\Classes;

use DateTimeZone;

class Timezone extends DateTimeZone
{
	public static function getList()
	{
		$zones = static::listIdentifiers();
		$return = [];
		foreach($zones as $zone)
			$return[] = ['id' => $zone, 'name' => $zone];

		return $return;
	}
}