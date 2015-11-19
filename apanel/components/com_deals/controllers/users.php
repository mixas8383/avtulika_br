<?php
/**
 * @package     	LongCMS.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('core.application.component.controlleradmin');

/**
 * Users list controller class.
 *
 * @package     	LongCMS.Administrator
 * @subpackage  com_users
 * @since       1.6
 */
class DealsControllerUsers extends JControllerAdmin
{
	/**
	 * @var    string  The prefix to use with controller messages.
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_DEALS_USERS';

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @return  UsersControllerUsers
	 *
	 * @since   1.6
	 * @see     JController
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('block',			'changeBlock');
		$this->registerTask('unblock',		'changeBlock');
		$this->registerTask('subscribe',		'changeSubscribe');
		$this->registerTask('unsubscribe',	'changeSubscribe');
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since	1.6
	 */
	public function getModel($name = 'User', $prefix = 'DealsModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}


	public function exportExcel()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		ini_set('memory_limit', '4096M');
		ini_set('max_execution_time', 300);


		// Get the model.
		$model = $this->getModel('Users');

		jimport('PHPExcel.PHPExcel');

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();


		// Set document properties
		$objPHPExcel->getProperties()->setCreator("LongCMS")
									 ->setLastModifiedBy("LongCMS")
									 ->setTitle("Brao Users")
									 ->setSubject("Brao Users Document")
									 ->setDescription("Users")
									 ->setKeywords("Brao, Users, Excel")
									 ->setCategory("Brao Users file");

		$sheet = $objPHPExcel->setActiveSheetIndex(0);


		$items = $model->getUsers();




		// addd header


		$sheet->setCellValue('A1', 'სახელი');
		$sheet->setCellValue('B1', 'გვარი');
		$sheet->setCellValue('C1', 'პირადი ნომერი');
		$sheet->setCellValue('D1', 'ელ. ფოსტა');
		$sheet->setCellValue('E1', 'ტელეფონი');
		$sheet->setCellValue('F1', 'ბალანსი');
		$sheet->setCellValue('G1', 'სტატუსი');
		$sheet->setCellValue('H1', 'რეგისტრაციის თარიღი');
		$sheet->setCellValue('I1', 'ID');






		$row = 2;
		foreach ($items as $item) {

			$sheet->setCellValue('A'.$row, $item->name);
			$sheet->setCellValue('B'.$row, $item->surname);
			$sheet->setCellValue('C'.$row, $item->persNumber);
			$sheet->setCellValue('D'.$row, $item->email);

			$phone = array();
			if ($item->phone) {
				$phone[] = $item->phone;
			}
			if ($item->mobile) {
				$phone[] = $item->mobile;
			}
			$sheet->setCellValue('E'.$row, implode(', ', $phone));

			$balance = Balance::convertAsMajor($item->balance);
			$sheet->setCellValue('F'.$row, $balance);

			$status = $item->block == 1 ? 'აქტიური' : 'პასიური';
			$sheet->setCellValue('G'.$row, $status);

			$sheet->setCellValue('H'.$row, $item->registerDate);
			$sheet->setCellValue('I'.$row, $item->id);

			$row++;
		}

		$max_row = $sheet->getHighestRow();
		$max_column = $sheet->getHighestColumn();

		$columns = range('A', $max_column);
		$rows = range(1, $max_row);

		// set default styles
		$sheet->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$sheet->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$sheet->getDefaultStyle()->getAlignment()->setWrapText(true);
		$sheet->getDefaultStyle()->applyFromArray(array("font" => array('size' => 10)));


		foreach($columns as $column) {
			$width = 30;
			$sheet->getColumnDimension($column)->setWidth($width);
			$sheet->getStyle($column.'1')->getFont()->setBold(true);

			if (in_array($column, array('E', 'F', 'I', 'J', 'K', 'L'))) {
				//$sheet->getStyle($column.'1:'.$column.$max_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				//$sheet->getStyle($column.'2:'.$column.$max_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			}


		}

		foreach($rows as $row) {
			if ($row == 1) {
				$sheet->getRowDimension($row)->setRowHeight(30);
			} else {
				$sheet->getRowDimension($row)->setRowHeight(20);
			}
		}

		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Users');
		$objPHPExcel->getActiveSheet()->setShowGridLines(true);

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);

		$filename = 'Users_'.date('Y-m-d_H:i:s');


		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
		//header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');


		$objWriter->save('php://output');
		die;

	}


	/**
	 * Method to change the block status on a record.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function changeBlock()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$ids	= JRequest::getVar('cid', array(), '', 'array');
		$values	= array('block' => 1, 'unblock' => 0);
		$task	= $this->getTask();
		$value	= JArrayHelper::getValue($values, $task, 0, 'int');

		if (empty($ids))
		{
			JError::raiseWarning(500, JText::_('COM_USERS_USERS_NO_ITEM_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Change the state of the records.
			if (!$model->block($ids, $value))
			{
				JError::raiseWarning(500, $model->getError());
			}
			else
			{
				if ($value == 1)
				{
					$this->setMessage(JText::plural('COM_USERS_N_USERS_BLOCKED', count($ids)));
				}
				elseif ($value == 0)
				{
					$this->setMessage(JText::plural('COM_USERS_N_USERS_UNBLOCKED', count($ids)));
				}
			}
		}

		$this->setRedirect('index.php?option=com_deals&view=users');
	}


	public function changeSubscribe()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$ids	= JRequest::getVar('cid', array(), '', 'array');
		$values	= array('subscribe' => 1, 'unsubscribe' => 0);
		$task	= $this->getTask();
		$value	= JArrayHelper::getValue($values, $task, 0, 'int');

		if (empty($ids))
		{
			JError::raiseWarning(500, JText::_('COM_USERS_USERS_NO_ITEM_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Change the state of the records.
			if (!$model->subscribe($ids, $value))
			{
				JError::raiseWarning(500, $model->getError());
			}
			else
			{
				if ($value == 1)
				{
					$this->setMessage(JText::plural('COM_USERS_N_USERS_SUBSCRIBED', count($ids)));
				}
				elseif ($value == 0)
				{
					$this->setMessage(JText::plural('COM_USERS_N_USERS_UNSUBSCRIBED', count($ids)));
				}
			}
		}

		$this->setRedirect('index.php?option=com_deals&view=users');
	}



	/**
	 * Method to activate a record.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function activate()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$ids	= JRequest::getVar('cid', array(), '', 'array');

		if (empty($ids))
		{
			JError::raiseWarning(500, JText::_('COM_USERS_USERS_NO_ITEM_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Change the state of the records.
			if (!$model->activate($ids))
			{
				JError::raiseWarning(500, $model->getError());
			}
			else
			{
				$this->setMessage(JText::plural('COM_USERS_N_USERS_ACTIVATED', count($ids)));
			}
		}

		$this->setRedirect('index.php?option=com_deals&view=users');
	}
}
