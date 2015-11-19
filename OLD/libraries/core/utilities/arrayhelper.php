<?php
/**
 * @package     	LongCMS.Platform
 * @subpackage  Utilities
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die('Restricted access');

/**
 * JArrayHelper is an array utility class for doing all sorts of odds and ends with arrays.
 *
 * @package     	LongCMS.Platform
 * @subpackage  Utilities
 * @since       11.1
 */
class JArrayHelper
{
	/**
	 * Option to perform case-sensitive sorts.
	 *
	 * @var    mixed  Boolean or array of booleans.
	 * @since  11.3
	 */
	protected static $sortCase;

	/**
	 * Option to set the sort direction.
	 *
	 * @var    mixed  Integer or array of integers.
	 * @since  11.3
	 */
	protected static $sortDirection;

	/**
	 * Option to set the object key to sort on.
	 *
	 * @var    string
	 * @since  11.3
	 */
	protected static $sortKey;

	/**
	 * Option to perform a language aware sort.
	 *
	 * @var    mixed  Boolean or array of booleans.
	 * @since  11.3
	 */
	protected static $sortLocale;

	/**
	 * Function to convert array to integer values
	 *
	 * @param   array  &$array   The source array to convert
	 * @param   mixed  $default  A default value (int|array) to assign if $array is not an array
	 * @param   bool	   $removeZeros	Remove or not zero elements from array
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public static function toInteger(&$array, $default = null, $removeZeros = false)
	{
		if (is_array($array))
		{
			foreach ($array as $i => $v)
			{
				$val = (int) $v;
				if ($removeZeros && $val === 0)
				{
					unset($array[$i]);
					continue;
				}
				$array[$i] = $val;
			}
		}
		else
		{
			if ($default === null)
			{
				$array = array();
			}
			elseif (is_array($default))
			{
				self::toInteger($default, null, $removeZeros);
				$array = $default;
			}
			else
			{
				$array = array((int) $default);
			}
		}
	}

	/**
	 * Utility function to map an array to a stdClass object.
	 *
	 * @param   array   &$array  The array to map.
	 * @param   string  $class   Name of the class to create
	 *
	 * @return  object   The object mapped from the given array
	 *
	 * @since   11.1
	 */
	public static function toObject(&$array, $class = 'stdClass')
	{
		$obj = null;
		if (is_array($array))
		{
			$obj = new $class;
			foreach ($array as $k => $v)
			{
				if (is_array($v))
				{
					$obj->$k = self::toObject($v, $class);
				}
				else
				{
					$obj->$k = $v;
				}
			}
		}
		return $obj;
	}

	/**
	 * Utility function to map an array to a string.
	 *
	 * @param   array    $array         The array to map.
	 * @param   string   $inner_glue    The glue (optional, defaults to '=') between the key and the value.
	 * @param   string   $outer_glue    The glue (optional, defaults to ' ') between array elements.
	 * @param   boolean  $keepOuterKey  True if final key should be kept.
	 *
	 * @return  string   The string mapped from the given array
	 *
	 * @since   11.1
	 */
	public static function toString($array = null, $inner_glue = '=', $outer_glue = ' ', $keepOuterKey = false)
	{
		$output = array();

		if (is_array($array))
		{
			foreach ($array as $key => $item)
			{
				if (is_array($item))
				{
					if ($keepOuterKey)
					{
						$output[] = $key;
					}
					// This is value is an array, go and do it again!
					$output[] = self::toString($item, $inner_glue, $outer_glue, $keepOuterKey);
				}
				else
				{
					$output[] = $key . $inner_glue . '"' . $item . '"';
				}
			}
		}

		return implode($outer_glue, $output);
	}

	/**
	 * Utility function to map an object to an array
	 *
	 * @param   object   $p_obj    The source object
	 * @param   boolean  $recurse  True to recurse through multi-level objects
	 * @param   string   $regex    An optional regular expression to match on field names
	 *
	 * @return  array    The array mapped from the given object
	 *
	 * @since   11.1
	 */
	public static function fromObject($p_obj, $recurse = true, $regex = null)
	{
		if (is_object($p_obj))
		{
			return self::_fromObject($p_obj, $recurse, $regex);
		}
		else
		{
			return null;
		}
	}

	/**
	 * Utility function to map an object or array to an array
	 *
	 * @param   mixed    $item     The source object or array
	 * @param   boolean  $recurse  True to recurse through multi-level objects
	 * @param   string   $regex    An optional regular expression to match on field names
	 *
	 * @return  array  The array mapped from the given object
	 *
	 * @since   11.1
	 */
	protected static function _fromObject($item, $recurse, $regex)
	{
		if (is_object($item))
		{
			$result = array();
			foreach (get_object_vars($item) as $k => $v)
			{
				if (!$regex || preg_match($regex, $k))
				{
					if ($recurse)
					{
						$result[$k] = self::_fromObject($v, $recurse, $regex);
					}
					else
					{
						$result[$k] = $v;
					}
				}
			}
		}
		elseif (is_array($item))
		{
			$result = array();
			foreach ($item as $k => $v)
			{
				$result[$k] = self::_fromObject($v, $recurse, $regex);
			}
		}
		else
		{
			$result = $item;
		}
		return $result;
	}

	/**
	 * Extracts a column from an array of arrays or objects
	 *
	 * @param   array   &$array  The source array
	 * @param   string  $index   The index of the column or name of object property
	 *
	 * @return  array  Column of values from the source array
	 *
	 * @since   11.1
	 */
	public static function getColumn(&$array, $index)
	{
		$result = array();

		if (is_array($array))
		{
			$n = count($array);

			for ($i = 0; $i < $n; $i++)
			{
				$item = &$array[$i];

				if (is_array($item) && isset($item[$index]))
				{
					$result[] = $item[$index];
				}
				elseif (is_object($item) && isset($item->$index))
				{
					$result[] = $item->$index;
				}
				// else ignore the entry
			}
		}
		return $result;
	}

	/**
	 * Utility function to return a value from a named array or a specified default
	 *
	 * @param   array   &$array   A named array
	 * @param   string  $name     The key to search for
	 * @param   mixed   $default  The default value to give if no key found
	 * @param   string  $type     Return type for the variable (INT, FLOAT, STRING, WORD, BOOLEAN, ARRAY)
	 *
	 * @return  mixed  The value from the source array
	 *
	 * @since   11.1
	 */
	public static function getValue(&$array, $name, $default = null, $type = '')
	{
		// Initialise variables.
		$result = null;

		if (isset($array[$name]))
		{
			$result = $array[$name];
		}

		// Handle the default case
		if (is_null($result))
		{
			$result = $default;
		}

		// Handle the type constraint
		switch (strtoupper($type))
		{
			case 'UINT':
				// Only use the first integer value
				preg_match('/-?[0-9]+/', $result, $matches);
				$result = @abs((int) $matches[0]);
				break;

			case 'INT':
			case 'INTEGER':
				// Only use the first integer value
				@preg_match('/-?[0-9]+/', $result, $matches);
				$result = @(int) $matches[0];
				break;

			case 'FLOAT':
			case 'DOUBLE':
				// Only use the first floating point value
				@preg_match('/-?[0-9]+(\.[0-9]+)?/', $result, $matches);
				$result = @(float) $matches[0];
				break;

			case 'BOOL':
			case 'BOOLEAN':
				$result = (bool) $result;
				break;

			case 'ARRAY':
				if (!is_array($result))
				{
					$result = array($result);
				}
				break;

			case 'STRING':
				$result = (string) $result;
				break;

			case 'WORD':
				$result = (string) preg_replace('#\W#', '', $result);
				break;

			case 'NONE':
			default:
				// No casting necessary
				break;
		}
		return $result;
	}

	/**
	 * Method to determine if an array is an associative array.
	 *
	 * @param   array  $array  An array to test.
	 *
	 * @return  boolean  True if the array is an associative array.
	 *
	 * @since   11.1
	 */
	public static function isAssociative($array)
	{
		if (is_array($array))
		{
			foreach (array_keys($array) as $k => $v)
			{
				if ($k !== $v)
				{
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Pivots an array to create a reverse lookup of an array of scalars, arrays or objects.
	 *
	 * @param   array   $source  The source array.
	 * @param   string  $key     Where the elements of the source array are objects or arrays, the key to pivot on.
	 *
	 * @return  array  An array of arrays pivoted either on the value of the keys, or an individual key of an object or array.
	 *
	 * @since   11.3
	 */
	public static function pivot($source, $key = null)
	{
		$result = array();
		$counter = array();

		foreach ($source as $index => $value)
		{
			// Determine the name of the pivot key, and its value.
			if (is_array($value))
			{
				// If the key does not exist, ignore it.
				if (!isset($value[$key]))
				{
					continue;
				}

				$resultKey = $value[$key];
				$resultValue = &$source[$index];
			}
			elseif (is_object($value))
			{
				// If the key does not exist, ignore it.
				if (!isset($value->$key))
				{
					continue;
				}

				$resultKey = $value->$key;
				$resultValue = &$source[$index];
			}
			else
			{
				// Just a scalar value.
				$resultKey = $value;
				$resultValue = $index;
			}

			// The counter tracks how many times a key has been used.
			if (empty($counter[$resultKey]))
			{
				// The first time around we just assign the value to the key.
				$result[$resultKey] = $resultValue;
				$counter[$resultKey] = 1;
			}
			elseif ($counter[$resultKey] == 1)
			{
				// If there is a second time, we convert the value into an array.
				$result[$resultKey] = array(
					$result[$resultKey],
					$resultValue,
				);
				$counter[$resultKey]++;
			}
			else
			{
				// After the second time, no need to track any more. Just append to the existing array.
				$result[$resultKey][] = $resultValue;
			}
		}

		unset($counter);

		return $result;
	}

	/**
	 * Utility function to sort an array of objects on a given field
	 *
	 * @param   array  &$a             An array of objects
	 * @param   mixed  $k              The key (string) or a array of key to sort on
	 * @param   mixed  $direction      Direction (integer) or an array of direction to sort in [1 = Ascending] [-1 = Descending]
	 * @param   mixed  $caseSensitive  Boolean or array of booleans to let sort occur case sensitive or insensitive
	 * @param   mixed  $locale         Boolean or array of booleans to let sort occur using the locale language or not
	 *
	 * @return  array  The sorted array of objects
	 *
	 * @since   11.1
	 */
	public static function sortObjects(&$a, $k, $direction = 1, $caseSensitive = true, $locale = false)
	{
		if (!is_array($locale) or !is_array($locale[0]))
		{
			$locale = array($locale);
		}

		self::$sortCase = (array) $caseSensitive;
		self::$sortDirection = (array) $direction;
		self::$sortKey = (array) $k;
		self::$sortLocale = $locale;

		usort($a, array(__CLASS__, '_sortObjects'));

		self::$sortCase = null;
		self::$sortDirection = null;
		self::$sortKey = null;
		self::$sortLocale = null;

		return $a;
	}

	/**
	 * Callback function for sorting an array of objects on a key
	 *
	 * @param   array  &$a  An array of objects
	 * @param   array  &$b  An array of objects
	 *
	 * @return  integer  Comparison status
	 *
	 * @see     JArrayHelper::sortObjects()
	 * @since   11.1
	 */
	protected static function _sortObjects(&$a, &$b)
	{
		$key = self::$sortKey;

		for ($i = 0, $count = count($key); $i < $count; $i++)
		{
			if (isset(self::$sortDirection[$i]))
			{
				$direction = self::$sortDirection[$i];
			}

			if (isset(self::$sortCase[$i]))
			{
				$caseSensitive = self::$sortCase[$i];
			}

			if (isset(self::$sortLocale[$i]))
			{
				$locale = self::$sortLocale[$i];
			}

			$va = $a->$key[$i];
			$vb = $b->$key[$i];

			if ((is_bool($va) or is_numeric($va)) and (is_bool($vb) or is_numeric($vb)))
			{
				$cmp = $va - $vb;
			}
			elseif ($caseSensitive)
			{
				$cmp = JString::strcmp($va, $vb, $locale);
			}
			else
			{
				$cmp = JString::strcasecmp($va, $vb, $locale);
			}

			if ($cmp > 0)
			{

				return $direction;
			}

			if ($cmp < 0)
			{
				return -$direction;
			}
		}

		return 0;
	}

	/**
	 * Multidimensional array safe unique test
	 *
	 * @param   array  $myArray  The array to make unique.
	 *
	 * @return  array
	 *
	 * @see     http://php.net/manual/en/function.array-unique.php
	 * @since   11.2
	 */
	public static function arrayUnique($myArray)
	{
		if (!is_array($myArray))
		{
			return $myArray;
		}

		foreach ($myArray as &$myvalue)
		{
			$myvalue = serialize($myvalue);
		}

		$myArray = array_unique($myArray);

		foreach ($myArray as &$myvalue)
		{
			$myvalue = unserialize($myvalue);
		}

		return $myArray;
	}

	/**
	 * Convert a multi-dimensional array into a single-dimensional array.
	 *
	 *     $array = array('set' => array('one' => 'something'), 'two' => 'other');
	 *
	 *     // Flatten the array
	 *     $array = JArrayHelper::flatten($array);
	 *
	 *     // The array will now be
	 *     array('one' => 'something', 'two' => 'other');
	 *
	 * [!!] The keys of array values will be discarded.
	 *
	 * @param   array   array to flatten
	 * @return  array
	 */
	public static function flatten(array $array)
	{
		$flat = array();
		foreach ($array as $key => $value)
		{
			if (is_array($value))
			{
				$flat += self::flatten($value);
			}
			else
			{
				$flat[$key] = $value;
			}
		}
		return $flat;
	}



	/**
	 * Creates a callable function and parameter list from a string representation.
	 * Note that this function does not validate the callback string.
	 *
	 *     // Get the callback function and parameters
	 *     list($func, $params) = JArrayHelper::callback('Foo::bar(apple,orange)');
	 *
	 *     // Get the result of the callback
	 *     $result = call_user_func_array($func, $params);
	 *
	 * @param   string  callback string
	 * @return  array   function, params
	 */
	public static function callback($str)
	{
		// Overloaded as parts are found
		$command = $params = NULL;

		// command[param,param]
		if (preg_match('/^([^\(]*+)\((.*)\)$/', $str, $match))
		{
			// command
			$command = $match[1];

			if ($match[2] !== '')
			{
				// param,param
				$params = preg_split('/(?<!\\\\),/', $match[2]);
				$params = str_replace('\,', ',', $params);
			}
		}
		else
		{
			// command
			$command = $str;
		}

		if (strpos($command, '::') !== FALSE)
		{
			// Create a static method callable command
			$command = explode('::', $command, 2);
		}

		return array($command, $params);
	}


	/**
	 * Overwrites an array with values from input arrays.
	 * Keys that do not exist in the first array will not be added!
	 *
	 *     $a1 = array('name' => 'john', 'mood' => 'happy', 'food' => 'bacon');
	 *     $a2 = array('name' => 'jack', 'food' => 'tacos', 'drink' => 'beer');
	 *
	 *     // Overwrite the values of $a1 with $a2
	 *     $array = JArrayHelper::overwrite($a1, $a2);
	 *
	 *     // The output of $array will now be:
	 *     array('name' => 'jack', 'mood' => 'happy', 'food' => 'tacos')
	 *
	 * @param   array   master array
	 * @param   array   input arrays that will overwrite existing values
	 * @return  array
	 */
	public static function overwrite(array $array1, array $array2)
	{
		foreach (array_intersect_key($array2, $array1) as $key => $value)
		{
			$array1[$key] = $value;
		}

		if (func_num_args() > 2)
		{
			foreach (array_slice(func_get_args(), 2) as $array2)
			{
				foreach (array_intersect_key($array2, $array1) as $key => $value)
				{
					$array1[$key] = $value;
				}
			}
		}

		return $array1;
	}


	/**
	 * Merges one or more arrays recursively and preserves all keys.
	 * Note that this does not work the same as [array_merge_recursive](http://php.net/array_merge_recursive)!
	 *
	 *     $john = array('name' => 'john', 'children' => array('fred', 'paul', 'sally', 'jane'));
	 *     $mary = array('name' => 'mary', 'children' => array('jane'));
	 *
	 *     // John and Mary are married, merge them together
	 *     $john = JArrayHelper::merge($john, $mary);
	 *
	 *     // The output of $john will now be:
	 *     array('name' => 'mary', 'children' => array('fred', 'paul', 'sally', 'jane'))
	 *
	 * @param   array  initial array
	 * @param   array  array to merge
	 * @param   array  ...
	 * @return  array
	 */
	public static function merge(array $a1, array $a2)
	{
		$result = array();
		for ($i = 0, $total = func_num_args(); $i < $total; $i++)
		{
			// Get the next array
			$arr = func_get_arg($i);

			// Is the array associative?
			$assoc = self::isAssociative($arr);

			foreach ($arr as $key => $val)
			{
				if (isset($result[$key]))
				{
					if (is_array($val) AND is_array($result[$key]))
					{
						if (self::isAssociative($val))
						{
							// Associative arrays are merged recursively
							$result[$key] = self::merge($result[$key], $val);
						}
						else
						{
							// Find the values that are not already present
							$diff = array_diff($val, $result[$key]);

							// Indexed arrays are merged to prevent duplicates
							$result[$key] = array_merge($result[$key], $diff);
						}
					}
					else
					{
						if ($assoc)
						{
							// Associative values are replaced
							$result[$key] = $val;
						}
						elseif ( ! in_array($val, $result, TRUE))
						{
							// Indexed values are added only if they do not yet exist
							$result[] = $val;
						}
					}
				}
				else
				{
					// New values are added
					$result[$key] = $val;
				}
			}
		}

		return $result;
	}

	/**
	 * Recursive version of [array_map](http://php.net/array_map), applies the
	 * same callback to all elements in an array, including sub-arrays.
	 *
	 *     // Apply "strip_tags" to every element in the array
	 *     $array = JArrayHelper::map('strip_tags', $array);
	 *
	 * [!!] Unlike `array_map`, this method requires a callback and will only map
	 * a single array.
	 *
	 * @param   mixed   callback applied to every element in the array
	 * @param   array   array to map
	 * @param   array   array of keys to apply to
	 * @return  array
	 */
	public static function map($callback, array $array, $keys = NULL)
	{
		foreach ($array as $key => $val)
		{
			if (is_array($val))
			{
				$array[$key] = self::map($callback, $array[$key]);
			}
			elseif ( ! is_array($keys) or in_array($key, $keys))
			{
				if (is_array($callback))
				{
					foreach ($callback as $cb)
					{
						$array[$key] = call_user_func($cb, $array[$key]);
					}
				}
				else
				{
					$array[$key] = call_user_func($callback, $array[$key]);
				}
			}
		}

		return $array;
	}

	/**
	 * Fill an array with a range of numbers.
	 *
	 *     // Fill an array with values 5, 10, 15, 20
	 *     $values = WSArray::range(5, 20);
	 *
	 * @param   integer  stepping
	 * @param   integer  ending number
	 * @return  array
	 */
	public static function range($step = 10, $max = 100)
	{
		if ($step < 1)
		{
			return array();
		}
		$array = array();
		for ($i = $step; $i <= $max; $i += $step)
		{
			$array[$i] = $i;
		}

		return $array;
	}


}
