<?php
/**
* @version		$Id: excelxml.php 262 2012-01-16 17:52:00Z a.kikabidze $
* @package	LongCMS.Framework.WSLib
* @copyright	Copyright (C) 2009 - 2012 LongCMS Team. All rights reserved.
* @license		GNU General Public License version 2 or later
*/
defined('JPATH_PLATFORM') or die('Restricted access');

jimport('core.filesystem.folder');
jimport('core.filesystem.file');


class JExcelXml
{
	private $_foldername;
	private $_filename;
	private $_col_count;
	private $_row_count;
	private $_rows;

	private $_column_width;
	private $_row_array;
	private $_nl;
	private $_tab;

	private $_styles;

	public function __construct($file, $col_count, $row_count)
	{

		$filename = JFile::getName($file);
		$this->_filename = JFile::makeSafe($filename);

		$foldername = dirname($file);
		$foldername = str_replace('/', DS, $foldername);
		$this->_foldername = $foldername;

		if (!JFolder::exists(JPATH_SITE.DS.$this->_foldername))
		{
			JFolder::create(JPATH_SITE.DS.$this->_foldername, 0777);
		}

		// create file
		$this->_write();

		$this->_column_width = 150;
		$this->_col_count = $col_count;
		$this->_row_count = $row_count;
		$this->_nl = "\n";
		$this->_tab = "\t";
	}

	public function start()
	{
		$header = $this->_getHeader();
		$header .= $this->_getWorkSheetHeader();
		$header .= $this->_getTableHeader();
		$header .= $this->_getColumns();
		$this->_write($header);


	}

    private function _getTableHeader()
	{
		$table = '<Table ss:ExpandedColumnCount="'.$this->_col_count.'" ss:ExpandedRowCount="'.$this->_row_count.'" x:FullColumns="1" x:FullRows="1">'.$this->_nl;
		return $table;
	}


    private function _getWorkSheetHeader()
	{
		$table = '<Worksheet ss:Name="Worksheet1">'.$this->_nl;
		return $table;
	}

    private function _getColumns()
	{
		$columns = '';
		for($i=1; $i<=$this->_col_count; $i++)
		{
			$columns .= '<Column ss:Index="'.$i.'" ss:Width="'.$this->_column_width.'" />'.$this->_nl;
		}
		return $columns;
	}

    private function _getHeader()
	{
        $styles = '';
		if (!empty($this->_styles) && is_array($this->_styles))
		{
            $styles = implode('', $this->_styles);
        }
        ob_start();
		echo '<?xml version="1.0"?>';
		echo '<?mso-application progid="Excel.Sheet"?>';
		?>

<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:x="urn:schemas-microsoft-com:office:excel"
xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
xmlns:html="http://www.w3.org/TR/REC-html40">
    <OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office">
        <DownloadComponents/>
        <LocationOfComponents HRef="file:///\\"/>
    </OfficeDocumentSettings>
    <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
        <WindowHeight>12525</WindowHeight>
        <WindowWidth>15195</WindowWidth>
        <WindowTopX>480</WindowTopX>
        <WindowTopY>120</WindowTopY>
        <ActiveSheet>0</ActiveSheet>
        <ProtectStructure>False</ProtectStructure>
        <ProtectWindows>False</ProtectWindows>
    </ExcelWorkbook>
    <Styles>
        <Style ss:ID="Default" ss:Name="Normal">
            <Alignment ss:Vertical="Bottom"/>
            <Borders/>
            <Font/>
            <Interior/>
            <NumberFormat/>
            <Protection/>
        </Style>
        <Style ss:ID="bold">
            <Font ss:Bold="1" />
        </Style>
        <?php echo $styles ?>
    </Styles>
		<?php
		$header = ob_get_clean();
		return $header;
    }



    public function finish($output = false)
	{
		$footer = '</Table>'.$this->_nl;

		$footer .= '<WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
<ProtectObjects>False</ProtectObjects>
<ProtectScenarios>False</ProtectScenarios>
</WorksheetOptions>'.$this->_nl;

		$footer .= '</Worksheet>'.$this->_nl;

		$footer .= '</Workbook>';
		$this->_write($footer);
		if ($output)
		{
			$this->_download();
		}
    }

    public function addRow($array, $style = null)
	{
        if (!is_array($array))
		{
            // Asume the delimiter is , or ;
            $array = str_replace(',', ';', $array);
            $array = explode(';', $array);
        }
        if (!is_null($style))
		{
            $style_array = array('attach_style' => $style);
            $array = array_merge($array, $style_array);
        }

        $this->_row_array[] = $array;
	}

	public function storeRow()
	{
        $row_array = $this->_row_array;
        if (!is_array($row_array)) return;

        $cnt = 0;
        $row_cell = array();
        foreach($row_array as $row_data)
		{
            $cnt++;

            // See if there are styles attached
            $style = null;
            if (!empty($row_data['attach_style']))
			{
                $style = $row_data['attach_style'];
                unset($row_data['attach_style']);
            }

            // Store the counter of rows
            $this->counters['rows'] = $cnt;

            $cells = '';
            $cell_cnt = 0;
            foreach($row_data as $key => $cell_data)
			{
                $cell_cnt++;
                $cells .= $this->_nl.$this->_prepareCell($cell_data, $style);
            }

            // Store the number of cells in row
            $row_cell[$cnt][] = $cell_cnt;

            $this->rows[] = '<Row>'.$cells.$this->_nl.'</Row>'.$this->_nl;
        }

        // Find out max cells in all rows
        $max_cells = max($row_cell);
        $this->counters['cols'] = $max_cells[0];


		$this->_write(implode('', $this->rows));

	}

    private function _prepareCell($cell_data, $style = null)
	{

        $str = str_replace("\t", " ", $cell_data);          // replace tabs with spaces
        $str = str_replace("\r\n", "\n", $str);             // replace windows-like new-lines with unix-like
        $str = str_replace('"',  '""', $str);               // escape quotes so we support multiline cells now
        preg_match('#\"\"#', $str) ? $str = '"'.$str.'"' : $str; // If there are double doublequotes, encapsulate str in doublequotes
		$merge = '';
        // Formating: bold
        if (!is_null($style))
		{
            $style = ' ss:StyleID="'.$style.'"';
        }
		elseif (preg_match('/^\*([^\*]+)\*$/', $str, $out))
		{
            $style  = ' ss:StyleID="bold"';
            $str    = $out[1];
        }

        if (preg_match('/\|([\d]+)$/', $str, $out))
		{
            $merge  = ' ss:MergeAcross="'.$out[1].'"';
            $str    = str_replace($out[0], '', $str);
        }
        // Get type
        $type = preg_match('/^([\d]+)$/', $str) ? 'Number' : 'String';

        return '<Cell'.$style.$merge.'><Data ss:Type="'.$type.'">'.$str.'</Data></Cell>';
    }

	public function addStyle($style_id, $parameters)
	{
        $interiors = '';
        $fonts = '';
        foreach($parameters as $param => $data)
		{
            switch($param)
			{
                case 'size':
                    $font['ss:Size'] = $data;
                	break;

                case 'font':
                    $font['ss:FontName'] = $data;
                	break;

                case 'color':
                case 'colour':
                    $font['ss:Color'] = $data;
                	break;

                case 'bgcolor':
                    $interior['ss:Color'] = $data;
                	break;

                case 'bold':
                    $font['ss:Bold'] = $data;
                	break;

                case 'italic':
                    $font['ss:Italic'] = $data;
                	break;

                case 'strike':
                    $font['ss:StrikeThrough'] = $data;
                	break;
            }
        }
        if (is_array($interior))
		{
            foreach($interior as $param => $value)
			{
                $interiors .= ' '.$param.'="'.$value.'"';
            }
            $interior = '<Interior ss:Pattern="Solid"'.$interiors.' />'.$this->_nl;
        }
        if (is_array($font))
		{
            foreach($font as $param => $value)
			{
                $fonts .= ' '.$param.'="'.$value.'"';
            }
            $font = '<Font'.$fonts.' />'.$this->_nl;
        }
        $this->_styles[] = '
        <Style ss:ID="'.$style_id.'">
            '.$interior.$font.'
        </Style>';
    }


    private function _write($str = '')
	{
        $write = file_put_contents(JPATH_SITE.DS.$this->_foldername.DS.$this->_filename, $str, FILE_APPEND);
		return $write;
    }

    private function _download()
	{
		$content = file_get_contents(JPATH_SITE.DS.$this->_foldername.DS.$this->_filename);
		header("Cache-Control: public, must-revalidate");
        header("Pragma: no-cache");
        header("Content-Length: ".strlen($content) );
        header("Content-Type: application/vnd.ms-excel");

		header('Content-Disposition: attachment; filename="'.$this->_filename.'"');
		header("Content-Transfer-Encoding: binary");



        echo $content;
        exit;
    }


    public function getLink()
	{
		return JURI::root().str_replace(DS, '/', $this->_foldername).'/'.$this->_filename;
    }




}
