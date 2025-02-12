<?php
/**
 * @package     	LongCMS.Platform
 * @subpackage  Utilities
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * JSimpleCrypt is a very simple encryption algorithm for encrypting/decrypting strings
 *
 * @package     	LongCMS.Platform
 * @subpackage  Utilities
 * @since       11.1
 * @deprecated  12.3  Use JCrypt instead.
 */
class JSimpleCrypt extends JObject
{
	/**
	 * Encryption/Decryption Key
	 *
	 * @var    string
	 */
	protected $_key;

	/**
	 * Object Constructor takes an optional key to be used for encryption/decryption. If no key is given then the
	 * secret word from the configuration object is used.
	 *
	 * @param   string  $key  Optional encryption key
	 *
	 * @since   11.1
	 * @deprecated  12.3  Use JCrypt instead.
	 */
	public function __construct($key = null)
	{
		JLog::add('JSimpleCrypt is deprecated. Use JCrypt instead.', JLog::WARNING, 'deprecated');

		if ($key)
		{
			$this->_key = (string) $key;
		}
		else
		{
			$conf = &JFactory::getConfig();
			$this->_key = md5($conf->get('secret'));
		}
	}

	/**
	 * Decrypt a string
	 *
	 * @param   string  $s  String to decrypt
	 *
	 * @return  string
	 *
	 * @since   11.1
	 * @deprecated  12.3  Use JCrypt instead.
	 */
	public function decrypt($s)
	{
		$ai = $this->_hexToIntArray($s);
		(string) $s1 = $this->_xorString($ai);

		return $s1;
	}

	/**
	 * Encrypt a string
	 *
	 * @param   string  $s  String to encrypt
	 *
	 * @return  string
	 *
	 * @since   11.1
	 * @deprecated  12.3  Use JCrypt instead.
	 */
	public function encrypt($s)
	{
		$ai = $this->_xorCharString($s);
		$s1 = '';

		for ($i = 0, $count = count($ai); $i < $count; $i++)
		{
			$s1 = $s1 . $this->_intToHex((int) $ai[$i]);
		}

		return $s1;
	}

	/**
	 * Convert hex to an integer
	 *
	 * @param   string   $s  The hex string to convert.
	 * @param   integer  $i  The offset?
	 *
	 * @return  integer
	 *
	 * @since   11.1
	 * @deprecated  12.3  Use JCrypt instead.
	 */
	protected function _hexToInt($s, $i)
	{
		(int) $j = $i * 2;
		(string) $s1 = $s;
		(string) $c = substr($s1, $j, 1); // get the char at position $j, length 1
		(string) $c1 = substr($s1, $j + 1, 1); // get the char at postion $j + 1, length 1
		(int) $k = 0;

		switch ($c)
		{
			case "A":
				$k += 160;
				break;
			case "B":
				$k += 176;
				break;
			case "C":
				$k += 192;
				break;
			case "D":
				$k += 208;
				break;
			case "E":
				$k += 224;
				break;
			case "F":
				$k += 240;
				break;
			case " ":
				$k += 0;
				break;
			default:
				(int) $k = $k + (16 * (int) $c);
				break;
		}

		switch ($c1)
		{
			case "A":
				$k += 10;
				break;
			case "B":
				$k += 11;
				break;
			case "C":
				$k += 12;
				break;
			case "D":
				$k += 13;
				break;
			case "E":
				$k += 14;
				break;
			case "F":
				$k += 15;
				break;
			case " ":
				$k += 0;
				break;
			default:
				$k += (int) $c1;
				break;
		}

		return $k;
	}
	/**
	 * Convert hex to an array of integers
	 *
	 * @param   string  $s  The hex string to convert to an integer array.
	 *
	 * @return  array  An array of integers.
	 *
	 * @since   11.1
	 * @deprecated  12.3  Use JCrypt instead.
	 */
	protected function _hexToIntArray($s)
	{
		(string) $s1 = $s;
		(int) $i = strlen($s1);
		(int) $j = $i / 2;
		for ($l = 0; $l < $j; $l++)
		{
			(int) $k = $this->_hexToInt($s1, $l);
			$ai[$l] = $k;
		}

		return $ai;
	}

	/**
	 * Convert character string to integer
	 *
	 * @param   string  $c  The character to convert to an integer.
	 *
	 * @return  integer
	 *
	 * @since   11.1
	 * @deprecated  12.3  Use JCrypt instead.
	 */
	protected function _charToInt($c)
	{
		$ac[0] = $c;

		return $ac;
	}

	/**
	 * XorString
	 *
	 * @param   string  $ai  The string.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 * @deprecated  12.3  Use JCrypt instead.
	 */
	protected function _xorString($ai)
	{
		$s = $this->_key;
		(int) $i = strlen($s);
		$ai1 = $ai;
		(int) $j = count($ai1);
		for ($i = 0; $i < $j; $i = strlen($s))
		{
			$s = $s . $s;
		}

		for ($k = 0; $k < $j; $k++)
		{
			(string) $c = substr($s, $k, 1);
			$ac[$k] = chr($ai1[$k] ^ ord($c));
		}

		(string) $s1 = implode('', $ac);
		return $s1;
	}

	/**
	 * Convert integer to hex
	 *
	 * @param   integer  $i  An integer value to convert.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 * @deprecated  12.3  Use JCrypt instead.
	 */
	protected function _intToHex($i)
	{
		(int) $j = (int) $i / 16;
		if ((int) $j == 0)
		{
			(string) $s = " ";
		}
		else
		{
			(string) $s = strtoupper(dechex($j));
		}
		(int) $k = (int) $i - (int) $j * 16;
		(string) $s = $s . strtoupper(dechex($k));

		return $s;
	}

	/**
	 * Use xor encryption
	 *
	 * @param   string  $s  The string.
	 *
	 * @return  array  An array of integers
	 *
	 * @since   11.1
	 * @deprecated  12.3  Use JCrypt instead.
	 */
	protected function _xorCharString($s)
	{
		$ac = preg_split('//', $s, -1, PREG_SPLIT_NO_EMPTY);
		(string) $s1 = $this->_key;
		(int) $i = strlen($s1);
		(int) $j = count($ac);
		for ($i = 0; $i < $j; $i = strlen($s1))
		{
			$s1 = $s1 . $s1;
		}

		for ($k = 0; $k < $j; $k++)
		{
			$c = substr($s1, $k, 1);
			$ai[$k] = ord($c) ^ ord($ac[$k]);
		}

		return $ai;
	}
}
