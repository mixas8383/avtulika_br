<?php
/**
* @version		$Id: math.php 262 2012-01-16 17:52:00Z a.kikabidze $
* @package	LongCMS.Framework.WSLib
* @copyright	Copyright (C) 2009 - 2012 LongCMS Team. All rights reserved.
* @license		GNU General Public License version 2 or later
*/

defined('JPATH_PLATFORM') or die('Restricted access');

/**
 * LongCMS utilities Math class
 *
 * @static
 * @package		LongCMS.Platform
 * @subpackage	Utilities
 * @since	1.5
 */
abstract class JMath
{
	const ROUND_HALF_UP		= 1;
	const ROUND_HALF_DOWN	= 2;
	const ROUND_HALF_EVEN	= 3;
	const ROUND_HALF_ODD		= 4;

	/**
	 * @var  array  Valid byte units => power of 2 that defines the unit's size
	 */
	public static $byte_units = array
	(
		'B'   => 0,
		'K'   => 10,
		'Ki'  => 10,
		'KB'  => 10,
		'KiB' => 10,
		'M'   => 20,
		'Mi'  => 20,
		'MB'  => 20,
		'MiB' => 20,
		'G'   => 30,
		'Gi'  => 30,
		'GB'  => 30,
		'GiB' => 30,
		'T'   => 40,
		'Ti'  => 40,
		'TB'  => 40,
		'TiB' => 40,
		'P'   => 50,
		'Pi'  => 50,
		'PB'  => 50,
		'PiB' => 50,
		'E'   => 60,
		'Ei'  => 60,
		'EB'  => 60,
		'EiB' => 60,
		'Z'   => 70,
		'Zi'  => 70,
		'ZB'  => 70,
		'ZiB' => 70,
		'Y'   => 80,
		'Yi'  => 80,
		'YB'  => 80,
		'YiB' => 80,
	);

	/**
	 * Returns the English ordinal suffix (th, st, nd, etc) of a number.
	 *
	 *     echo 2, JMath::ordinal(2);   // "2nd"
	 *     echo 10, JMath::ordinal(10); // "10th"
	 *     echo 33, JMath::ordinal(33); // "33rd"
	 *
	 * @param   integer  number
	 * @return  string
	 */
	public static function ordinal($number)
	{
		if ($number % 100 > 10 AND $number % 100 < 14)
		{
			return 'th';
		}

		switch ($number % 10)
		{
			case 1:
				return 'st';
			case 2:
				return 'nd';
			case 3:
				return 'rd';
			default:
				return 'th';
		}
	}



	/**
	 * Locale-aware number and monetary formatting.
	 *
	 *     // In English, "1,200.05"
	 *     // In Spanish, "1200,05"
	 *     // In Portuguese, "1 200,05"
	 *     echo JMath::format(1200.05, 2);
	 *
	 *     // In English, "1,200.05"
	 *     // In Spanish, "1.200,05"
	 *     // In Portuguese, "1.200.05"
	 *     echo JMath::format(1200.05, 2, TRUE);
	 *
	 * @param   float    number to format
	 * @param   integer  decimal places
	 * @param   boolean  monetary formatting?
	 * @return  string
	 */
	public static function number_format($number, $places, $monetary = FALSE)
	{
		$info = localeconv();

		if ($monetary)
		{
			$decimal   = strlen($info['mon_decimal_point']) ? $info['mon_decimal_point'] : $info['decimal_point'];
			$thousands = strlen($info['mon_thousands_sep']) ? $info['mon_thousands_sep'] : $info['thousands_sep'];
		}
		else
		{
			$decimal   = $info['decimal_point'];
			$thousands = $info['thousands_sep'];
		}

		return number_format($number, $places, $decimal, $thousands);
	}



	/**
	 * Round a number to a specified precision, using a specified tie breaking technique
	 *
	 * @param float $value Number to round
	 * @param integer $precision Desired precision
	 * @param integer $mode Tie breaking mode, accepts the PHP_ROUND_HALF_* constants
	 * @param boolean $native Set to false to force use of the userland implementation
	 * @return float Rounded number
	 */
	public static function round($value, $precision = 0, $mode = self::ROUND_HALF_UP, $native = true)
	{
		if (version_compare(PHP_VERSION, '5.3', '>=') AND $native)
		{
			return round($value, $precision, $mode);
		}

		if ($mode === self::ROUND_HALF_UP)
		{
			return round($value, $precision);
		}
		else
		{
			$factor = ($precision === 0) ? 1 : pow(10, $precision);

			switch ($mode)
			{
				case self::ROUND_HALF_DOWN:
				case self::ROUND_HALF_EVEN:
				case self::ROUND_HALF_ODD:
					// Check if we have a rounding tie, otherwise we can just call round()
					if (($value * $factor) - floor($value * $factor) === 0.5)
					{
						if ($mode === self::ROUND_HALF_DOWN)
						{
							// Round down operation, so we round down unless the value
							// is -ve because up is down and down is up down there. ;)
							$up = ($value < 0);
						}
						else
						{
							// Round up if the integer is odd and the round mode is set to even
							// or the integer is even and the round mode is set to odd.
							// Any other instance round down.
							$up = ( ! ( ! (floor($value * $factor) & 1)) === ($mode === self::ROUND_HALF_EVEN));
						}

						if ($up)
						{
							$value = ceil($value * $factor);
						}
						else
						{
							$value = floor($value * $factor);
						}
						return $value / $factor;
					}
					else
					{
						return round($value, $precision);
					}
					break;
			}
		}
	}


	/**
	 * Converts a file size number to a byte value. File sizes are defined in
	 * the format: SB, where S is the size (1, 8.5, 300, etc.) and B is the
	 * byte unit (K, MiB, GB, etc.). All valid byte units are defined in
	 * JMath::$byte_units
	 *
	 *     echo JMath::bytes('200K');  // 204800
	 *     echo JMath::bytes('5MiB');  // 5242880
	 *     echo JMath::bytes('1000');  // 1000
	 *     echo JMath::bytes('2.5GB'); // 2684354560
	 *
	 * @param   string   file size in SB format
	 * @return  float
	 */
	public static function bytes($size)
	{
		jimport('core.utilities.array');
		// Prepare the size
		$size = trim( (string) $size);

		// Construct an OR list of byte units for the regex
		$accepted = implode('|', array_keys(Num::$byte_units));

		// Construct the regex pattern for verifying the size format
		$pattern = '/^([0-9]+(?:\.[0-9]+)?)('.$accepted.')?$/Di';

		// Verify the size format and store the matching parts
		if ( ! preg_match($pattern, $size, $matches))
		{
			return false;
		}
		// Find the float value of the size
		$size = (float) $matches[1];

		// Find the actual unit, assume B if no unit specified
		$unit = WSArray::get($matches, 2, 'B');

		// Convert the size into bytes
		$bytes = $size * pow(2, Num::$byte_units[$unit]);

		return $bytes;
	}






	/**
	 * Get number sgn
	 *
	 * @param $num
	 *
	 * @return 1 or -1
	 */
	public static function sgn($num)
	{
		return $num ? ($num > 0 ? 1 : -1) : 0;
	}

	public static function floor($num, $d)
	{
		return floor($num * pow(10, $d)) / pow(10, $d);
	}

	public static function ceil($num, $d)
	{
		return ceil($num * pow(10, $d)) / pow(10, $d);
	}

	public static function roundDown($num, $d = 0)
	{
		return self::sgn($num) * self::floor(abs($num), $d);
	}

	public static function roundUp($num, $d = 0)
	{
		// подтасовкой знака меняем направление округления
		// с плюс бесконечности на <от нуля>
		return self::sgn($num) * self::ceil(abs($num), $d);
	}

	public static function roundHalfDown($num, $d = 0)
	{
		// Если последняя цифра > 5
		// то округляем вверх
		// иначе округляем вниз
		return((2 * self::roundUp($num, $d) == self::roundUp(2 * $num, $d))
		? round($num, $d)
		: self::roundDown($num, $d));
	}

	public static function roundHalfEven($num, $d = 0)
	{
		// Если округляемая цифра !=5, то
		// всё ясно, округление стандартное
		if (round($num,$d) == self::roundHalfDown($num, $d))
		{
			return round($num, $d);
		}

		// Получаем предпоследнюю цифру
		$pre_digit = self::roundDown($num, $d);

		// Если она четная
		if (($pre_digit / 2) == self::roundDown($pre_digit / 2, $d))
		{
			// то округляем вниз
			return self::roundDown($num, $d);
		}
		else
		{
			// иначе округляем вверх
			return self::roundUp($num, $d);
		}
	}


	public static function random($type = 'alnum', $length = 8)
    {
        $utf8 = FALSE;

        switch ($type)
        {
            case 'alnum':
                $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            	break;
            case 'alpha':
                $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            	break;
            case 'hexdec':
                $pool = '0123456789abcdef';
            	break;
            case 'numeric':
                $pool = '0123456789';
            	break;
            case 'nozero':
                $pool = '123456789';
            	break;
            case 'distinct':
                $pool = '2345679ACDEFHJKLMNPRSTUVWXYZ';
            	break;
            default:
                $pool = (string) $type;
                $utf8 = ! utf8::is_ascii($pool);
            	break;
        }

        // Split the pool into an array of characters
        $pool = ($utf8 === TRUE) ? utf8::str_split($pool, 1) : str_split($pool, 1);

        // Largest pool key
        $max = count($pool) - 1;

        $str = '';
        for ($i = 0; $i < $length; $i++)
        {
            // Select a random character from the pool and add it to the string
            $str .= $pool[mt_rand(0, $max)];
        }

        // Make sure alnum strings contain at least one letter and one digit
        if ($type === 'alnum' && $length > 1)
        {
            if (ctype_alpha($str))
            {
                // Add a random digit
                $str[mt_rand(0, $length - 1)] = chr(mt_rand(48, 57));
            }
            else if (ctype_digit($str))
            {
                // Add a random letter
                $str[mt_rand(0, $length - 1)] = chr(mt_rand(65, 90));
            }
        }

        return $str;
    }



}

