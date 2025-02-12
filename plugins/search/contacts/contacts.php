<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Contacts Search plugin
 *
 * @package	LongCMS.Plugin
 * @subpackage	Search.contacts
 * @since		1.6
 */
class plgSearchContacts extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	* @return array An array of search areas
	*/
	function onContentSearchAreas()
	{
		static $areas = array(
			'contacts' => 'PLG_SEARCH_CONTACTS_CONTACTS'
		);
		return $areas;
	}

	/**
	* Contacts Search method
	*
	* The sql must return the following fields that are used in a common display
	* routine: href, title, section, created, text, browsernav
	* @param string Target search string
	* @param string matching option, exact|any|all
	* @param string ordering option, newest|oldest|popular|alpha|category
	 */
	function onContentSearch($text, $phrase='', $ordering='', $areas=null)
	{
		$db		= JFactory::getDbo();
		$app	= JFactory::getApplication();
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());

		if (is_array($areas)) {
			if (!array_intersect($areas, array_keys($this->onContentSearchAreas()))) {
				return array();
			}
		}

		$sContent		= $this->params->get('search_content',		1);
		$sArchived		= $this->params->get('search_archived',		1);
		$limit			= $this->params->def('search_limit',		50);
		$state = array();
		if ($sContent) {
			$state[]=1;
		}
		if ($sArchived) {
			$state[]=2;
		}

		$text = trim($text);
		if ($text == '') {
			return array();
		}

		$section = JText::_('PLG_SEARCH_CONTACTS_CONTACTS');

		switch ($ordering) {
			case 'alpha':
				$order = 'a.name ASC';
				break;

			case 'category':
				$order = 'c.title ASC, a.name ASC';
				break;

			case 'popular':
			case 'newest':
			case 'oldest':
			default:
				$order = 'a.name DESC';
		}

		$text	= $db->Quote('%'.$db->escape($text, true).'%', false);

		$rows = array();
		if (!empty($state)) {
			$query	= $db->getQuery(true);
			//sqlsrv changes
			$case_when = ' CASE WHEN ';
			$case_when .= $query->charLength('a.alias');
			$case_when .= ' THEN ';
			$a_id = $query->castAsChar('a.id');
			$case_when .= $query->concatenate(array($a_id, 'a.alias'), ':');
			$case_when .= ' ELSE ';
			$case_when .= $a_id.' END as slug';

			$case_when1 = ' CASE WHEN ';
			$case_when1 .= $query->charLength('c.alias');
			$case_when1 .= ' THEN ';
			$c_id = $query->castAsChar('c.id');
			$case_when1 .= $query->concatenate(array($c_id, 'c.alias'), ':');
			$case_when1 .= ' ELSE ';
			$case_when1 .= $c_id.' END as catslug';

			$query->select('a.name AS title, \'\' AS created, a.con_position, a.misc, '
					.$case_when.','.$case_when1.', '
					. $query->concatenate(array("a.name", "a.con_position", "a.misc"), ",").' AS text,'
                    . $query->concatenate(array($db->Quote($section), "c.title"), " / ").' AS section,'
					. '\'2\' AS browsernav');
			$query->from('#__contact_details AS a');
			$query->innerJoin('#__categories AS c ON c.id = a.catid');
			$query->where('(a.name LIKE '. $text .'OR a.misc LIKE '. $text .'OR a.con_position LIKE '. $text
						.'OR a.address LIKE '. $text .'OR a.suburb LIKE '. $text .'OR a.state LIKE '. $text
						.'OR a.country LIKE '. $text .'OR a.postcode LIKE '. $text .'OR a.telephone LIKE '. $text
						.'OR a.fax LIKE '. $text .') AND a.published IN ('.implode(',', $state).') AND c.published=1 '
						.'AND a.access IN ('. $groups. ') AND c.access IN ('. $groups. ')' );
			$query->group('a.id, a.con_position, a.misc');
			$query->order($order);

			// Filter by language
			if ($app->isSite() && $app->getLanguageFilter()) {
				$tag = JFactory::getLanguage()->getTag();
				$query->where('a.language in (' . $db->Quote($tag) . ',' . $db->Quote('*') . ')');
				$query->where('c.language in (' . $db->Quote($tag) . ',' . $db->Quote('*') . ')');
			}

			$db->setQuery($query, 0, $limit);
			$rows = $db->loadObjectList();

			if ($rows) {
				foreach($rows as $key => $row) {
					$rows[$key]->href = 'index.php?option=com_contact&view=contact&id='.$row->slug.'&catid='.$row->catslug;
					$rows[$key]->text = $row->title;
					$rows[$key]->text .= ($row->con_position) ? ', '.$row->con_position : '';
					$rows[$key]->text .= ($row->misc) ? ', '.$row->misc : '';
				}
			}
		}
		return $rows;
	}
}
