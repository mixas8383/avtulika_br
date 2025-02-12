<?php
/**
 * @package	LongCMS.Administrator
 * @subpackage	com_languages
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in LongCMS
defined('_JEXEC') or die;

/**
 * JSON Response class
 *
 * @package	LongCMS.Administrator
 * @since		2.5
 */
class JJsonResponse
{
	/**
	 * Determines whether the request was successful
	 *
	 * @var		boolean
	 * @since	2.5
	 */
	public $success		= true;

	/**
	 * Determines whether the request wasn't successful.
	 * This is always the negation of $this->success,
	 * so you can use both flags equivalently.
	 *
	 * @var		boolean
	 * @since	2.5
	 */
	public $error			= false;

	/**
	 * The main response message
	 *
	 * @var		string
	 * @since	2.5
	 */
	public $message		= null;

	/**
	 * Array of messages gathered in the JApplication object
	 *
	 * @var		array
	 * @since	2.5
	 */
	public $messages	= null;

	/**
	 * The response data
	 *
	 * var		array/object
	 * @since	2.5
	 */
	public $data			= null;

	/**
	 * Constructor
	 *
	 * @param		array/object	$response	The Response data
	 * @param		string				$message	The main response message
	 * @param		boolean				$error		True, if the success flag shall be set to false, defaults to false
	 *
	 * @return	void
	 *
	 * @since		2.5
	 */
	public function __construct($response = null, $message = null, $error = false)
	{
		$this->message = $message;

		// Get the message queue
		$messages = JFactory::getApplication()->getMessageQueue();

		// Build the sorted messages list
		if (is_array($messages) && count($messages))
		{
			foreach ($messages as $message)
			{
				if (isset($message['type']) && isset($message['message']))
				{
					$lists[$message['type']][] = $message['message'];
				}
			}
		}

		// If messages exist add them to the output
		if (isset($lists) && is_array($lists))
		{
			$this->messages = $lists;
		}

		// Check if we are dealing with an error
		if ($response instanceof Exception)
		{
			// Prepare the error response
			$this->success	= false;
			$this->error		= true;
			$this->message	= $response->getMessage();
		}
		else
		{
			// Prepare the response data
			$this->success	= !$error;
			$this->error		= $error;
			$this->data			= $response;
		}
	}

	/**
	 * Magic toString method for sending the response in JSON format
	 *
	 * @return	string	The response in JSON format
	 *
	 * @since		2.5
	 */
	public function __toString()
	{
		return json_encode($this);
	}
}
