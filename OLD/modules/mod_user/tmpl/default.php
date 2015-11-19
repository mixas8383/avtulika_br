<?php
/**
 * @package	LongCMS.Site
 * @subpackage	mod_menu
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

$user = PDeals::getUser();

// Note. It is important to remove spaces between elements.
JHtml::_('behavior.keepalive');
?>
<div class="mod_user<?php echo $moduleclass_sfx ?>">
<?php

if ($user->get('guest')) {
	$regItemid = JMenu::getItemid('com_users', 'registration');
	$logItemid = JMenu::getItemid('com_users', 'login');
	$regLink = JRoute::_('index.php?option=com_users&view=registration&Itemid='.$regItemid);
	$logLink = JRoute::_('index.php?option=com_users&view=login&Itemid='.$logItemid);
	$fbLink = JRoute::_('index.php?option=com_users&task=user.loginFB');
	?>
	<div class="mod_user_but<?php echo $moduleclass_sfx ?>">
		<span class="mod_user_log">
			<a href="<?php echo $logLink ?>">
				<?php //echo JText::_('JLOGIN');?>
			</a>
		</span>
		<span class="mod_user_reg">
			<a href="<?php echo $regLink ?>">
				<?php //echo JText::_('JREGISTER');?>
			</a>
		</span>
		<div class="cls"></div>
		<?php
		$user_params = JComponentHelper::getParams('com_users');
		$allow_fb_authorization = $user_params->get('allow_fb_authorization');
		if ($allow_fb_authorization) {
			?>
			<div class="mod_user_fb_log">
				<a href="<?php echo $fbLink ?>">
				</a>
			</div>
			<?php
		}
		?>
	</div>
	<?php
} else {

	$name = $user->get('name').' '.$user->get('surname');
	$id = $user->get('id');
	$balance = $user->getBalance();


	$profile_itemid = JMenu::getItemid('com_users', 'profile', 'edit');
	$transactions_itemid = JMenu::getItemid('com_deals', 'transactions');
	//$deposit_itemid = JMenu::getItemid('com_deals', 'deposit');
	$logout_itemid = JMenu::getItemid('com_users', 'logout');


	$profile_link = JRoute::_('index.php?option=com_users&view=profile&layout=edit&Itemid='.$profile_itemid);
	$transactions_link = JRoute::_('index.php?option=com_deals&view=transactions&Itemid='.$transactions_itemid);
	//$deposit_link = JRoute::_('index.php?option=com_deals&view=deposit&Itemid='.$deposit_itemid);
	$logout_link = JRoute::_('index.php?option=com_users&task=user.logout&return='.$return.'&Itemid='.$logout_itemid);
	?>
	<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form">

		<div class="mod_user_names">
			<span class="mod_user_name">
				<?php echo $name ?>
			</span>
		</div>
		<div class="mod_user_balance">
			<span class="mod_user_id">
				<?php echo JText::_('MOD_USER_ID') ?>
			</span>
			<span class="mod_user_idnumber">
				<?php echo $id ?>
			</span>
			<span class="mod_user_balance">
				<?php echo JText::_('MOD_USER_BALANCE') ?>
			</span>
			<span class="mod_user_balancnumbere">
				<?php echo $balance; ?>
			</span>
			<span class="mod_user_balancnumbere">
				<?php echo JText::_('GEL') ?>
			</span>
		</div>

		<div class="mod_user_toolbar">
			<span class="mod_user_profile">
				<a href="<?php echo $profile_link ?>" title="<?php echo JText::_('MOD_USER_PROFILE');?>">
					<?php //echo JText::_('MOD_USER_PROFILE');?>
					<img src="templates/longcms/images/icons/profile.png" alt=" "/>
				</a>
			</span>
			<span class="mod_user_transactions">
				<a href="<?php echo $transactions_link ?>" title="<?php echo JText::_('MOD_USER_TRANSACTIONS');?>">
					<?php //echo JText::_('MOD_USER_CART');?>
					<img src="templates/longcms/images/icons/transactions.png" alt=" "/>
				</a>
			</span>

			<span class="mod_user_logout">
				<a href="<?php echo $logout_link ?>" title="<?php echo JText::_('MOD_USER_LOGOUT');?>">
					<?php //echo JText::_('MOD_USER_LOGOUT');?>
					<img src="templates/longcms/images/icons/logout.png" alt=" "/>
				</a>
			</span>


		</div>

	</form>
	<?php
}
?>
</div>
