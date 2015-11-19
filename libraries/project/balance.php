<?php
/**
 * @package    LongCMS.Platform
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die('Restricted access');

/**
 * LongCMS Platform Factory class
 *
 * @package  LongCMS.Platform
 * @since    11.1
 */
abstract class Balance
{

	// convert dollars in cents
	public static function convertAsMinor($sum)
	{
		if ($sum == 0) {
			return 0;
		}
		$sum = str_replace(array(',', ' '), array('.', ''), trim($sum));
		//$sum = (float)$sum;
		$sum *= 100;
		$return = $sum;
		return $return;
	}


	// convert cents in dollars
	public static function convertAsMajor($cents)
	{
		if ($cents == 0) {
			return '0.00';
		}
		//$cents = intval($cents);
		$return = $cents / 100;
		return self::format($return);
	}


	// format amount as 0.00
	public static function format($amount = 0, $d = 2)
	{
		$amount = str_replace(array(',', ' '), array('.', ''), trim($amount));
		$amount = (float)$amount;
		$amount = ($amount * 100) / 100;
		$amount = number_format($amount, $d, '.', '');
		return $amount;
	}

}
