<?php
defined('_JEXEC') or die;
$app = JFactory::getApplication();
$rand = mt_rand();
$offline = $app->getCfg('offline');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<jdoc:include type="head" />
		<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/loader.php<?php echo JDEBUG?'?'.$rand:''; ?>" type="text/css" />
	</head>
	<body class="offline_body">
		<jdoc:include type="message" />
		<div id="frame" class="outline">
			<?php if ($app->getCfg('offline_image')) : ?>
				<?php endif; ?>
                <div class="offline_logo">
				<img src="templates/longcms/images/logo.png" alt="<?php echo htmlspecialchars($app->getCfg('sitename')); ?>" />
				</div>
                <h1>
					<?php //echo htmlspecialchars($app->getCfg('sitename')); ?>
				</h1>
                <div class="offline_text">
				<?php if ($app->getCfg('display_offline_message', 1) == 1 && str_replace(' ', '', $app->getCfg('offline_message')) != ''): ?>
				<p>
					<?php echo $app->getCfg('offline_message'); ?>
				</p>
				<?php elseif ($app->getCfg('display_offline_message', 1) == 2 && str_replace(' ', '', JText::_('JOFFLINE_MESSAGE')) != ''): ?>
				<p>
					<?php echo JText::_('JOFFLINE_MESSAGE'); ?>
				</p>
			<?php  endif; ?>
            </div>

            	<?php
            	if ($offline != 3) {
	            	?>
				<form action="<?php echo JRoute::_('index.php', true); ?>" method="post" id="form-login">
					<fieldset class="input">
						<p id="form-login-username">
							<label for="username"><?php echo JText::_('JGLOBAL_USERNAME') ?></label>
							<input name="username" id="username" type="text" class="inputbox" alt="<?php echo JText::_('JGLOBAL_USERNAME') ?>" size="18" />
						</p>
						<p id="form-login-password">
							<label for="passwd"><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>
							<input type="password" name="password" class="inputbox" size="18" alt="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" id="passwd" />
						</p>
						<p id="form-login-remember">
							<label for="remember"><?php echo JText::_('JGLOBAL_REMEMBER_ME') ?></label>
							<input type="checkbox" name="remember" class="inputbox" value="yes" alt="<?php echo JText::_('JGLOBAL_REMEMBER_ME') ?>" id="remember" />
						</p>
	                    <div class="off_button">
	                        <label>&nbsp;</label>
	                        <div class="button_off">
	                        <input type="submit" name="Submit" class="button_login" value="<?php echo JText::_('JLOGIN') ?>" />
	                        </div>
						</div>
	                    <input type="hidden" name="option" value="com_users" />
						<input type="hidden" name="task" value="user.login" />
						<input type="hidden" name="return" value="<?php echo base64_encode(JURI::base()) ?>" />
						<?php echo JHtml::_('form.token'); ?>
					</fieldset>
				</form>
	            	<?php
            	}
            	?>

		</div>
		<script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/script.js<?php echo JDEBUG?'?'.$rand:''; ?>" type="text/javascript"></script>

	</body>
</html>
