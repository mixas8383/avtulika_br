<?php
/**
* @version		$Id: player.php 262 2012-01-16 17:52:00Z a.kikabidze $
* @package	LongCMS.Framework
* @copyright	Copyright (C) 2009 - 2012 LongCMS Team. All rights reserved.
* @license		GNU General Public License version 2 or later
*/
defined('JPATH_PLATFORM') or die('Restricted access');

class JPlayer
{
	private $_player_path;
	private $_player_width;
	private $_player_height;
	private $_player_controlbar;
	private $_player_skin;
	private $_player_autostart;
	private $_player_bufferlength;
	private $_player_volume;
	private $_player_mute;
	private $_player_quality;
	private $_player_repeat;
	private $_player_shuffle;
	private $_player_stretching;
	private $_player_image;
	private $_player_icons;
	private $_player_wmode;

	private $_player_smoothing;
	private $_player_provider;
	private $_player_backcolor;
	private $_player_frontcolor;
	private $_player_lightcolor;

	private $_player_events = array();
	private $_player_plugins = array();


	private $_available_params = array(
		'autostart' => array(
			'false'=>'No','true'=>'Yes'
		),
		'icons' => array(
			'true'=>'Yes','false'=>'No'
		),
		'mute' => array(
			'false'=>'No','true'=>'Yes'
		),
		'quality' => array(
			'true'=>'Yes','false'=>'No'
		),
		'shuffle' => array(
			'false'=>'No','true'=>'Yes'
		),
		'wmode' => array(
			'none'=>'None','opaque'=>'Opaque','transparent'=>'Transparent'
		),
		'provider' => array(
			'video'=>'Video','sound'=>'Sound','image'=>'Image','youtube'=>'Youtube','http'=>'http://','rtmp'=>'rtmp://',
		),
		'repeat' => array(
			'none'=>'None','list'=>'List','always'=>'Always','single'=>'Single',
		),
		'stretching' => array(
			'uniform'=>'Uniform','fill'=>'Fill','exactfit'=>'Exactfit','none'=>'None',
		),
		'controlbar' => array(
			'bottom'=>'Bottom','top'=>'Top','over'=>'Over','none'=>'None',
		),
		'playlist' => array(
			'none'=>'None','bottom'=>'Bottom','over'=>'Over','right'=>'Right','left'=>'Left','top'=>'Top',
		),
	);



	private $_file;
	private $_js_eol;

	private $_error_msg;

	private $_unique_name;
	private $_document;

	private function __construct()
	{
		$this->_document = JFactory::getDocument();
		$this->_player_path = JPath::removeRoot(JPATH_PLAYER);

		$this->_js_eol = JDEBUG ? "\n" : "";
		$this->_js_tab = JDEBUG ? "\t" : "";
		$js_file = $this->_player_path.'/jwplayer.js';
		$this->_document->addScript($js_file);
	}

	public static function getInstance()
	{
		static $instance;
		if (is_null($instance)) {
			$instance = new self();
		}
		$instance->setUnique();
		return $instance;
	}

	public function setUnique()
	{
		$this->_unique_name = JFactory::getUnique('player_');
	}

	public function setWidth($width)
	{
		$this->_player_width = $width;
	}

	public function setWmode($value)
	{
		if ($value == 'none')
		{
			return;
		}
		$this->_player_wmode = $value;
	}

	public function getAvailableParams($name = null)
	{
		if ($name)
		{
			$params = isset($this->_available_params[$name]) ? $this->_available_params[$name] : null;
		}
		else
		{
			$params = $this->_available_params;
		}
		return $params;
	}

	public function setHeight($height)
	{
		$this->_player_height = $height;
	}

	public function setControlbar($value)
	{
		if ($value == 'bottom')
		{
			return;
		}
		$this->_player_controlbar = $value;
	}

	public function setSkin($value)
	{
		$this->_player_skin = $value;
	}

	public function setAutostart($value)
	{
		if ($value == 'false')
		{
			return;
		}
		$this->_player_autostart = $value;
	}

	public function setBufferlength($value)
	{
		if ($value == 1)
		{
			return;
		}
		$this->_player_bufferlength = $value;
	}


	public function setVolume($value)
	{
		if ($value == 90)
		{
			return;
		}
		$this->_player_volume = $value;
	}


	public function setMute($value)
	{
		if ($value == 'false')
		{
			return;
		}
		$this->_player_mute = $value;
	}


	public function setQuality($value)
	{
		$this->_player_quality = $value;
	}


	public function setRepeat($value)
	{
		if ($value == 'none')
		{
			return;
		}
		$this->_player_repeat = $value;
	}

	public function setShuffle($value)
	{
		if ($value == 'false')
		{
			return;
		}
		$this->_player_shuffle = $value;
	}

	public function setSmoothing($value)
	{
		if ($value == 'true')
		{
			return;
		}
		$this->_player_smoothing = $value;
	}

	public function setStretching($value)
	{
		if ($value == 'uniform')
		{
			return;
		}
		$this->_player_stretching = $value;
	}

	public function setImage($value)
	{
		$this->_player_image = $value;
	}
	public function setIcons($value)
	{
		if ($value)
		{
			return;
		}
		$this->_player_icons = $value;
	}
	public function setProvider($value)
	{
		$this->_player_provider = $value;
	}

	public function setBackcolor($value)
	{
		if (strtolower($value) == 'ffffff')
		{
			return;
		}
		$this->_player_backcolor = $value;
	}

	public function setFrontcolor($value)
	{
		if (strtolower($value) == '000000')
		{
			return;
		}
		$this->_player_frontcolor = $value;
	}

	public function setLightcolor($value)
	{
		if (strtolower($value) == '000000')
		{
			return;
		}
		$this->_player_lightcolor = $value;
	}

	public function setScreencolor($value)
	{
		if (strtolower($value) == '000000')
		{
			return;
		}
		$this->_player_screencolor = $value;
	}





	public function setFile($file)
	{
		$this->_file = $file;
	}


	public function setErrorMsg($value)
	{
		$this->_error_msg = $value;
	}





	public function addEvent($event, $callback)
	{
		$this->_player_events[$event] = $callback;
	}



	public function render()
	{
		$js = $this->_getPlayerJs();

		$this->_document->addScriptDeclaration($js);

		$html = '<div id="'.$this->_unique_name.'">'.$this->_error_msg.'</div>';


		return $html;
	}

	private function _getPlayerJs()
	{
		$js = 'jwplayer("'.$this->_unique_name.'").setup({'.$this->_js_eol;

		$params = array();

        $params[] = 'flashplayer:"'.$this->_player_path.'/player.swf"';
        $params[] = 'mediaid:"'.$this->_unique_name.'_id"';
        $params[] = 'width:"'.$this->_player_width.'"';
        $params[] = 'height:"'.$this->_player_height.'"';
	    if (!empty($this->_player_image)) {
			$params[] = 'image:"'.$this->_player_image.'"';
		}
	    if (!empty($this->_player_skin)) {
			$params[] = 'skin:"'.$this->_player_path.'/skins/'.$this->_player_skin.'"';
		}
	    if (!empty($this->_player_autostart)) {
			$params[] = 'autostart:"'.$this->_player_autostart.'"';
		}
	    if (!empty($this->_player_bufferlength)) {
			$params[] = 'bufferlength:"'.$this->_player_bufferlength.'"';
		}
	    if (!empty($this->_player_volume)) {
			$params[] = 'volume:"'.$this->_player_volume.'"';
		}
	    if (!empty($this->_player_mute)) {
			$params[] = 'mute:"'.$this->_player_mute.'"';
		}
	    if (!empty($this->_player_quality)) {
			$params[] = 'quality:"'.$this->_player_quality.'"';
		}
	    if (!empty($this->_player_repeat)) {
			$params[] = 'repeat:"'.$this->_player_repeat.'"';
		}
	    if (!empty($this->_player_shuffle)) {
			$params[] = 'shuffle:"'.$this->_player_shuffle.'"';
		}
	    if (!empty($this->_player_stretching)) {
			$params[] = 'stretching:"'.$this->_player_stretching.'"';
		}
	    if (!empty($this->_player_icons)) {
			$params[] = 'icons:"'.$this->_player_icons.'"';
		}
	    if (!empty($this->_player_provider)) {
			$params[] = 'provider:"'.$this->_player_provider.'"';
		}
	    if (!empty($this->_player_smoothing)) {
			$params[] = 'smoothing:"'.$this->_player_smoothing.'"';
		}
	    if (!empty($this->_file)) {
			$params[] = 'file:"'.$this->_file.'"';
		}
	    if (!empty($this->_file)) {
			$params[] = 'controlbar:"'.(!empty($this->_player_controlbar)?$this->_player_controlbar:'bottom').'"';
		}
	    if (!empty($this->_player_wmode)) {
			$params[] = 'wmode:"'.$this->_player_wmode.'"';
		}

	    if (!empty($this->_player_backcolor)) {
			$params[] = 'backcolor:"'.$this->_player_backcolor.'"';
		}

	    if (!empty($this->_player_frontcolor)) {
			$params[] = 'frontcolor:"'.$this->_player_frontcolor.'"';
		}

	    if (!empty($this->_player_lightcolor)) {
			$params[] = 'lightcolor:"'.$this->_player_lightcolor.'"';
		}


        if (!empty($this->_player_events)) {
			$events_js = $this->_js_eol.'	"events": {'.$this->_js_eol;
			$events = array();
			foreach($this->_player_events as $event=>$code)
			{
				$events[] = $event.': function(event) { '.$code.' }';
			}
			$events_js .= implode(",".$this->_js_eol, $events);
			$events_js .= $this->_js_eol.'},'.$this->_js_eol;
			$params[] = $events_js;
		}
		$js .= $this->_js_tab.implode(",".$this->_js_eol.$this->_js_tab, $params);
		$js .= $this->_js_eol.'});';

		return $js;
	}

	private function _encodeUrl($url)
	{
		$url = str_replace(array('?','=','&'), array('%3F','%3D','%26'), $url);
		return $url;
	}


	private function __clone()
	{

	}

}

