<?php

class Office_Functions_Helper_Data extends Mage_Core_Helper_Abstract
{
	// calculate facebook like loggedin time by providing unix timestamp
	public function CalculateTime($time) {
		$time = time() - $time; // to get the time since that moment
	
		$tokens = array (
			31536000 => 'year',
			2592000 => 'month',
			604800 => 'week',
			86400 => 'day',
			3600 => 'hour',
			60 => 'minute',
			1 => 'second'
		);
	
		foreach ($tokens as $unit => $text) {
			if ($time < $unit) continue;
			$numberOfUnits = floor($time / $unit);
			return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
		}
	}

}