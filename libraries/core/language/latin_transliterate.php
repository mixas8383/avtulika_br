<?php
/**
 * @package     	LongCMS.Platform
 * @subpackage  Language
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * @package     	LongCMS.Platform
 * @subpackage  Language
 * @since   11.1
 */

/**
 * Class to transliterate strings
 *
 * @package     	LongCMS.Platform
 * @subpackage  Language
 * @since       11.1
 */
class JLanguageTransliterate
{
	private static $_letters_geo = array('ა', 'ბ', 'გ', 'დ', 'ე', 'ვ', 'ზ', 'თ', 'ი', 'კ', 'ლ', 'მ', 'ნ', 'ო', 'პ', 'ჟ', 'რ', 'ს', 'ტ', 'უ', 'ფ', 'ქ', 'ღ', 'ყ', 'შ', 'ჩ', 'ც', 'ძ', 'წ', 'ჭ', 'ხ', 'ჯ', 'ჰ');
	private static $_letters_lat = array('a', 'b', 'g', 'd', 'e', 'v', 'z', 'T', 'i', 'k', 'l', 'm', 'n', 'o', 'p', 'J', 'r', 's', 't', 'u', 'f', 'q', 'R', 'y', 'S', 'C', 'c', 'Z', 'w', 'W', 'x','j', 'h');
	private static $_letters_lat_wap = array('a', 'b', 'g', 'd', 'e', 'v', 'z', 'th', 'i', 'k', 'l', 'm', 'n', 'o', 'p', 'zh', 'r', 's', 't', 'u', 'f', 'q', 'gh', 'y', 'sh','ch', 'c', 'dz', 'ts', 'tc', 'kh', 'j', 'h');
	private static $_letters_rus = array("й","ц","у","к","е","н","г","ш","щ","з","х","ъ","ф","ы","в","а","п","р","о","л","д","ж","э","ё","я","ч","с","м","и","т","ь","б","ю","Й","Ц","У","К","Е","Н","Г","Ш","Щ","З","Х","Ъ","Ф","Ы","В","А","П","Р","О","Л","Д","Ж","Э","Ё","Я","Ч","С","М","И","Т","Ь","Б","Ю");
	private static $_letters_rus_lat = array("i","c","u","k","e","n","g","sh","shch","z","h","","f","y","v","a","p","r","o","l","d","zh","e","e","ya","ch","s","m","i","t","","b","yu","I","C","U","K","E","N","G","SH","SHCH","Z","X","","F","Y","V","A","P","R","O","L","D","ZH","E","E","YA","CH","S","M","I","T","","B","YU");

	private static $_letters = array(
			'ka-GE'=>array(
				'ka-GE'=>array('ა', 'ბ', 'გ', 'დ', 'ე', 'ვ', 'ზ', 'თ', 'ი', 'კ', 'ლ', 'მ', 'ნ', 'ო', 'პ', 'ჟ', 'რ', 'ს', 'ტ', 'უ', 'ფ', 'ქ', 'ღ', 'ყ', 'შ', 'ჩ', 'ც', 'ძ', 'წ', 'ჭ', 'ხ', 'ჯ', 'ჰ'),
				'en-GB'=>array('a', 'b', 'g', 'd', 'e', 'v', 'z', 'th', 'i', 'k', 'l', 'm', 'n', 'o', 'p', 'zh', 'r', 's', 't', 'u', 'f', 'q', 'gh', 'y', 'sh', 'ch', 'c', 'dz', 'c', 'ch', 'x','j', 'h'),
				'en-GB_canonical'=>array('a', 'b', 'g', 'd', 'e', 'v', 'z', 'T', 'i', 'k', 'l', 'm', 'n', 'o', 'p', 'J', 'r', 's', 't', 'u', 'f', 'q', 'R', 'y', 'S', 'C', 'c', 'Z', 'w', 'W', 'x','j', 'h'),
				'ru-RU'=>array('a', 'b', 'g', 'd', 'e', 'v', 'z', 'T', 'i', 'k', 'l', 'm', 'n', 'o', 'p', 'J', 'r', 's', 't', 'u', 'f', 'q', 'R', 'y', 'S', 'C', 'c', 'Z', 'w', 'W', 'x','j', 'h'), // must change
			),
			'en-GB'=>array(
				'en-GB'=>array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'),
				'ka-GE'=>array('ა', 'ბ', 'ც', 'დ', 'ე', 'ფ', 'გ', 'ჰ', 'ი', 'ჯ', 'კ', 'ლ', 'მ', 'ნ', 'ო', 'პ', 'ქ', 'რ', 'ს', 'ტ', 'უ', 'ვ', 'წ', 'ხ', 'ყ', 'ზ'),
				'ru-RU'=>array('a', 'b', 'g', 'd', 'e', 'v', 'z', 'T', 'i', 'k', 'l', 'm', 'n', 'o', 'p', 'J', 'r', 's', 't', 'u', 'f', 'q', 'R', 'y', 'S', 'C', 'c', 'Z', 'w', 'W', 'x','j', 'h'), // must change
			),
			'ru-RU'=>array(
				'en-GB'=>array('a', 'b', 'g', 'd', 'e', 'v', 'z', 'T', 'i', 'k', 'l', 'm', 'n', 'o', 'p', 'J', 'r', 's', 't', 'u', 'f', 'q', 'R', 'y', 'S', 'C', 'c', 'Z', 'w', 'W', 'x','j', 'h'), // must change
				'ka-GE'=>array('a', 'b', 'g', 'd', 'e', 'v', 'z', 'T', 'i', 'k', 'l', 'm', 'n', 'o', 'p', 'J', 'r', 's', 't', 'u', 'f', 'q', 'R', 'y', 'S', 'C', 'c', 'Z', 'w', 'W', 'x','j', 'h'), // must change
				'ru-RU'=>array('a', 'b', 'g', 'd', 'e', 'v', 'z', 'T', 'i', 'k', 'l', 'm', 'n', 'o', 'p', 'J', 'r', 's', 't', 'u', 'f', 'q', 'R', 'y', 'S', 'C', 'c', 'Z', 'w', 'W', 'x','j', 'h'), // must change
			),
		);

	/**
	 * Returns strings transliterated from UTF-8 to Latin
	 *
	 * @param   string   $string  String to transliterate
	 * @param   boolean  $case    Optionally specify upper or lower case. Default to null.
	 *
	 * @return  string  Transliterated string
	 *
	 * @since   11.1
	 */
	public static function utf8_latin_to_ascii($string, $case = 0)
	{
		static $UTF8_LOWER_ACCENTS = null;
		static $UTF8_UPPER_ACCENTS = null;

		if ($case <= 0)
		{

			if (is_null($UTF8_LOWER_ACCENTS))
			{
				$UTF8_LOWER_ACCENTS = array(
					'à' => 'a',
					'ô' => 'o',
					'ď' => 'd',
					'ḟ' => 'f',
					'ë' => 'e',
					'š' => 's',
					'ơ' => 'o',
					'ß' => 'ss',
					'ă' => 'a',
					'ř' => 'r',
					'ț' => 't',
					'ň' => 'n',
					'ā' => 'a',
					'ķ' => 'k',
					'ŝ' => 's',
					'ỳ' => 'y',
					'ņ' => 'n',
					'ĺ' => 'l',
					'ħ' => 'h',
					'ṗ' => 'p',
					'ó' => 'o',
					'ú' => 'u',
					'ě' => 'e',
					'é' => 'e',
					'ç' => 'c',
					'ẁ' => 'w',
					'ċ' => 'c',
					'õ' => 'o',
					'ṡ' => 's',
					'ø' => 'o',
					'ģ' => 'g',
					'ŧ' => 't',
					'ș' => 's',
					'ė' => 'e',
					'ĉ' => 'c',
					'ś' => 's',
					'î' => 'i',
					'ű' => 'u',
					'ć' => 'c',
					'ę' => 'e',
					'ŵ' => 'w',
					'ṫ' => 't',
					'ū' => 'u',
					'č' => 'c',
					'ö' => 'oe',
					'è' => 'e',
					'ŷ' => 'y',
					'ą' => 'a',
					'ł' => 'l',
					'ų' => 'u',
					'ů' => 'u',
					'ş' => 's',
					'ğ' => 'g',
					'ļ' => 'l',
					'ƒ' => 'f',
					'ž' => 'z',
					'ẃ' => 'w',
					'ḃ' => 'b',
					'å' => 'a',
					'ì' => 'i',
					'ï' => 'i',
					'ḋ' => 'd',
					'ť' => 't',
					'ŗ' => 'r',
					'ä' => 'ae',
					'í' => 'i',
					'ŕ' => 'r',
					'ê' => 'e',
					'ü' => 'ue',
					'ò' => 'o',
					'ē' => 'e',
					'ñ' => 'n',
					'ń' => 'n',
					'ĥ' => 'h',
					'ĝ' => 'g',
					'đ' => 'd',
					'ĵ' => 'j',
					'ÿ' => 'y',
					'ũ' => 'u',
					'ŭ' => 'u',
					'ư' => 'u',
					'ţ' => 't',
					'ý' => 'y',
					'ő' => 'o',
					'â' => 'a',
					'ľ' => 'l',
					'ẅ' => 'w',
					'ż' => 'z',
					'ī' => 'i',
					'ã' => 'a',
					'ġ' => 'g',
					'ṁ' => 'm',
					'ō' => 'o',
					'ĩ' => 'i',
					'ù' => 'u',
					'į' => 'i',
					'ź' => 'z',
					'á' => 'a',
					'û' => 'u',
					'þ' => 'th',
					'ð' => 'dh',
					'æ' => 'ae',
					'µ' => 'u',
					'ĕ' => 'e',
					'œ' => 'oe');
			}

			$string = str_replace(array_keys($UTF8_LOWER_ACCENTS), array_values($UTF8_LOWER_ACCENTS), $string);
		}

		if ($case >= 0)
		{
			if (is_null($UTF8_UPPER_ACCENTS))
			{
				$UTF8_UPPER_ACCENTS = array(
					'À' => 'A',
					'Ô' => 'O',
					'Ď' => 'D',
					'Ḟ' => 'F',
					'Ë' => 'E',
					'Š' => 'S',
					'Ơ' => 'O',
					'Ă' => 'A',
					'Ř' => 'R',
					'Ț' => 'T',
					'Ň' => 'N',
					'Ā' => 'A',
					'Ķ' => 'K',
					'Ŝ' => 'S',
					'Ỳ' => 'Y',
					'Ņ' => 'N',
					'Ĺ' => 'L',
					'Ħ' => 'H',
					'Ṗ' => 'P',
					'Ó' => 'O',
					'Ú' => 'U',
					'Ě' => 'E',
					'É' => 'E',
					'Ç' => 'C',
					'Ẁ' => 'W',
					'Ċ' => 'C',
					'Õ' => 'O',
					'Ṡ' => 'S',
					'Ø' => 'O',
					'Ģ' => 'G',
					'Ŧ' => 'T',
					'Ș' => 'S',
					'Ė' => 'E',
					'Ĉ' => 'C',
					'Ś' => 'S',
					'Î' => 'I',
					'Ű' => 'U',
					'Ć' => 'C',
					'Ę' => 'E',
					'Ŵ' => 'W',
					'Ṫ' => 'T',
					'Ū' => 'U',
					'Č' => 'C',
					'Ö' => 'Oe',
					'È' => 'E',
					'Ŷ' => 'Y',
					'Ą' => 'A',
					'Ł' => 'L',
					'Ų' => 'U',
					'Ů' => 'U',
					'Ş' => 'S',
					'Ğ' => 'G',
					'Ļ' => 'L',
					'Ƒ' => 'F',
					'Ž' => 'Z',
					'Ẃ' => 'W',
					'Ḃ' => 'B',
					'Å' => 'A',
					'Ì' => 'I',
					'Ï' => 'I',
					'Ḋ' => 'D',
					'Ť' => 'T',
					'Ŗ' => 'R',
					'Ä' => 'Ae',
					'Í' => 'I',
					'Ŕ' => 'R',
					'Ê' => 'E',
					'Ü' => 'Ue',
					'Ò' => 'O',
					'Ē' => 'E',
					'Ñ' => 'N',
					'Ń' => 'N',
					'Ĥ' => 'H',
					'Ĝ' => 'G',
					'Đ' => 'D',
					'Ĵ' => 'J',
					'Ÿ' => 'Y',
					'Ũ' => 'U',
					'Ŭ' => 'U',
					'Ư' => 'U',
					'Ţ' => 'T',
					'Ý' => 'Y',
					'Ő' => 'O',
					'Â' => 'A',
					'Ľ' => 'L',
					'Ẅ' => 'W',
					'Ż' => 'Z',
					'Ī' => 'I',
					'Ã' => 'A',
					'Ġ' => 'G',
					'Ṁ' => 'M',
					'Ō' => 'O',
					'Ĩ' => 'I',
					'Ù' => 'U',
					'Į' => 'I',
					'Ź' => 'Z',
					'Á' => 'A',
					'Û' => 'U',
					'Þ' => 'Th',
					'Ð' => 'Dh',
					'Æ' => 'Ae',
					'Ĕ' => 'E',
					'Œ' => 'Oe');
			}
			$string = str_replace(array_keys($UTF8_UPPER_ACCENTS), array_values($UTF8_UPPER_ACCENTS), $string);
		}

		return $string;
	}


	public static function convertToLat($string)
	{
		$string = str_replace(self::$_letters_geo, self::$_letters_lat_wap, $string);
		$string = str_replace(self::$_letters_rus, self::$_letters_rus_lat, $string);
		return $string;
	}

	public static function convert($string, $from, $to)
	{
		if ($from == '*') {
			foreach(self::$_letters as $k=>$v) {
				$letters_from = $v[$k];
				$letters_to = isset($v[$to]) ? $v[$to] : array();
				if ($k == $to || empty($letters_to)) {
					continue;
				}
				$string = str_replace($letters_from, $letters_to, $string);
			}
		} else {
			$letters_from = self::$_letters[$from][$from];
			$letters_to = self::$_letters[$from][$to];
			$string = str_replace($letters_from, $letters_to, $string);
		}
		return $string;
	}


}
