<?php
/**
 * @package	LongCMS.Administrator
 * @subpackage	com_languages
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Languages Strings Model
 *
 * @package			LongCMS.Administrator
 * @subpackage	com_languages
 * @since				2.5
 */
class LanguagesModelStrings extends JModelLegacy
{
	/**
	 * Method for refreshing the cache in the database with the known language strings
	 *
	 * @return	boolean	True on success, Exception object otherwise
	 *
	 * @since		2.5
	 */
	public function refresh()
	{
		require_once JPATH_COMPONENT.'/helpers/languages.php';

		$app = JFactory::getApplication();

		$app->setUserState('com_languages.overrides.cachedtime', null);

		// Empty the database cache first
		try
		{
			$this->_db->setQuery('TRUNCATE TABLE '.$this->_db->qn('#__overrider'));
			$this->_db->query();
		}
		catch (RuntimeException $e)
		{
			return $e;
		}

		// Create the insert query
		$query = $this->_db->getQuery(true)
					->insert($this->_db->qn('#__overrider'))
					->columns('constant, string, file');

		// Initialize some variables
		$client		= $app->getUserState('com_languages.overrides.filter.client', 'site') ? 'administrator' : 'site';
		$language	= $app->getUserState('com_languages.overrides.filter.language', 'en-GB');


		$base = constant('JPATH_'.strtoupper($client));
		$path = $base.'/language/' . $language;

		$files = array();

		// Parse common language directory
		if(JFolder::exists($path))
		{
			$files = JFolder::files($path, $language.'.*ini$', false, true);
		}

		// Parse language directories of components
		$files = array_merge($files, JFolder::files($base.'/components', $language.'.*ini$', 3, true));

		// Parse language directories of modules
		$files = array_merge($files, JFolder::files($base.'/modules', $language.'.*ini$', 3, true));

		// Parse language directories of templates
		$files = array_merge($files, JFolder::files($base.'/templates', $language.'.*ini$', 3, true));

		// Parse language directories of plugins
		$files = array_merge($files, JFolder::files(JPATH_PLUGINS, $language.'.*ini$', 3, true));

		// Parse all found ini files and add the strings to the database cache
		foreach ($files as $file)
		{
			$strings = LanguagesHelper::parseFile($file);
			if ($strings && count($strings))
			{
				$query->clear('values');
				foreach ($strings as $key => $string)
				{
					$query->values($this->_db->q($key).','.$this->_db->q($string).','.$this->_db->q(JPath::clean($file)));;
				}

				try
				{
					$this->_db->setQuery($query);
					if (!$this->_db->query())
					{
						return new Exception($this->_db->getErrorMsg());
					}
				}
				catch (RuntimeException $e)
				{
					return $e;
				}
			}
		}

		// Update the cached time
		$app->setUserState('com_languages.overrides.cachedtime.'.$client.'.'.$language, time());

		return true;
	}

	/**
	 * Method for searching language strings
	 *
	 * @return	array	Array of resuls on success, Exception object otherwise
	 *
	 * @since		2.5
	 */
	public function search()
	{
		$results = array();

		$limitstart = JRequest::getInt('more');

		try
		{
			$searchstring = $this->_db->q('%'.JRequest::getString('searchstring').'%');

			// Create the search query
			$query = $this->_db->getQuery(true)
						->select('constant, string, file')
						->from($this->_db->qn('#__overrider'));
			if (JRequest::getCmd('searchtype') == 'constant')
			{
				$query->where('constant LIKE '.$searchstring);
			}
			else
			{
				$query->where('string LIKE '.$searchstring);
			}

			// Consider the limitstart according to the 'more' parameter and load the results
			$this->_db->setQuery($query, $limitstart, 10);
			$results['results'] = $this->_db->loadObjectList();

			// Check whether there are more results than already loaded
			$query->clear('select')
						->select('COUNT(id)');
			$this->_db->setQuery($query);

			if ($this->_db->loadResult() > $limitstart + 10)
			{
				// If this is set a 'More Results' link will be displayed in the view
				$results['more'] = $limitstart + 10;
			}
		}
		catch (RuntimeException $e)
		{
			return $e;
		}

		return $results;
	}
}
