<?php
/**
 * @version   $Id: default.php 15 2013-12-26 18:37:15Z Logigroup $
 * @package     Joomla16.Tutorials
 * @subpackage  Components
 * @copyright   Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @author      Logigroup
 * @license http://www.gnu.org/licenses GNU/GPL
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$db=JFactory::getDBO();


function download_send_headers($filename) {
 // disposition / encoding on response body
    header("Content-type: application/vnd.ms-excel; charset=ISO-8859-1" );
    header("Content-Disposition: attachment;filename={$filename}");
   
}


function array2csv(array &$array)
{
   if (count($array) == 0) {
     return null;
   }
   ob_start();
   $df = fopen("php://output", 'w');
     
     foreach ($array as $row) {
      fputcsv($df, $row);
     }

   fclose($df);
   return ob_get_clean();
}


if(isset($_GET['export']) && $_GET['export']==1){


$list[0][0]= JText::_('COM_LG_USEREXPORT_USER');
$list[0][1] =JText::_('COM_LG_USEREXPORT_LOGIN');
$list[0][2] =JText::_('COM_LG_USEREXPORT_EMAIL');
$list[0][3] =JText::_('COM_LG_USEREXPORT_GROUP');
$list[0][4] =JText::_('COM_LG_USEREXPORT_ACTIV');
$list[0][5] =JText::_('COM_LG_USEREXPORT_ACTIVE');


$db->setQuery("
  SELECT  users.name,users.username,users.email,users.email,users.block,users.activation,groups.title
  from  #__users as users,#__user_usergroup_map as users_groups,#__usergroups as groups
  where users_groups.user_id=users.id and users_groups.group_id=groups.id and users_groups.group_id<>8 ");
$rows = $db->loadObjectList();

 $i=1;

 foreach($rows as $row) 
   {
    if($row->activation==''){
      $activation=1;
    }
    else{
      $activation=0;
    }
     if($row->block==0){
     $actif=1;
    }
    else{
      $actif=0;
    }

  $list[$i][0] =$row->name;
  $list[$i][1] =$row->username;
  $list[$i][2] =$row->email;
  $list[$i][3] =$row->title;
  $list[$i][4] =$actif;
  $list[$i][5] =$activation;
  
  $i++;

  }

download_send_headers("list_users.csv");
echo array2csv($list);
die();

echo '<script>window.location.href="index.php?option=com_lg-userexport";</script>';

}



?>

<form action="<?php echo JRoute::_('index.php?option=com_lg-userexport'); ?>" method="post" name="adminForm">
<div style=" font-size:14px; line-height:28px;">
<p>
  
   <?php echo JText::_('COM_LG_USEREXPORT_DESCRIPTION');?> 
</p>
</div>
<a style=" font-size:13px; display:block; width:160px; text-align:center; padding:10px 5px; background:#008fcb; color:#fff; margin-left:10px; border-radius:3px;"
 href="index.php?option=com_lg-userexport&export=1"><?php echo JText::_('COM_LG_USEREXPORT_TEXT_LIEN');?></a>

<p style="text-align:center;">
<br><br>
<?php echo JText::_('COM_LG_USEREXPORT_COPYRIGHT');?>
</p>


<input type="hidden" name="task" value="lg-userexport" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo JHtml::_('form.token'); ?>         
</form>
