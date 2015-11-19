<?php
/**
* @version		$Id: nativelib.php 262 2012-01-16 17:52:00Z a.kikabidze $
* @package	LongCMS.Framework.WSLib
* @copyright	Copyright (C) 2009 - 2012 LongCMS Team. All rights reserved.
* @license		GNU General Public License version 2 or later
*/

defined('JPATH_PLATFORM') or die('Restricted access');

jimport('core.filesystem.file');

class JNativeLib
{
	private $_file;
	private $_data;
	private $_nativeData;

	public function __construct($name)
	{
		if (empty($name))
		{
			throw new Exception('Native library name not defined.');
		}
		$this->_file = JPATH_SITE.DS.'libraries'.DS.'saqme'.DS.'advertlib'.DS.$name.'.php';
		if (!JFile::exists($this->_file))
		{
			$status = false;
			$status = $this->_create();
			if (!$status)
			{
				throw new Exception('Native library file '.$name.' not found.');
			}
		}
		$data = @file_get_contents($this->_file);

		$this->_nativeData = !empty($data) ? $data : 'a:0:{}';
		$this->_data = @unserialize($this->_nativeData);
	}

	public function getData($enabled = null, $limitstart = 0, $limit = null, $search = null, $orderBy = 'ordering', $orderDir = 'asc')
	{
		if (empty($this->_data) || !is_array($this->_data))
		{
			return array();
		}

		$return = array();
		foreach($this->_data as $key => $row)
		{
			$id = $key;
			$title = !empty($row->title) ? $row->title : '';
			$published = !empty($row->published) ? $row->published : 0;
			$ordering = !empty($row->ordering) ? $row->ordering : 0;

			if (!is_null($enabled))
			{
				if ($enabled && !$published)
				{
					continue;
				}
				else if (!$enabled && $published)
				{
					continue;
				}
			}
			if (!empty($search) && stripos($title, $search) === false)
			{
				continue;
			}
			$return[$id]->title = $title;
			$return[$id]->published = $published;
			$return[$id]->ordering = $ordering;
		}

		if ($limit)
		{
			$limitstart = (int)$limitstart;
			$limit = (int)$limit;
			$return = @array_slice($return, $limitstart, $limit, true);
		}


		$dataIterator = new ArrayIterator($return);
		switch($orderBy)
		{
			default:
			case 'ordering':
				$dataIterator->uasort('JNativelib::orderingCmp');
				$dataIterator->rewind();
				break;
			case 'title':
				$dataIterator->uasort('JNativelib::titleCmp');
				$dataIterator->rewind();
				break;
			case 'published':
				$dataIterator->uasort('JNativelib::publishedCmp');
				$dataIterator->rewind();
				break;
			case 'id':
				$dataIterator->rewind();
				break;
		}
		$dataIterator = (array)$dataIterator;

		if (strtolower($orderDir) == 'desc')
		{
			$dataIterator = array_reverse($dataIterator, true);
		}
		return $dataIterator;
	}

	public function getTotal()
	{
		return !empty($this->_data) ? count($this->_data) : 0;
	}


	public function getItem($id)
	{
		$empty = new stdClass;
		$empty->title = null;
		$empty->published = 1;
		$empty->ordering = 0;
		return !empty($this->_data[$id]) ? $this->_data[$id] : $empty;
	}


	public function setState($items, $state)
	{
		if (empty($this->_data) || !is_array($this->_data))
		{
			return false;
		}

		if (empty($items))
		{
			return false;
		}

		$return = array();
		foreach($this->_data as $key => $row)
		{
			$id = $key;
			$title = !empty($row->title) ? $row->title : '';
			$published = !empty($row->published) ? $row->published : 0;
			$ordering = !empty($row->ordering) ? $row->ordering : 0;

			if (in_array($key, $items))
			{
				$published = $state;
			}

			$return[$id]->title = $title;
			$return[$id]->published = $published;
			$return[$id]->ordering = $ordering;
		}
		$status = $this->_write($return);
		return $status;
	}


	public function store($data)
	{
		if (is_null($this->_data) || !is_array($this->_data))
		{
			return false;
		}

		if (empty($data))
		{
			return false;
		}

		$return = array();
		$item_id = null;

		if (!empty($data['id']))
		{
			foreach($this->_data as $key => $row)
			{
				$id = $key;
				$title = !empty($row->title) ? $row->title : '';
				$published = !empty($row->published) ? $row->published : 0;
				$ordering = !empty($row->ordering) ? $row->ordering : 0;

				if ($key == $data['id'])
				{
					$title = !empty($data['title']) ? $data['title'] : '';
					$published = !empty($data['published']) ? $data['published'] : 0;
					$ordering = !empty($data['ordering']) ? $data['ordering'] : 0;
				}

				$return[$id]->title = $title;
				$return[$id]->published = $published;
				$return[$id]->ordering = $ordering;
			}
			$item_id = $data['id'];
		}
		else
		{
			$std = new stdClass;
			$std->title = !empty($data['title']) ? $data['title'] : '';
			$std->published = !empty($data['published']) ? $data['published'] : '';
			$std->ordering = !empty($data['ordering']) ? $data['ordering'] : 0;
			if (empty($this->_data))
			{
				$std->ordering = 1;
				$this->_data[1] = $std;
			}
			else
			{
				$std->ordering = 9999999;
				$this->_data[] = $std;
			}
			$return = $this->_data;

			$keys = array_keys($return);
			$max_id = max($keys);
			$item_id = $max_id;
		}
		$this->_data = $return;

		$return = $this->reorderItems();

		$status = $this->_write($return);
		if (!$status)
		{
			return false;
		}
		return $item_id;
	}


	public function delete($items)
	{
		if (empty($this->_data) || !is_array($this->_data))
		{
			return false;
		}

		if (empty($items))
		{
			return false;
		}

		$return = array();
		foreach($this->_data as $key => $row)
		{
			$id = $key;
			$title = !empty($row->title) ? $row->title : '';
			$published = !empty($row->published) ? $row->published : 0;
			$ordering = !empty($row->ordering) ? $row->ordering : 0;

			if (in_array($key, $items))
			{
				continue;
			}

			$return[$id]->title = $title;
			$return[$id]->published = $published;
			$return[$id]->ordering = $ordering;
		}

		$this->_data = $return;
		$return = $this->reorderItems();


		$status = $this->_write($return);
		return $status;
	}

	public function saveOrdering($items)
	{
		if (empty($this->_data) || !is_array($this->_data))
		{
			return false;
		}

		if (empty($items))
		{
			return false;
		}


		$return = array();
		foreach($this->_data as $key => $row)
		{
			$id = $key;
			$title = !empty($row->title) ? $row->title : '';
			$published = !empty($row->published) ? $row->published : 0;
			$ordering = !empty($row->ordering) ? $row->ordering : 0;

			if (isset($items[$key]))
			{
				$ordering = $items[$key];
			}

			$return[$id]->title = $title;
			$return[$id]->published = $published;
			$return[$id]->ordering = $ordering;
		}
		$status = $this->_write($return);
		return $status;
	}

	public function reorderItems()
	{
		$data = $this->getData();
		$array = array();
		$i = 1;
		foreach($data as $key => $row)
		{
			$row->ordering = $i;
			$array[$key] = $row;
			$i++;
		}
		return $array;
	}




	public function orderItem($item, $movement, $reorder = true)
	{
		if (empty($this->_data) || !is_array($this->_data))
		{
			return false;
		}

		if (empty($item))
		{
			return false;
		}

		if (empty($movement))
		{
			return false;
		}

		if ($movement == -1)
		{
			$return = $this->_moveUp($item);
		}
		else
		{
			$return = $this->_moveDown($item);
		}

		$this->_data = $return;
		if ($reorder)
		{
			$return = $this->reorderItems();
		}
		$status = $this->_write($return);
		return $status;
	}



	private function _moveUp($item_id)
	{
		$item = $this->_data[$item_id];

		if (empty($item))
		{
			return false;
		}

		$prev_id = $this->_getPrevID($item_id);

		$return = array();
		foreach($this->_data as $key => $row)
		{
			$id = $key;
			$title = !empty($row->title) ? $row->title : '';
			$published = !empty($row->published) ? $row->published : 0;
			$ordering = !empty($row->ordering) ? $row->ordering : 0;

			if ($id == $item_id && $prev_id && isset($return[$prev_id]->ordering))
			{
				$return[$prev_id]->ordering++;
				$ordering--;
			}

			$return[$id]->title = $title;
			$return[$id]->published = $published;
			$return[$id]->ordering = $ordering;
		}


		return $return;
	}

	private function _moveDown($item_id)
	{
		$item = $this->_data[$item_id];

		if (empty($item))
		{
			return false;
		}

		$next_id = $this->_getNextID($item_id);

		$return = array();
		foreach($this->_data as $key => $row)
		{
			$id = $key;
			$title = !empty($row->title) ? $row->title : '';
			$published = !empty($row->published) ? $row->published : 0;
			$ordering = !empty($row->ordering) ? $row->ordering : 0;

			if ($id == $item_id && $next_id && isset($this->_data[$next_id]->ordering))
			{
				$this->_data[$next_id]->ordering--;
				$ordering++;
			}

			$return[$id]->title = $title;
			$return[$id]->published = $published;
			$return[$id]->ordering = $ordering;
		}

		return $return;
	}

	private function _getPrevID($item_id)
	{
		$prev_id = 0;
		foreach($this->_data as $key => $row)
		{
			if ($key == $item_id && $prev_id)
			{
				break;
			}
			else
			{
				$prev_id = $key;
			}
		}
		return $prev_id;
	}

	private function _getNextID($item_id)
	{
		$next_id = 0;
		$found = false;
		foreach($this->_data as $key => $row)
		{
			if ($found)
			{
				$next_id = $key;
				break;
			}
			if ($key == $item_id)
			{
				$found = true;
			}
		}
		return $next_id;
	}

	private function _create()
	{
		$status = false;
		if (!JFile::exists($this->_file))
		{
			$status = file_put_contents($this->_file, 'a:0:{}');
			chmod($this->_file, 0777);
		}
		return $status;
	}




	private function _write($data)
	{
		$data = @serialize($data);
		$status = @file_put_contents($this->_file, $data, LOCK_EX);
		return $status;
	}

	public static function orderingCmp($val1, $val2)
	{
		if ($val1->ordering === $val2->ordering)
		{
			return 0;
		}
		return ($val1->ordering >= $val2->ordering) ? 1 : -1;
	}

    public static function titleCmp($val1, $val2)
	{
        return strcasecmp($val1->title, $val2->title);
    }

	public static function publishedCmp($val1, $val2)
	{
		if ($val1->published === $val2->published)
		{
			return 0;
		}
		return ($val1->published >= $val2->published) ? 1 : -1;
	}


}











