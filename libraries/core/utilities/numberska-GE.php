<?php
/**
* @version		$Id: numberska-GE.php 262 2012-01-16 17:52:00Z a.kikabidze $
* @package	LongCMS.Framework.WSLib
* @copyright	Copyright (C) 2009 - 2012 LongCMS Team. All rights reserved.
* @license		GNU General Public License version 2 or later
*/
defined('JPATH_PLATFORM') or die('Restricted access');

class JNumberskaGE
{
    var $locale = 'ka-GE';
    var $lang = 'Georgian';
    var $lang_native = 'Georgian';
    var $_minus = 'მინუს '; // minus sign
	var $ceill = ' მთელი ';
	var $gell = 'ლარი';
	var $coin = 'თეთრი';
	var $and = 'და';
	var $delcur = ' ';

    var $a = array(
        0 =>'ნული', 'ერთი', 'ორი', 'სამი', 'ოთხი', 'ხუთი', 'ექვსი', 'შვიდი', 'რვა', 'ცხრა'
    );
    var $b = array(
        0 =>'ათი', 'თერთმეტი','თორმეტი','ცამეტი', 'თოთხმეტი', 'თხუთმეტი', 'თექვსმეტი', 'ჩვიდმეტი', 'თვრამეტი', 'ცხრამეტი'
    );
    var $c = array(
			1 => 'ათი',  'ოცი','ოცდაათი','ორმოცი','ორმოცდაათი','სამოცი','სამოცდაათი','ოთხმოცი','ოთხმოცდაათი'
    );
    var $ce = array(
        2 => 'ოცდა','ოცდა', 'ორმოცდა','ორმოცდა','სამოცდა','სამოცდა','ოთხმოცდა','ოთხმოცდა'
    );
    var $d = array(
        1=> 'ასი','ორასი','სამასი','ოთხასი','ხუთასი','ექვსასი','შვიდასი','რვაასი','ცხრაასი'
    );
    var $de = array(
        1=>'ას','ორას','სამას','ოთხას','ხუთას','ექვსას','შვიდას','რვაას','ცხრაას'
    );
    var $e = array(
	 1=>'ათასი','მილიონი','მილიარდი', 'ტრილიონი','კვადრილიონი','კვინტილიონი'
        );
    var $ee = array(
	 1=>'ათას',' მილიონ','მილიარდ', 'ტრილიონ','კვადრილიონ','კვინტილიონ'
        );
	    var $f = array(
        1=> 'მეათედი','მეასედი','მეათასედი','მეათიათასედი','მეასიათასედი','მემილიონედი'
    );


    var $_sep = '';
    function Currency($num)
    {
		$str = str_replace(',', '.', strval($num));
		$dec = explode('.',$str);
		if (!empty($dec[1]))
		{
			$ceill = $this->toWords($dec[0]);
			$decimal = $this->toWords($dec[1]);
			return $ceill.
						$this->delcur.
						$this->gell.
						$this->delcur.
						$this->and.
						$this->delcur.
						$decimal.
						$this->delcur.
						$this->coin;
		}
		else
		{
			return $this->toWords($num).$this->delcur.$this->gell;
		}
	}

    function word($num)
    {
		$str = str_replace(',', '.', $num);
		$dec = explode('.',$str);

		if (!empty($dec[1]))
		{
			$ceill = $this->toWords($dec[0]);
			$decimal = $this->toWords($dec[1]);
			return $ceill.$this->ceill.$decimal.' '. $this->f[strlen($dec[1])];
		}
		else
		{
			return $this->toWords($num);
		}
	}


    function toWords($num)
    {
        $r = '';
		$prep = '';
        // add a minus sign
        if (substr($num, 0, 1) == '-')
		{
            $prep = $this->_minus;
            $num = substr($num, 1);
        }
        // strip excessive zero signs and spaces
        $num = trim($num);
        $num = preg_replace('/^0+/', '', $num);
		if (empty($num))
		{
			echo  "<br />";
			return $this->a[0];
		}

		if ($num < 0)
		{
			$prep = $this->_minus;
		}

		$n = str_split($num);
		$n = array_reverse($n);
		$n = array_chunk($n, 3);
		$ftriplet = $this->getTriplet($n[0]);
		unset($n[0]);
		$return = $ftriplet;
		$count =1;
		foreach($n as $c)
		{
			$return = $this->getOtherTriplet($c, $count, $return).' '.$return;
			$count++;
		}
		return $prep.$return;
	}

	function getTriplet($n)
	{
		$c = count($n);
		switch($c)
		{
			case 1:
				return $this->a[$n[0]];
				break;
			case 2:
				if ($n[0] == 0)
				{
					return $this->c[$n[1]];
				}
				return $this->get2($n);
				break;
			case 3:
				if ($n[0] == 0 && $n[1] == 0)
				{
					return $this->d[$n[2]];
				}
				if ($n[1] == 0)
				{
					return $this->de[$n[2]].' '.$this->_sep.$this->a[$n[0]];
				}
				if ($n[0] == 0)
				{
					return $this->de[$n[2]].' '.$this->_sep.$this->c[$n[1]];
				}
				return $this->de[$n[2]].' '.$this->_sep.$this->get2($n);
				break;
		}
		return $this->a[$n[0]];
	}

	function get2($n)
	{
		switch($n[1])
		{
			case 1:
				return $this->b[$n[0]];
				break;
			case 3:
			case 5:
			case 7:
			case 9:
				return $this->ce[$n[1]].$this->b[$n[0]];
			case 2:
			case 4:
			case 6:
			case 8:
				return $this->ce[$n[1]].$this->a[$n[0]];
				break;
		}
	}

	function getOtherTriplet($n, $count, $return)
	{
		$c = count($n);
	 	$k = intval(implode($n));
		if (empty($k))
		{
			return '';
		}
		$return = trim($return);
		if (empty($return))
		{
			$app = $this->e[ $count ];
		}
		else
		{
			$app = $this->ee[ $count ];
		}
		if ($n[0] == 1 && $c == 1)
		{
			return $app;
		}

		switch($c)
		{
			case 1:
				return $this->a[$n[0]].$app;
				break;
			case 2:
				if ($n[0] == 0)
				{
					return $this->c[$n[1]].$app;
				}
				return $this->get2($n).$app;
				break;
			case 3:
				if ($n[0] == 0 && $n[1] == 0)
				{
					return $this->d[$n[2]].$app;
				}
				if ($n[1] == 0)
				{
					return $this->de[$n[2]].' '.$this->_sep.$this->a[$n[0]].$app;
				}
				if ($n[0] == 0)
				{
					return $this->de[$n[2]].' '.$this->_sep.$this->c[$n[1]].$app;
				}
				return $this->de[$n[2]].' '.$this->_sep.$this->get2($n).$app;
				break;
		}
		return $this->a[$n[0]].$app;
	}
}

