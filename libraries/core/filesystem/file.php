<?php
/**
 * @package     	LongCMS.Platform
 * @subpackage  FileSystem
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('core.filesystem.path');

/**
 * A File handling class
 *
 * @package     	LongCMS.Platform
 * @subpackage  FileSystem
 * @since       11.1
 */
class JFile
{
	private static $_mimes = array(
			'ai'=>'application/postscript',
			'aif'=>'audio/x-aiff',
			'aifc'=>'audio/x-aiff',
			'aiff'=>'audio/x-aiff',
			'asc'=>'text/plain',
			'au'=>'audio/basic',
			'avi'=>'video/x-msvideo',
			'bcpio'=>'application/x-bcpio',
			'bin'=>'application/octet-stream',
			'bmp'=>'image/bmp',
			'c'=>'text/plain',
			'cc'=>'text/plain',
			'ccad'=>'application/clariscad',
			'cdf'=>'application/x-netcdf',
			'class'=>'application/octet-stream',
			'cpio'=>'application/x-cpio',
			'cpt'=>'application/mac-compactpro',
			'csh'=>'application/x-csh',
			'css'=>'text/css',
			'dcr'=>'application/x-director',
			'dir'=>'application/x-director',
			'dms'=>'application/octet-stream',
			'doc'=>'application/msword',
			'drw'=>'application/drafting',
			'dvi'=>'application/x-dvi',
			'dwg'=>'application/acad',
			'dxf'=>'application/dxf',
			'dxr'=>'application/x-director',
			'eps'=>'application/postscript',
			'etx'=>'text/x-setext',
			'exe'=>'application/octet-stream',
			'ez'=>'application/andrew-inset',
			'f'=>'text/plain',
			'f90'=>'text/plain',
			'fli'=>'video/x-fli',
			'flv'=>'video/x-flv',
			'gif'=>'image/gif',
			'gtar'=>'application/x-gtar',
			'gz'=>'application/x-gzip',
			'h'=>'text/plain',
			'hdf'=>'application/x-hdf',
			'hh'=>'text/plain',
			'hqx'=>'application/mac-binhex40',
			'htm'=>'text/html',
			'html'=>'text/html',
			'ice'=>'x-conference/x-cooltalk',
			'ief'=>'image/ief',
			'iges'=>'model/iges',
			'igs'=>'model/iges',
			'ips'=>'application/x-ipscript',
			'ipx'=>'application/x-ipix',
			'jpe'=>'image/jpeg',
			'jpeg'=>'image/jpeg',
			'jpg'=>'image/jpeg',
			'js'=>'application/x-javascript',
			'kar'=>'audio/midi',
			'latex'=>'application/x-latex',
			'lha'=>'application/octet-stream',
			'lsp'=>'application/x-lisp',
			'lzh'=>'application/octet-stream',
			'm'=>'text/plain',
			'man'=>'application/x-troff-man',
			'me'=>'application/x-troff-me',
			'mesh'=>'model/mesh',
			'mid'=>'audio/midi',
			'midi'=>'audio/midi',
			'mif'=>'application/vnd.mif',
			'mime'=>'www/mime',
			'mov'=>'video/quicktime',
			'movie'=>'video/x-sgi-movie',
			'mp2'=>'audio/mpeg',
			'mp3'=>'audio/mpeg',
			'mpe'=>'video/mpeg',
			'mpeg'=>'video/mpeg',
			'mpg'=>'video/mpeg',
			'mpga'=>'audio/mpeg',
			'ms'=>'application/x-troff-ms',
			'msh'=>'model/mesh',
			'nc'=>'application/x-netcdf',
			'oda'=>'application/oda',
			'pbm'=>'image/x-portable-bitmap',
			'pdb'=>'chemical/x-pdb',
			'pdf'=>'application/pdf',
			'pgm'=>'image/x-portable-graymap',
			'pgn'=>'application/x-chess-pgn',
			'png'=>'image/png',
			'pnm'=>'image/x-portable-anymap',
			'pot'=>'application/mspowerpoint',
			'ppm'=>'image/x-portable-pixmap',
			'pps'=>'application/mspowerpoint',
			'ppt'=>'application/mspowerpoint',
			'ppz'=>'application/mspowerpoint',
			'pre'=>'application/x-freelance',
			'prt'=>'application/pro_eng',
			'ps'=>'application/postscript',
			'qt'=>'video/quicktime',
			'ra'=>'audio/x-realaudio',
			'ram'=>'audio/x-pn-realaudio',
			'ras'=>'image/cmu-raster',
			'rgb'=>'image/x-rgb',
			'rm'=>'audio/x-pn-realaudio',
			'roff'=>'application/x-troff',
			'rpm'=>'audio/x-pn-realaudio-plugin',
			'rtf'=>'text/rtf',
			'rtx'=>'text/richtext',
			'scm'=>'application/x-lotusscreencam',
			'set'=>'application/set',
			'sgm'=>'text/sgml',
			'sgml'=>'text/sgml',
			'sh'=>'application/x-sh',
			'shar'=>'application/x-shar',
			'silo'=>'model/mesh',
			'sit'=>'application/x-stuffit',
			'skd'=>'application/x-koan',
			'skm'=>'application/x-koan',
			'skp'=>'application/x-koan',
			'skt'=>'application/x-koan',
			'smi'=>'application/smil',
			'smil'=>'application/smil',
			'snd'=>'audio/basic',
			'sol'=>'application/solids',
			'spl'=>'application/x-futuresplash',
			'src'=>'application/x-wais-source',
			'step'=>'application/STEP',
			'stl'=>'application/SLA',
			'stp'=>'application/STEP',
			'sv4cpio'=>'application/x-sv4cpio',
			'sv4crc'=>'application/x-sv4crc',
			'swf'=>'application/x-shockwave-flash',
			't'=>'application/x-troff',
			'tar'=>'application/x-tar',
			'tcl'=>'application/x-tcl',
			'tex'=>'application/x-tex',
			'texi'=>'application/x-texinfo',
			'texinfo'=>'application/x-texinfo',
			'tif'=>'image/tiff',
			'tiff'=>'image/tiff',
			'tr'=>'application/x-troff',
			'tsi'=>'audio/TSP-audio',
			'tsp'=>'application/dsptype',
			'tsv'=>'text/tab-separated-values',
			'txt'=>'text/plain',
			'unv'=>'application/i-deas',
			'ustar'=>'application/x-ustar',
			'vcd'=>'application/x-cdlink',
			'vda'=>'application/vda',
			'viv'=>'video/vnd.vivo',
			'vivo'=>'video/vnd.vivo',
			'vrml'=>'model/vrml',
			'wav'=>'audio/x-wav',
			'wrl'=>'model/vrml',
			'xbm'=>'image/x-xbitmap',
			'xlc'=>'application/vnd.ms-excel',
			'xll'=>'application/vnd.ms-excel',
			'xlm'=>'application/vnd.ms-excel',
			'xls'=>'application/vnd.ms-excel',
			'xlw'=>'application/vnd.ms-excel',
			'xml'=>'application/xml',
			'xpm'=>'image/x-xpixmap',
			'xwd'=>'image/x-xwindowdump',
			'xyz'=>'chemical/x-pdb',
			'zip'=>'application/zip',
		);

	/**
	 * Gets the extension of a file name
	 *
	 * @param   string  $file  The file name
	 *
	 * @return  string  The file extension
	 *
	 * @since   11.1
	 */
	public static function getExt($file)
	{
		$dot = strrpos($file, '.') + 1;

		return substr($file, $dot);
	}

	/**
	 * Strips the last extension off of a file name
	 *
	 * @param   string  $file  The file name
	 *
	 * @return  string  The file name without the extension
	 *
	 * @since   11.1
	 */
	public static function stripExt($file)
	{
		return preg_replace('#\.[^.]*$#', '', $file);
	}

	/**
	 * Makes file name safe to use
	 *
	 * @param   string  $file  The name of the file [not full path]
	 *
	 * @return  string  The sanitised string
	 *
	 * @since   11.1
	 */
	public static function makeSafe($file, $lower = false, $no_spaces = false)
	{
		$regex = array('#(\.){2,}#', '#[^A-Za-z0-9\.\_\- ]#', '#^\.#');
		$file = preg_replace($regex, '', $file);
		if ($lower) {
			$file = strtolower($file);
		}
		if ($no_spaces) {
			$file = str_replace(array(' '), array('_'), $file);
		}
		return $file;
	}

	/**
	 * Copies a file
	 *
	 * @param   string   $src          The path to the source file
	 * @param   string   $dest         The path to the destination file
	 * @param   string   $path         An optional base path to prefix to the file names
	 * @param   boolean  $use_streams  True to use streams
	 *
	 * @return  boolean  True on success
	 *
	 * @since   11.1
	 */
	public static function copy($src, $dest, $path = null, $use_streams = false)
	{
		// Prepend a base path if it exists
		if ($path)
		{
			$src = JPath::clean($path . '/' . $src);
			$dest = JPath::clean($path . '/' . $dest);
		}

		// Check src path
		if (!is_readable($src))
		{
			JError::raiseWarning(21, JText::sprintf('JLIB_FILESYSTEM_ERROR_JFILE_FIND_COPY', $src));

			return false;
		}

		if ($use_streams)
		{
			$stream = JFactory::getStream();

			if (!$stream->copy($src, $dest))
			{
				JError::raiseWarning(21, JText::sprintf('JLIB_FILESYSTEM_ERROR_JFILE_STREAMS', $src, $dest, $stream->getError()));

				return false;
			}

			return true;
		}
		else
		{
			// Initialise variables.
			$FTPOptions = JClientHelper::getCredentials('ftp');

			if ($FTPOptions['enabled'] == 1)
			{
				// Connect the FTP client
				jimport('core.client.ftp');
				$ftp = JFTP::getInstance($FTPOptions['host'], $FTPOptions['port'], null, $FTPOptions['user'], $FTPOptions['pass']);

				// If the parent folder doesn't exist we must create it
				if (!file_exists(dirname($dest)))
				{
					jimport('core.filesystem.folder');
					JFolder::create(dirname($dest));
				}

				// Translate the destination path for the FTP account
				$dest = JPath::clean(str_replace(JPATH_ROOT, $FTPOptions['root'], $dest), '/');
				if (!$ftp->store($src, $dest))
				{

					// FTP connector throws an error
					return false;
				}
				$ret = true;
			}
			else
			{
				if (!@ copy($src, $dest))
				{
					JError::raiseWarning(21, JText::_('JLIB_FILESYSTEM_ERROR_COPY_FAILED'));

					return false;
				}
				$ret = true;
			}

			return $ret;
		}
	}

	/**
	 * Delete a file or array of files
	 *
	 * @param   mixed  $file  The file name or an array of file names
	 *
	 * @return  boolean  True on success
	 *
	 * @since   11.1
	 */
	public static function delete($file)
	{
		// Initialise variables.
		jimport('core.client.helper');
		$FTPOptions = JClientHelper::getCredentials('ftp');

		if (is_array($file))
		{
			$files = $file;
		}
		else
		{
			$files[] = $file;
		}

		// Do NOT use ftp if it is not enabled
		if ($FTPOptions['enabled'] == 1)
		{
			// Connect the FTP client
			jimport('core.client.ftp');
			$ftp = JFTP::getInstance($FTPOptions['host'], $FTPOptions['port'], null, $FTPOptions['user'], $FTPOptions['pass']);
		}

		foreach ($files as $file)
		{
			$file = JPath::clean($file);

			// Try making the file writable first. If it's read-only, it can't be deleted
			// on Windows, even if the parent folder is writable
			@chmod($file, 0777);

			// In case of restricted permissions we zap it one way or the other
			// as long as the owner is either the webserver or the ftp
			if (@unlink($file))
			{
				// Do nothing
			}
			elseif ($FTPOptions['enabled'] == 1)
			{
				$file = JPath::clean(str_replace(JPATH_ROOT, $FTPOptions['root'], $file), '/');
				if (!$ftp->delete($file))
				{
					// FTP connector throws an error

					return false;
				}
			}
			else
			{
				$filename = basename($file);
				JError::raiseWarning('SOME_ERROR_CODE', JText::sprintf('JLIB_FILESYSTEM_DELETE_FAILED', $filename));

				return false;
			}
		}

		return true;
	}

	/**
	 * Moves a file
	 *
	 * @param   string   $src          The path to the source file
	 * @param   string   $dest         The path to the destination file
	 * @param   string   $path         An optional base path to prefix to the file names
	 * @param   boolean  $use_streams  True to use streams
	 *
	 * @return  boolean  True on success
	 *
	 * @since   11.1
	 */
	public static function move($src, $dest, $path = '', $use_streams = false)
	{
		if ($path)
		{
			$src = JPath::clean($path . '/' . $src);
			$dest = JPath::clean($path . '/' . $dest);
		}

		// Check src path
		if (!is_readable($src))
		{

			return JText::_('JLIB_FILESYSTEM_CANNOT_FIND_SOURCE_FILE');
		}

		if ($use_streams)
		{
			$stream = JFactory::getStream();

			if (!$stream->move($src, $dest))
			{
				JError::raiseWarning(21, JText::sprintf('JLIB_FILESYSTEM_ERROR_JFILE_MOVE_STREAMS', $stream->getError()));

				return false;
			}

			return true;
		}
		else
		{
			// Initialise variables.
			jimport('core.client.helper');
			$FTPOptions = JClientHelper::getCredentials('ftp');

			if ($FTPOptions['enabled'] == 1)
			{
				// Connect the FTP client
				jimport('core.client.ftp');
				$ftp = JFTP::getInstance($FTPOptions['host'], $FTPOptions['port'], null, $FTPOptions['user'], $FTPOptions['pass']);

				// Translate path for the FTP account
				$src = JPath::clean(str_replace(JPATH_ROOT, $FTPOptions['root'], $src), '/');
				$dest = JPath::clean(str_replace(JPATH_ROOT, $FTPOptions['root'], $dest), '/');

				// Use FTP rename to simulate move
				if (!$ftp->rename($src, $dest))
				{
					JError::raiseWarning(21, JText::_('JLIB_FILESYSTEM_ERROR_RENAME_FILE'));

					return false;
				}
			}
			else
			{
				if (!@ rename($src, $dest))
				{
					JError::raiseWarning(21, JText::_('JLIB_FILESYSTEM_ERROR_RENAME_FILE'));

					return false;
				}
			}

			return true;
		}
	}

	/**
	 * Read the contents of a file
	 *
	 * @param   string   $filename   The full file path
	 * @param   boolean  $incpath    Use include path
	 * @param   integer  $amount     Amount of file to read
	 * @param   integer  $chunksize  Size of chunks to read
	 * @param   integer  $offset     Offset of the file
	 *
	 * @return  mixed  Returns file contents or boolean False if failed
	 *
	 * @since   11.1
	 */
	public static function read($filename, $incpath = false, $amount = 0, $chunksize = 8192, $offset = 0)
	{
		// Initialise variables.
		$data = null;
		if ($amount && $chunksize > $amount)
		{
			$chunksize = $amount;
		}

		if (false === $fh = fopen($filename, 'rb', $incpath))
		{
			JError::raiseWarning(21, JText::sprintf('JLIB_FILESYSTEM_ERROR_READ_UNABLE_TO_OPEN_FILE', $filename));

			return false;
		}

		clearstatcache();

		if ($offset)
		{
			fseek($fh, $offset);
		}

		if ($fsize = @ filesize($filename))
		{
			if ($amount && $fsize > $amount)
			{
				$data = fread($fh, $amount);
			}
			else
			{
				$data = fread($fh, $fsize);
			}
		}
		else
		{
			$data = '';
			// While it's:
			// 1: Not the end of the file AND
			// 2a: No Max Amount set OR
			// 2b: The length of the data is less than the max amount we want
			while (!feof($fh) && (!$amount || strlen($data) < $amount))
			{
				$data .= fread($fh, $chunksize);
			}
		}
		fclose($fh);

		return $data;
	}

	/**
	 * Write contents to a file
	 *
	 * @param   string   $file         The full file path
	 * @param   string   &$buffer      The buffer to write
	 * @param   boolean  $use_streams  Use streams
	 *
	 * @return  boolean  True on success
	 *
	 * @since   11.1
	 */
	public static function write($file, &$buffer, $use_streams = false)
	{
		@set_time_limit(ini_get('max_execution_time'));

		// If the destination directory doesn't exist we need to create it
		if (!file_exists(dirname($file)))
		{
			jimport('core.filesystem.folder');
			JFolder::create(dirname($file));
		}

		if ($use_streams)
		{
			$stream = JFactory::getStream();
			// Beef up the chunk size to a meg
			$stream->set('chunksize', (1024 * 1024 * 1024));

			if (!$stream->writeFile($file, $buffer))
			{
				JError::raiseWarning(21, JText::sprintf('JLIB_FILESYSTEM_ERROR_WRITE_STREAMS', $file, $stream->getError()));
				return false;
			}

			return true;
		}
		else
		{
			// Initialise variables.
			$FTPOptions = JClientHelper::getCredentials('ftp');

			if ($FTPOptions['enabled'] == 1)
			{
				// Connect the FTP client
				jimport('core.client.ftp');
				$ftp = JFTP::getInstance($FTPOptions['host'], $FTPOptions['port'], null, $FTPOptions['user'], $FTPOptions['pass']);

				// Translate path for the FTP account and use FTP write buffer to file
				$file = JPath::clean(str_replace(JPATH_ROOT, $FTPOptions['root'], $file), '/');
				$ret = $ftp->write($file, $buffer);
			}
			else
			{
				$file = JPath::clean($file);
				$ret = is_int(file_put_contents($file, $buffer)) ? true : false;
			}

			return $ret;
		}
	}

	/**
	 * Moves an uploaded file to a destination folder
	 *
	 * @param   string   $src          The name of the php (temporary) uploaded file
	 * @param   string   $dest         The path (including filename) to move the uploaded file to
	 * @param   boolean  $use_streams  True to use streams
	 *
	 * @return  boolean  True on success
	 *
	 * @since   11.1
	 */
	public static function upload($src, $dest, $use_streams = false)
	{
		// Ensure that the path is valid and clean
		$dest = JPath::clean($dest);

		// Create the destination directory if it does not exist
		$baseDir = dirname($dest);

		if (!file_exists($baseDir))
		{
			jimport('core.filesystem.folder');
			JFolder::create($baseDir);
		}

		if ($use_streams)
		{
			$stream = JFactory::getStream();

			if (!$stream->upload($src, $dest))
			{
				JError::raiseWarning(21, JText::sprintf('JLIB_FILESYSTEM_ERROR_UPLOAD', $stream->getError()));
				return false;
			}

			return true;
		}
		else
		{
			// Initialise variables.
			$FTPOptions = JClientHelper::getCredentials('ftp');
			$ret = false;

			if ($FTPOptions['enabled'] == 1)
			{
				// Connect the FTP client
				jimport('core.client.ftp');
				$ftp = JFTP::getInstance($FTPOptions['host'], $FTPOptions['port'], null, $FTPOptions['user'], $FTPOptions['pass']);

				// Translate path for the FTP account
				$dest = JPath::clean(str_replace(JPATH_ROOT, $FTPOptions['root'], $dest), '/');

				// Copy the file to the destination directory
				if (is_uploaded_file($src) && $ftp->store($src, $dest))
				{
					unlink($src);
					$ret = true;
				}
				else
				{
					JError::raiseWarning(21, JText::_('JLIB_FILESYSTEM_ERROR_WARNFS_ERR02'));
				}
			}
			else
			{
				if (is_writeable($baseDir) && move_uploaded_file($src, $dest))
				{
					// Short circuit to prevent file permission errors
					if (JPath::setPermissions($dest))
					{
						$ret = true;
					}
					else
					{
						JError::raiseWarning(21, JText::_('JLIB_FILESYSTEM_ERROR_WARNFS_ERR01'));
					}
				}
				else
				{
					JError::raiseWarning(21, JText::_('JLIB_FILESYSTEM_ERROR_WARNFS_ERR02'));
				}
			}

			return $ret;
		}
	}

	/**
	 * Wrapper for the standard file_exists function
	 *
	 * @param   string  $file  File path
	 *
	 * @return  boolean  True if path is a file
	 *
	 * @since   11.1
	 */
	public static function exists($file)
	{
		return is_file(JPath::clean($file));
	}

	/**
	 * Returns the name, without any path.
	 *
	 * @param   string  $file  File path
	 *
	 * @return  string  filename
	 *
	 * @since   11.1
	 */
	public static function getName($file)
	{
		// Convert back slashes to forward slashes
		$file = str_replace('\\', '/', $file);
		$slash = strrpos($file, '/');
		if ($slash !== false)
		{

			return substr($file, $slash + 1);
		}
		else
		{

			return $file;
		}
	}

	/**
	 * Attempt to get the mime type from a file. This method is horribly
	 * unreliable, due to PHP being horribly unreliable when it comes to
	 * determining the mime type of a file.
	 *
	 *     $mime = WSFile::mime($file);
	 *
	 * @param   string  file name or path
	 * @return  string  mime type on success
	 * @return  FALSE   on failure
	 */
	public static function getMime($filename)
	{
		// Get the complete path to the file
		$filename = realpath($filename);

		// Get the extension from the filename
		$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

		if (preg_match('/^(?:jpe?g|png|[gt]if|bmp|swf)$/', $extension))
		{
			// Use getimagesize() to find the mime type on images
			$file = getimagesize($filename);

			if (isset($file['mime']))
			{
				return $file['mime'];
			}
		}

		if (class_exists('finfo', FALSE))
		{
			if ($info = new finfo(defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME))
			{
				return $info->file($filename);
			}
		}

		if (ini_get('mime_magic.magicfile') && function_exists('mime_content_type'))
		{
			// The mime_content_type function is only useful with a magic file
			return mime_content_type($filename);
		}

		if ( ! empty($extension))
		{
			return self::getMimeByExt($extension);
		}

		// Unable to find the mime-type
		return FALSE;
	}

	/**
	 * Return the mime type of an extension.
	 *
	 *     $mime = WSFile::mime_by_ext('png'); // "image/png"
	 *
	 * @param   string  extension: php, pdf, txt, etc
	 * @return  string  mime type on success
	 * @return  FALSE   on failure
	 */
	public static function getMimeByExt($extension)
	{
		// Load all of the mime types
		return isset(self::$_mimes[$extension]) ? self::$_mimes[$extension] : FALSE;
	}










}
