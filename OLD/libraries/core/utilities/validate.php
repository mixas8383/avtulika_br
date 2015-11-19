<?php
/**
* @version		$Id: validate.php 262 2012-01-16 17:52:00Z a.kikabidze $
* @package	LongCMS.Framework.WSLib
* @copyright	Copyright (C) 2009 - 2012 LongCMS Team. All rights reserved.
* @license		GNU General Public License version 2 or later
*/

defined('JPATH_PLATFORM') or die('Restricted access');



 // check if php version is 5.2 or greatest (for filter_var function)
if (version_compare(phpversion(), '5.2') < 0)
{
	define('WS_PHP52', false);
}
else
{
	define('WS_PHP52', true);
}



/**
 * LongCMS WS Lib Validator class
 *
 * @static
 * @package		LongCMS.WSLib
 * @subpackage	Utilities
 * @since	1.5
 */
abstract class JValidate
{

    /**
     * Check if string is valid E-mail address
     *
     * Returns $value if and only if $value contains only valid E-mail address
     *
     * @param  string $value		string
     * @param  mixed $default	default value to return if check failed
     * @return mixed
     */
	public static function mail($value, $default = false)
	{
        if (!is_string($value))
		{
            return $default;
        }

		if (WS_PHP52)
		{
			$options = array(
				'options' => array(
					'default' => $default // value to return if the filter fails
				)
			);
			$result = filter_var($value, FILTER_VALIDATE_EMAIL, $options);
		}
		else
		{
			// Check Length
			$len = strlen(trim($value));
			if ($len < 5 || $len > 255)
			{
				return $default;
			}

			if (preg_match('#^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$#is', $value))
			{
				$result = $value;
			}
			else
			{
				$result = $default;
			}
		}
		return $result;
	}



    /**
     * Check if string is valid URL address
     *
     * Returns $value if and only if $value contains only valid URL address
     *
     * @param  string $value		string
     * @param  mixed $default	default value to return if check failed
     * @return mixed
     */
	public static function URL($value, $default = false)
	{
        if (!is_string($value))
		{
            return $default;
        }

		if (WS_PHP52)
		{
			$options = array(
				'options' => array(
					'default' => $default // value to return if the filter fails
				),
				'flags' => FILTER_FLAG_HOST_REQUIRED
			);
			$result = filter_var($value, FILTER_VALIDATE_URL, $options);
		}
		else
		{
			// Check Length
			$len = strlen(trim($value));
			if ($len < 4 || $len > 255)
			{
				return $default;
			}

			if (preg_match('#^http[s]?://[a-z0-9\-]+\.([a-z0-9\-]+\.)?[a-z]+#is', $value))
			{
				$result = $value;
			}
			else
			{
				$result = $default;
			}
		}
		return $result;

	}



    /**
     * Check if number in range min and max
     *
     * Returns $value if and only if $value in range min and max
     *
     * @param  int $value		number for check
     * @param  int $value		range min
     * @param  int $value		range max
     * @param  mixed $default	default value to return if check failed
     * @return mixed
     */
	public static function range($value, $min = null, $max = null, $default = false)
	{
        if (!is_string($value) && !is_int($value) && !is_float($value))
		{
            return $default;
        }


		if ($min === null)
		{
			$min = -2147483647;
		}
		if ($max === null)
		{
			$max = 2147483647;
		}

		$value = (int)$value;
		$min = (int)$min;
		$max = (int)$max;

		if (WS_PHP52)
		{
			$options = array(
				'options' => array(
					'default' => $default, // value to return if the filter fails
					// other options here
					'min_range' => $min,
					'max_range' => $max,
				)
			);

			$result = filter_var($value, FILTER_VALIDATE_INT, $options);
		}
		else
		{
			if ($value >= $min && $value <= $max)
			{
				$result = $value;
			}
			else
			{
				$result = $default;
			}
		}
		return $result;

	}


    /**
     * Check if string is valid IP address
     *
     * Returns $value if and only if $value contains only valid IP address
     *
     * @param  string $value		string
     * @param  mixed $default	default value to return if check failed
     * @return mixed
     */
	public static function IP($value, $default = false)
	{
        if (!is_string($value))
		{
            return $default;
        }
		if (WS_PHP52)
		{
			$options = array(
				'options' => array(
					'default' => $default // value to return if the filter fails
				)
			);

			$result = filter_var($value, FILTER_VALIDATE_IP, $options);
		}
		else
		{
			// Check Length
			$len = strlen(trim($value));
			if ($len < 15 || $len > 255)
			{
				return $default;
			}

			if (preg_match('#^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$#', $value))
			{
				$result = $value;
			}
			else
			{
				$result = $default;
			}
		}
		return $result;
	}


    /**
     * Check if string is valid date format
     *
     * Returns $value if and only if $value contains only valid date format
     *
     * @param  string $value		string
     * @param  mixed $default	default value to return if check failed
     * @return mixed
     */
	public static function date($value, $default = false)
	{
        if (!is_string($value))
		{
            return $default;
        }
		// Check Length
		$len = strlen(trim($value));
		if ($len !== 10)
		{
			return $default;
		}

		preg_match('#^(\d{4})-(\d{2})-(\d{2})$#', $value, $matches);

		if (!$matches)
		{
			return $default;
		}

		$date = checkdate($matches[2], $matches[3], $matches[1]);
		if (!$date)
		{
			return $default;
		}
		return $value;
	}


    /**
     * Check if string is valid datetime format
     *
     * Returns $value if and only if $value contains only valid datetime format
     *
     * @param  string $value		string
     * @param  mixed $default	default value to return if check failed
     * @return mixed
     */
	public static function dateTime($value, $default = false)
	{
        if (!is_string($value))
		{
            return $default;
        }
		// Check Length
		$len = strlen(trim($value));
		if ($len !== 19)
		{
			return $default;
		}


		preg_match('#^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$#', $value, $matches);

		if (!$matches)
		{
			return $default;
		}

		$date = checkdate($matches[2], $matches[3], $matches[1]);
		if (!$date)
		{
			return $default;
		}



		if ($matches[4] < 0 || $matches[4] > 23)
		{
			return $default;
		}

		if ($matches[5] < 0 || $matches[5] > 59)
		{
			return $default;
		}

		if ($matches[6] < 0 || $matches[6] > 59)
		{
			return $default;
		}

		return $value;
	}

    /**
     * Check if string is valid RGB color
     *
     * Returns $value if and only if $value contains only RGB color characters
     *
     * @param  string $value		string
     * @param  mixed $default	default value to return if check failed
     * @return mixed
     */
	public static function RGB($value, $default = false)
	{
        if (!is_string($value) && !is_int($value))
		{
            return $default;
        }

		// Check Length
		$len = strlen(trim($value));
		if ($len > 7 | $len < 3)
		{
			return $default;
		}

		if (!preg_match("#(^\#?[0-9a-f]{3}$)|(^\#?[0-9a-f]{6}$)#i", $value))
		{
			return $default;
		}
		return $value;
	}



    /**
     * Check if string is hexdecimal
     *
     * Returns $value if and only if $value contains only hexadecimal digit characters
     *
     * @param  string $value		string
     * @param  mixed $default	default value to return if check failed
     * @return mixed
     */
	public static function hex($value, $default = false)
	{
        if (!is_string($value) && !is_int($value))
		{
            return $default;
        }

        if (!ctype_xdigit((string) $value))
		{
            return $default;
        }
		return $value;
	}




}
