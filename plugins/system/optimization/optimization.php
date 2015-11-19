<?php
/**
 * @package     	LongCMS.Plugin
 * @subpackage  System.Highlight
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_BASE') or die;

jimport('core.application.component.helper');

/**
 * System plugin to highlight terms.
 *
 * @package     	LongCMS.Plugin
 * @subpackage  System.Highlight
 * @since       2.5
 */
class PlgSystemOptimization extends JPlugin
{
	/**
	 * Method to catch the onAfterDispatch event.
	 *
	 * This is where we setup the click-through content highlighting for.
	 * The highlighting is done with JavaScript so we just
	 * need to check a few parameters and the JHtml behavior will do the rest.
	 *
	 * @return  boolean  True on success
	 *
	 * @since   2.5
	 */
	private $_app = null;
	private $_doc = null;
	private $_body = null;
	private $_tab = null;
	private $_line_end = null;

	public function onAfterRender()
	{
		$this->_app = JFactory::getApplication();
		// Check that we are in the site application.
		if ($this->_app->isAdmin())
		{
			return true;
		}
		$this->_doc = JFactory::getDocument();

		// Check if the highlighter should be activated in this environment.
		if ($this->_doc->getType() !== 'html')
		{
			return true;
		}

		$this->_line_end = $this->_doc->_getLineEnd();
		$this->_tab = $this->_doc->_getTab();
		$this->_body = JResponse::getBody();

		// optimize javascript
		$this->_optimizeJavascript();





		JResponse::setBody($this->_body);
	}

	private function _optimizeJavascript()
	{
		$included_scripts = $this->_doc->_scripts;
		$inline_scripts = $this->_doc->_script;

		// @TODO: add excluded scripts option
		$ex_list = false;

		$out = '';
		foreach($included_scripts as $src => $strAttr)
		{
			$mime = $strAttr['mime'];
			$line = $this->_tab.'<script src="'.$src.'"';
			if (!is_null($strAttr['mime']))
			{
				$line .= ' type="' . $strAttr['mime'] . '"';
			}
			if ($strAttr['defer'])
			{
				$line .= ' defer="defer"';
			}
			if ($strAttr['async'])
			{
				$line .= ' async="async"';
			}
			$line .= '></script>'.$this->_line_end;
			if ($ex_list)
			{
				if ($ex_list && !preg_match($ex_list, $strSrc))
				{
					$out .= $line;
				}
			}
			else
			{
				$out .= $line;
			}
			$this->_body = str_replace($line, '', $this->_body);
		}

		$inline_origin = '';
		$inline_out = '';
		foreach($inline_scripts as $type => $content)
		{
			$inline_origin = $this->_tab.'<script type="'.$type.'">'.$this->_line_end;
			$inline_out = $this->_tab.'<script type="'.$type.'">'.$this->_line_end;

			// This is for full XHTML support.
			if ($this->_doc->_mime != 'text/html')
			{
				$inline_origin .= $this->_tab.$this->_tab.'<![CDATA['.$this->_line_end;
				$inline_out .= $this->_tab.$this->_tab.'<![CDATA['.$this->_line_end;
			}

			$inline_origin .= $content.$this->_line_end;

			$src = $this->_filterJS($content);
			$inline_out .= $src.$this->_line_end;


			if ($this->_doc->_mime != 'text/html' )
			{
				$inline_origin .= $this->_tab.$this->_tab.'// ]]>'.$this->_line_end;
				$inline_out .= $this->_tab.$this->_tab.'// ]]>'.$this->_line_end;
			}
			$inline_origin .= $this->_tab.'</script>'.$this->_line_end;
			$inline_out .= $this->_tab.'</script>'.$this->_line_end;
			$out .= $inline_out;
			$this->_body = str_replace($inline_origin, '', $this->_body);
		}

// @TODO: uncomment this block
/*		$compressJS = $this->params->get('compressJS', '');
		if ($compressJS)
		{
			$Out = $this->_compressJS($Out);
		}

		$replacetoken = $this->params->get('replacetoken', 0);
		$token = $this->params->get('token', '');

		if ($replacetoken && $token && strpos($c, $token) !== false)
		{
			$c = str_replace($token, $Out, $c);
		}
		else
		{
			$c = str_replace('</body>', $Out.'</body>', $c);
		}
*/
/*		if (JDEBUG) {
  print_r(htmlspecialchars($out));
  die;

		}*/


		$this->_body = str_replace('</body>', $out.'</body>', $this->_body);
	}

	private function _filterJS($content)
	{
		$content = preg_replace('#<script.*?>(.*?)</script>#is', '$1', $content);
		return $content;
	}

	private function _compressJS($js)
	{
		$js = str_replace("\t", "", $js);
		$js = preg_replace("#\s{2,}#", " ", $js);
		$js = str_replace(": ", ':', $js);
		$js = str_replace(" :", ':', $js);
		$js = preg_replace("#^//.*?$#m", '', $js);
		$js = str_replace("\n", '', $js);
		$js = str_replace("\r", '', $js);

		$js = str_replace("= ", '=', $js);
		$js = str_replace(" =", '=', $js);
		$js = str_replace(", ", ',', $js);
		$js = str_replace(" ,", ',', $js);
		$js = str_replace("} ", '}', $js);
		$js = str_replace(" }", '}', $js);
		$js = str_replace("; ", ';', $js);
		$js = str_replace(" ;", ';', $js);

		$js = str_replace('<script type="text/javascript">', "<script type=\"text/javascript\">\n", $js);
		$js = str_replace('</script>', "\n</script>", $js);
		return $js;
	}


}
