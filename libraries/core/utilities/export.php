<?php
/**
* @version		$Id: export.php 262 2012-01-16 17:52:00Z a.kikabidze $
* @package	LongCMS.Framework.WSLib
* @copyright	Copyright (C) 2009 - 2012 LongCMS Team. All rights reserved.
* @license		GNU General Public License version 2 or later
*/
defined('JPATH_PLATFORM') or die('Restricted access');

/**
Example:

jimport('core.utilities.export');
$export = new JExport('namespace');

$export->setLimit($limit);
$export->setTimeOut($timeout);
$export->setFilePath($filepath);

$export->addWhere('`field`='.$export->quote('value'));
. . .
. . .
. . .
$export->addCountQuery($countQuery);
$export->addSelectQuery($selectQuery);

$data = $export->getData();

// here process data

$export->storeData($processedData);

$export->setRedirect();

$export->display();
*/


jimport('core.filesystem.file');
jimport('core.filesystem.folder');

class JExport
{
	private $_namespace;
	private $_session;
	private $_db;
	private $_file;
	private $_folder;
	private $_fileurl;



	private $_fullData = array();
	private $_data = array();
	private $_total;
	private $_limit;
	private $_limitstart;
	private $_timeout;
	private $_whereArray = array();
	private $_ordering;
	private $_countQuery;
	private $_selectQuery;
	private $_startTime;
	private $_dataCallback;





	public function __construct($namespace = 'jexport')
	{
		$this->_namespace = $namespace;
		$this->_session = JFactory::getSession();
		$this->_db = JFactory::getDBO();
		$this->_limitstart = JRequest::getInt('limitstart', 0);

		$this->_startTime = microtime(true);
	}

	public function setFilePath($filepath)
	{
		$sesfilepath = $this->getSesValue('filepath');
		if (empty($sesfilepath))
		{
			$this->setSesValue('filepath', $filepath);
		}
		else
		{
			$filepath = $sesfilepath;
		}

		$folder = JFolder::makeSafe(dirname($filepath));
		$file = JFile::makeSafe(basename($filepath));

		if (!JFolder::exists(JPATH_SITE.DS.$folder))
		{
			JFolder::create(JPATH_SITE.DS.$folder, 0777);
		}

		$this->_folder = $folder;
		$this->_file = $file;
		$this->_fileurl = JURI::root().$this->_folder.'/'.$this->_file;
	}


	public function setLimit($limit)
	{
		$this->_limit = $limit;
	}

	public function getLimit()
	{
		return $this->_limit;
	}

	public function setTimeout($timeout)
	{
		$this->_timeout = (int)$timeout;
	}

	public function getTimeout()
	{
		return $this->_timeout;
	}




	public function addWhere($where)
	{
		$this->_whereArray[] = $where;
	}


	public function addCountQuery($query)
	{
		$this->_countQuery = $query;
	}

	public function getCountQuery()
	{
		return $this->_countQuery;
	}




	public function addSelectQuery($query)
	{
		$this->_selectQuery = $query;
	}

	public function getLimitstart()
	{
		return $this->_limitstart;
	}


	public function setOrdering($ordering)
	{
		$this->_ordering = $ordering;
		return $this->_ordering;
	}







	public function quote($value)
	{
		return $this->_db->quote($value);
	}




	public function getData()
	{
		$this->_getTotal();
		if (empty($this->_selectQuery))
		{
			return false;
		}
		$where = $this->_getWhere();
		$ordering = $this->_getOrdering();

		$query = $this->_selectQuery
						.$where
						.$ordering
						;
		$this->_db->setQuery($query, $this->_limitstart, $this->_limit);
		$this->_data = $this->_db->loadObjectList();
		return $this->_data;
	}


	public function setRedirect()
	{
		$uri = JFactory::getUri();
		$uri->setVar('limitstart', $this->_limitstart + $this->_limit);
		$uri->setVar('rand', mt_rand(10000000, 99999999));
		$url = $uri->toString();
		if (!$this->isFinished())
		{
			$timeout = $this->getTimeout();
			$timeout *=  1000
			?>
			<script language="javascript" type="text/javascript">
			var timeout = "<?php echo $timeout ?>";
			var doRedirect = true;
			function redirect()
			{
				location.replace("<?php  echo $url ?>")
			}
			if (doRedirect)
			{
				setTimeout(redirect, timeout);
			}
			</script>
			<?php
		}
	}




	public function display()
	{
		if (!empty($this->_data))
		{
			$tm = round(microtime(true) - $this->_startTime, 10);

			echo '<div class="jexport_'.$this->_namespace.'_summary">';
			echo JText::_('Limitstart:').' '.$this->_limitstart.'<br />';
			echo JText::_('Limit:').' '.$this->_limit.'<br />';
			echo JText::_('Processed:').' '.($this->_limitstart + $this->_limit > $this->_total ? $this->_total : $this->_limitstart + $this->_limit).'<br />';
			echo JText::_('Total:').' '.$this->_total.'<br />';
			echo JText::_('Time Elapsed:').' '.$tm.' sec<br />';
			if ($this->isFinished())
			{
				echo '<div class="jexport_'.$this->_namespace.'_finished">';
				echo JText::_('Process Finished');
				echo '</div>';

				echo '<div class="jexport_'.$this->_namespace.'_download">';
				echo '<a href="'.$this->_fileurl.'" target="_blank">'.JText::_('Download').'</a>';
				echo '</div>';
				$this->_clearSession();
			}
			echo '</div>';
		}


	}


	public function isFinished()
	{
		$isFinish = false;
		if (($this->_limitstart + $this->_limit) >= $this->_total)
		{
			$isFinish = true;
		}
		return $isFinish;
	}

	public function storeData($data)
	{
		$path = JPATH_SITE.DS.$this->_folder.DS.$this->_file;
		$status = file_put_contents($path, $data, FILE_APPEND | LOCK_EX);
		return $status;
	}


	public function setSesValue($name, $value = null)
	{
		$sesNames = (array)$this->_session->get('sesNames', array(), $this->_namespace);
		$sesNames[] = $name;
		$sesNames = array_unique($sesNames);
		$this->_session->set('sesNames', $sesNames, $this->_namespace);

		$this->_session->set($name, $value, $this->_namespace);
	}

	public function getSesValue($name, $default = null)
	{
		$return = $this->_session->get($name, $default, $this->_namespace);
		return $return;
	}


	private function _clearSession()
	{
		$sesNames = (array)$this->getSesValue('sesNames', array());
		foreach($sesNames as $name)
		{
			$this->setSesValue($name, null);
		}
		$this->setSesValue('sesNames', array());
	}


	private function _getTotal()
	{
		$this->_total = $this->getSesValue('total');
		if (!$this->_total)
		{
			$this->_total = $this->_countTotal();
			$this->setSesValue('total', $this->_total);
		}
		return $this->_total;
	}

	private function _countTotal()
	{
		if (empty($this->_countQuery))
		{
			return false;
		}
		$where = $this->_getWhere();
		$query = $this->_countQuery
						.$where
						;
		$this->_db->setQuery($query);
		$total = $this->_db->loadResult();
		return $total;
	}

	private function _getWhere()
	{
		$where = count($this->_whereArray) ? ' WHERE ('.implode(') AND (', $this->_whereArray).') ' : '';
		return $where;
	}

	protected function _getOrdering()
	{
		return $this->_ordering ? ' ORDER BY '.$this->_ordering : '';
	}


}
