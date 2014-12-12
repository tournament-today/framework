<?php namespace Syn\Framework\Classes;

use Carbon\Carbon;

class Timing
{
	public static function remaining(Carbon $datetime)
	{
		if($datetime->isPast())
			return trans('time.past');

		return static::difference(Carbon::now(), $datetime);
	}

	public static function ago(Carbon $datetime)
	{
		if($datetime->isFuture())
			return trans('time.future');

		return static::difference(Carbon::now(), $datetime);
	}

	public static function difference(Carbon $dt1, Carbon $dt2)
	{

		if($dt2 > $dt1)
		{
			$first = $dt2;
			$last = $dt1;
		}
		else
		{
			$first = $dt1;
			$last = $dt2;
		}

		if($last -> diffInMonths($first) > 2)
			return sprintf("%d %s",
				$last -> diffInMonths($first),
				trans_choice('time.month', $last -> diffInMonths($first))
			);
		elseif($last -> diffInWeeks($first) > 2)
			return sprintf("%d %s",
				$last -> diffInWeeks($first),
				trans_choice('time.week', $last -> diffInWeeks($first))
			);
		elseif($last -> diffInDays($first) > 2)
			return sprintf("%d %s",
				$last -> diffInDays($first),
				trans_choice('time.day', $last -> diffInDays($first))
			);
		elseif($last -> diffInHours($first) > 2)
			return sprintf("%d %s",
				$last -> diffInHours($first),
				trans_choice('time.hour', $last -> diffInHours($first))
			);
		elseif($last -> diffInMinutes($first) > 2)
			return sprintf("%d %s",
				$last -> diffInMinutes($first),
				trans_choice('time.minute', $last -> diffInMinutes($first))
			);
		elseif($last -> diffInSeconds($first) > 1)
			return sprintf("%d %s",
				$last -> diffInSeconds($first),
				trans_choice('time.second', $last -> diffInSeconds($first))
			);
		elseif($last -> diffInSeconds($first) == 0)
			return trans('time.now');
	}
}