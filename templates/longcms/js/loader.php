<?php

/**
 * @package                LongCMS.Site
 * @subpackage	Templates.longcms
 * @copyright        Copyright (C) 2009 - 2012 LongCMS Team. All rights reserved.
 * @license                GNU General Public License version 2 or later; see LICENSE.txt
 */
$scripts = array(
    'jquery-1.9.1.min.js',
    'longlib.js',
    'custom.js',
    'jquery.tools.1.2.7.min.js',
    'jquery.fancybox-2.1.4.pack.js',
    'chosen.jquery.min.js',
    'jwplayer.js',
    'jquery.cycle-3.0.2.src.js',
    //'kbd.js',
    //'iedie.js',
    //'jquery.ui.1.8.18.min.js',
    //'wslib.js',
    //'jquery.delegate.1.1.min.js',
    //'jquery.jcarousellite.1.0.1.min.js',
    //'jquery.alerts.1.1.min.js',
    //'jquery.swfobject.min.js',
    //'swfobject.js',
    //'jquery.elastic.min.js',
    //'jquery.validate.1.9.min.js',
    //'jquery.validate.methods.src.js',
    //'jquery.prettyPhoto.3.1.3.min.js',
    //'jquery.mousewheel.3.0.6.min.js',
    //'com_slidearticle.js',
    //'mainnews.js',
    //'jquery.swfobject.1.1.1.min.js',
    //'jquery.cycle.2.9.js',
    //'jquery.jcarousellite.1.0.1.min.js',
    //'custom-form-elements.js',
    //'jquery.mousewheel.3.0.6.min.js',
    //'jquery.fancybox.2.1.0.pack.js',
    //'droplinemenu.js',
    'domready.js',
);


// Set flag that this is a parent file.
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);

define('JPATH_BASE', dirname('../../../index.php'));
require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

define('PATH_ROOT', dirname(__FILE__) . '/');

define('ENABLE_CACHE', JDEBUG ? false : true);
define('ENABLE_GZIP', true);
define('COMPRESS_LEVEL', 4);


$mt_str = 'Thu, 14 Jan 2010 12:15:52 GMT';
$expr = 86400 * 365;

$etag = md5_file(__FILE__);

header('Etag: ' . $etag);
header('Cache-control: private');
//header('Pragma: cache');
//header('Expires: '.gmdate('D, d M Y H:i:s', time()+$expr).' GMT');
//header('Cache-Control: ');
header('Content-type: text/javascript');

if (ENABLE_CACHE)
{
    $cache = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : 0;
    $client_etag = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER["HTTP_IF_NONE_MATCH"]) : '';

    if ($cache && $client_etag)
    {
        $cache = preg_replace("#;.*$#", "", $cache);
        if ($client_etag == $etag && $cache == $mt_str)
        {
            header('HTTP/1.1 304 Not Modified');
            header('Cache-Control: max-age=' . $expr . ', must-revalidate');
            exit;
        }
    }
}
header('Last-Modified: ' . $mt_str);


$buffer = '';
foreach ($scripts as $script)
{
    if (JDEBUG)
    {
        $buffer .= "\n/* start file: " . $script . " */\n";
    }
    $buffer .= @file_get_contents(PATH_ROOT . 'src/' . $script) . "\n";
    if (JDEBUG)
    {
        $buffer .= "\n/* end file: " . $script . " */\n";
    }
}


if (ENABLE_GZIP)
{
    // GZip module
    $HTTP_ACCEPT_ENCODING = !empty($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '';
    $HTTP_TE = !empty($_SERVER['HTTP_TE']) ? $_SERVER['HTTP_TE'] : '';
    if ($HTTP_ACCEPT_ENCODING)
    {
        $compress = strtolower($HTTP_ACCEPT_ENCODING);
    } else
    {
        $compress = strtolower($HTTP_TE);
    }
    $callback = null;
    if (stripos($compress, 'deflate') !== false)
    {

        function compress_output($output)
        {
            return gzdeflate($output, COMPRESS_LEVEL);
        }

        $method = 'deflate';
        header('Content-Encoding: deflate');
        $callback = 'compress_output';
    } else if (stripos($compress, 'gzip') !== false)
    {

        function compress_output($output)
        {
            return gzencode($output, COMPRESS_LEVEL);
        }

        $method = 'gzip';
        header('Content-Encoding: gzip');
        $callback = 'compress_output';
    } else if (stripos($compress, 'x-gzip') !== false)
    {

        function compress_output($output)
        {
            $x = "\x1f\x8b\x08\x00\x00\x00\x00\x00";
            $size = strlen($output);
            $crc = crc32($output);
            $output = gzcompress($output, COMPRESS_LEVEL);
            $output = substr($output, 0, strlen($output) - COMPRESS_LEVEL);
            $x.= $output;
            $x.= pack('V', $crc);
            $x.= pack('V', $size);
            return $x;
        }

        $method = 'x-gzip';
        header('Content-Encoding: x-gzip');
        $callback = 'compress_output';
    }
    ob_start($callback);
    ob_implicit_flush(false);
    echo $buffer;
    ob_end_flush();
} else
{
    echo $buffer;
}
