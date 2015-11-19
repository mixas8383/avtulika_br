<?php
/**
 * @package     	LongCMS.Platform
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * JMenu class
 *
 * @package     	LongCMS.Platform
 * @subpackage  Application
 * @since       11.1
 */
class JMenu extends JObject
{
	/**
	 * Array to hold the menu items
	 *
	 * @var    array
	 * @since   11.1
	 */
	protected $_items = array();

	/**
	 * Identifier of the default menu item
	 *
	 * @var    integer
	 * @since   11.1
	 */
	protected $_default = array();

	/**
	 * Identifier of the active menu item
	 *
	 * @var    integer
	 * @since  11.1
	 */
	protected $_active = 0;

	/**
	 * @var    array  JMenu instances container.
	 * @since  11.3
	 */
	protected static $instances = array();

	/**
	 * @var    array  Itemid-s container.
	 * @since  11.3
	 */
	protected static $_itemidList = array();

	/**
	 * Class constructor
	 *
	 * @param   array  $options  An array of configuration options.
	 *
	 * @since   11.1
	 */
	public function __construct($options = array())
	{
		// Load the menu items
		$this->load();

		foreach ($this->_items as $item)
		{
			if ($item->home)
			{
				$this->_default[trim($item->language)] = $item->id;
			}

			// Decode the item params
			$result = new JRegistry;
			$result->loadString($item->params);
			$item->params = $result;
		}
	}

	/**
	 * Returns a JMenu object
	 *
	 * @param   string  $client   The name of the client
	 * @param   array   $options  An associative array of options
	 *
	 * @return  JMenu  A menu object.
	 *
	 * @since   11.1
	 */
	public static function getInstance($client, $options = array())
	{
		if (empty(self::$instances[$client]))
		{
			//Load the router object
			$info = JApplicationHelper::getClientInfo($client, true);

			$path = $info->path . '/includes/menu.php';
			if (file_exists($path))
			{
				include_once $path;

				// Create a JPathway object
				$classname = 'JMenu' . ucfirst($client);
				$instance = new $classname($options);
			}
			else
			{
				//$error = JError::raiseError(500, 'Unable to load menu: '.$client);
				//TODO: Solve this
				$error = null;
				return $error;
			}

			self::$instances[$client] = & $instance;
		}

		return self::$instances[$client];
	}

	/**
	 * Get menu item by id
	 *
	 * @param   integer  $id  The item id
	 *
	 * @return  mixed    The item object, or null if not found
	 *
	 * @since   11.1
	 */
	public function getItem($id)
	{
		$result = null;
		if (isset($this->_items[$id]))
		{
			$result = &$this->_items[$id];
		}

		return $result;
	}

	/**
	 * Set the default item by id and language code.
	 *
	 * @param   integer  $id        The menu item id.
	 * @param   string   $language  The language cod (since 1.6).
	 *
	 * @return  boolean  True, if successful
	 *
	 * @since   11.1
	 */
	public function setDefault($id, $language = '')
	{
		if (isset($this->_items[$id]))
		{
			$this->_default[$language] = $id;
			return true;
		}

		return false;
	}

	/**
	 * Get the default item by language code.
	 *
	 * @param   string  $language  The language code, default value of * means all.
	 *
	 * @return  object  The item object
	 *
	 * @since   11.1
	 */
	public function getDefault($language = '*')
	{
		if (array_key_exists($language, $this->_default))
		{
			return $this->_items[$this->_default[$language]];
		}
		elseif (array_key_exists('*', $this->_default))
		{
			return $this->_items[$this->_default['*']];
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Set the default item by id
	 *
	 * @param   integer  $id  The item id
	 *
	 * @return  mixed  If successful the active item, otherwise null
	 *
	 * @since   11.1
	 */
	public function setActive($id)
	{
		if (isset($this->_items[$id]))
		{
			$this->_active = $id;
			$result = &$this->_items[$id];
			return $result;
		}

		return null;
	}

	/**
	 * Get menu item by id.
	 *
	 * @return  object  The item object.
	 *
	 * @since   11.1
	 */
	public function getActive()
	{
		if ($this->_active)
		{
			$item = &$this->_items[$this->_active];
			return $item;
		}

		return null;
	}

	/**
	 * Gets menu items by attribute
	 *
	 * @param   string   $attributes  The field name
	 * @param   string   $values      The value of the field
	 * @param   boolean  $firstonly   If true, only returns the first item found
	 *
	 * @return  array
	 *
	 * @since   11.1
	 */
	public function getItems($attributes, $values, $firstonly = false)
	{
		$items = array();
		$attributes = (array) $attributes;
		$values = (array) $values;

		foreach ($this->_items as $item)
		{
			if (!is_object($item))
			{
				continue;
			}

			$test = true;
			for ($i = 0, $count = count($attributes); $i < $count; $i++)
			{
				if (is_array($values[$i]))
				{
					if (!in_array($item->$attributes[$i], $values[$i]))
					{
						$test = false;
						break;
					}
				}
				else
				{
					if ($item->$attributes[$i] != $values[$i])
					{
						$test = false;
						break;
					}
				}
			}

			if ($test)
			{
				if ($firstonly)
				{
					return $item;
				}

				$items[] = $item;
			}
		}

		return $items;
	}

	/**
	 * Gets the parameter object for a certain menu item
	 *
	 * @param   integer  $id  The item id
	 *
	 * @return  JRegistry  A JRegistry object
	 *
	 * @since   11.1
	 */
	public function getParams($id)
	{
		if ($menu = $this->getItem($id))
		{
			return $menu->params;
		}
		else
		{
			return new JRegistry;
		}
	}

	/**
	 * Getter for the menu array
	 *
	 * @return  array
	 *
	 * @since   11.1
	 */
	public function getMenu()
	{
		return $this->_items;
	}

	/**
	 * Method to check JMenu object authorization against an access control
	 * object and optionally an access extension object
	 *
	 * @param   integer  $id  The menu id
	 *
	 * @return  boolean  True if authorised
	 *
	 * @since   11.1
	 */
	public function authorise($id)
	{
		$menu = $this->getItem($id);
		$user = JFactory::getUser();

		if ($menu)
		{
			return in_array((int) $menu->access, $user->getAuthorisedViewLevels());
		}
		else
		{
			return true;
		}
	}

	/**
	 * Loads the menu items
	 *
	 * @return  array
	 *
	 * @since   11.1
	 */
	public function load()
	{
		return array();
	}


	/**
	 * Get Itemid
	 *
	 * Returns Itemid if it doesn't already exist for selected view/component.
	 *
	 * @param string $component Component name.
	 * @param string $view View name.
	 * @param string $layout Layout name.
	 *
	 * @return int	Itemid
	 */
	public static function getItemid($component = null, $view = null, $layout = null)
	{
		$app = JFactory::getApplication();

		if (is_null($component))
		{
			$jinput = $app->input;
			$component = $jinput->get('option', '', 'cmd');
		}
		if (is_null($view))
		{
			$view = substr($component, 4);
		}
		if (is_null($layout))
		{
			$layout = 'default';
		}

		if (!$component)
		{
			$error = JError::raiseWarning('107001', JText::_('JLIB_MENU_ERROR_GET_ITEMID'));
			return 1;
		}

		$hash = $component.'.'.$view.'.'.$layout;
		if (isset(self::$_itemidList[$hash]))
		{
			return self::$_itemidList[$hash];
		}

		$comp = JComponentHelper::getComponent($component);
		$menus = $app->getMenu('site');

		$items = $menus->getItems('component_id', isset($comp->id) ? $comp->id : 0);


		$match = NULL;


		$default = 1;
		if ($items)
		{
			foreach($items as $item)
			{
				if (empty($item->id))
				{
					continue;
				}
				$cview = isset($item->query['view']) ? $item->query['view'] : '';
				$clayout = isset($item->query['layout']) ? $item->query['layout'] : '';

				if ($default == 1)
				{
					$default = $item->id;
				}

				if ($layout != 'default')
				{
					if ($cview == $view && $clayout == $layout)
					{
						$match = $item->id;
						break;
					}
				}
				else
				{
					if ($cview == $view)
					{
						$match = $item->id;
						break;
					}
				}
			}
		}
		if (!$match)
		{
			$match = $default;
		}
		self::$_itemidList[$hash] = $match;
		return $match;
	}


}
