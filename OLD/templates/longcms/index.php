<?php
/**
 * @package                	LongCMS.Site
 * @subpackage			Templates.longcms
 * @copyright        		Copyright (C) 2009 - 2012 LongCMS Team. All rights reserved.
 * @license                	GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// get params
$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$lang = JFactory::getLanguage();

$logo = $this->params->get('logo');
$link_logo = $this->params->get('link_logo');
$logo_title = htmlspecialchars($app->getCfg('sitename'), ENT_QUOTES);
$logo_url = JURI::root().'index.php';


$rand = mt_rand();
$lang_prefix = $lang->getTagPrefix();
$lang_code = $lang->getTag();
$option = JRequest::getCmd('option');
$view = JRequest::getCmd('view');
$layout = JRequest::getCmd('layout');


?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml">
	<head>
		<jdoc:include type="head" />
		<link rel="stylesheet" href="templates/<?php echo $this->template ?>/css/style.css<?php echo JDEBUG?'?'.$rand:'?v=6'; ?>"/>
		<!--[if IE 9.0]>
			<link rel="stylesheet" href="templates/<?php echo $this->template ?>/css/ie9.css<?php echo JDEBUG?'?'.$rand:''; ?>"/>
		<![endif]-->
		<!--[if IE 7.0]>
			<link rel="stylesheet" href="templates/<?php echo $this->template ?>/css/ie7.css<?php echo JDEBUG?'?'.$rand:''; ?>"/>
		<![endif]-->
		<script type="text/javascript">
			JMain = {
				<?php
				if (JDEBUG) {
					?>
					debug : true,
					<?php
				} else {
					?>
					debug : false,
					<?php
				}
				?>
				lang_prefix : "<?php echo $lang_prefix ?>",
				lang_code : "<?php echo $lang_code ?>",
				option : "<?php echo $option ?>",
				view : "<?php echo $view ?>",
				layout : "<?php echo $layout ?>"
			}
		</script>
	</head>

	<body>
		<div id="fb-root"></div>
		<div id="all">
			<div class="all">
				<div id="center">
					<div id="header">
						<div class="header">
							<div id="header_left">
								<div class="header_left">
									<div id="site_logo">
										<a href="<?php echo $logo_url ?>">
											<img border="0" title="<?php echo $logo_title ?>" alt="<?php echo $logo_title ?>" src="<?php echo $logo ?>" />
										</a>
									</div>
								</div>
							</div>
							<div id="header_right">
								<div class="header_right">
                                    <div class="header_rightin">
                                        <jdoc:include type="modules" name="login"/>
                                    </div>
								</div>
								<div class="header_banner">
									<jdoc:include type="modules" name="header_banner"/>
								</div>
                                <div class="cls"></div>
							</div>
							<div class="cls"></div>
						</div>
					</div>
					<div id="mainmenu">
						<div class="mainmenu">
							<jdoc:include type="modules" name="menu"/>
						</div>
					</div>
					<div id="content">
						<div class="content">
							<noscript>
								<div align="center" class="jserror">
									To See This Web Page, You Need JavaScript!!!
								</div>
							</noscript>
							<jdoc:include type="message" />
							<jdoc:include type="modules" name="content" style="cont"/>
                            <div class="content_in">
                                <jdoc:include type="component" />
                            </div>
							<jdoc:include type="modules" name="content1" style="cont"/>
						</div>
					</div>
					<div id="footer">
						<div class="footer">
							<div class="footer_in">
								<div id="footer_menu">
									<div class="footer_menu">
										<jdoc:include type="modules" name="footer_menu" />
                                        <div class="cls"></div>
									</div>
                                    <div id="footer_top_right">
                                    	<div class="footer_top_right">
                                        	<jdoc:include type="modules" name="foot_info" style="foot" />
                                        </div>
                                    </div>
                                    <div class="cls"></div>
								</div>
								<div id="copyright">
									<div class="copyright">
										<jdoc:include type="modules" name="copyright" />
									</div>
								</div>
								<div class="counter">
									<jdoc:include type="modules" name="counter" />
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div id="side_phone">
			<p>
				<b>ტელეფონი:</b>
				<br />214-86-11;<br />592-83-55-44;
				<br /><span style="font-size:11px">(10:00-20:00 საათამდე)</span>
				<br />574-80-80-55;
				<br /><span style="font-size:11px">(20:00-23:00 საათამდე)</span>

			</p>
			<br />
			<p>
				<b>მისამართი:</b><br />
				თბილისი,<br />თამარაშვილის #19
			</p>
		</div>


		<a href="#all" id="top-link"></a>
		<jdoc:include type="modules" name="debug" />
		<script src="templates/<?php echo $this->template ?>/js/script.js<?php echo JDEBUG?'?'.$rand:''; ?>"></script>
		<?php
		$script = '';
		if ($app->getCfg('fb_sdk', 0)) {
			$appid = $app->getCfg('fb_og_appid', '');
			$lang_tag = $lang->getTag(true);
			$lang_prefix = $lang->getTagPrefix();
			$channel_file = JURI::root().'templates/'.$this->template.'/php/channel_'.$lang_prefix.'.php';
			ob_start();
			?>
			window.fbAsyncInit = function() {
				FB.init({
					appId      : '<?php echo $appid ?>',
					channelUrl : '<?php echo $channel_file ?>',
					status     : true,
					cookie     : true,
					xfbml      : true
				});
			};
			(function(d, debug){
				var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
				if (d.getElementById(id)) {return;}
				js = d.createElement('script'); js.id = id; js.async = true;
				js.src = "//connect.facebook.net/<?php echo $lang_tag ?>/all" + (debug ? "/debug" : "") + ".js";
				ref.parentNode.insertBefore(js, ref);
			}(document, /*debug*/ false));
			<?php
			$script .= ob_get_clean();
		}
		if ($app->getCfg('analytics_enabled', 0)) {
			$analytics_account = $app->getCfg('analytics_account', '');
			$analytics_domain = $app->getCfg('analytics_domain', '');
			ob_start();
			?>
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', '<?php echo $analytics_account ?>']);
			<?php
			if ($analytics_domain) {
			?>
			_gaq.push(['_setDomainName', '<?php echo $analytics_domain ?>']);
			<?php
			}
			?>
			_gaq.push(['_trackPageview']);
			(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();
			<?php
			$script .= ob_get_clean();
		}
		if ($script) {
			$doc->addScriptDeclaration($script);
		}

		?>
	</body>
</html>