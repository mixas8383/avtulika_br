<?php
/**
 * @package	LongCMS.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

// Create shortcuts to some parameters.
$params		= $this->item->params;
$images = json_decode($this->item->images);
$urls = json_decode($this->item->urls);
$canEdit	= $this->item->params->get('access-edit');
$user		= JFactory::getUser();

$article_title = $this->escape($this->item->title);
$article_link = $this->item->readmore_link;

$category_title = $this->escape($this->item->category_title);
$parent_title = $this->escape($this->item->parent_title);

?>
<div class="comp_block<?php echo $this->pageclass_sfx?>">
	<?php
	if ($this->params->get('show_page_heading')) {
		?>
		<div class="page_title">
        	<h1>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
            </h1>
		</div>
		<?php
		if ($params->get('show_title')) {
			?>
			<div class="article_title">
				<?php echo $this->escape($this->item->title); ?>
			</div>
			<?php
		}
	}
	else
	{
		if ($params->get('show_title')) {
			?>
			<div class="page_title">
				<?php echo $this->escape($this->item->title); ?>
			</div>
			<?php
		}
	}

	if (!empty($this->item->pagination) AND $this->item->pagination && !$this->item->paginationposition && $this->item->paginationrelative) {
		echo $this->item->pagination;
	}
	?>

	<div class="content_body">

		<?php
		if (!$params->get('show_intro')) {
			?>
			<div class="content_text">
				<?php echo $this->item->event->afterDisplayTitle;?>
			</div>
			<?php
		}
		?>

		<?php echo $this->item->event->beforeDisplayContent; ?>

		<?php
		$useDefList = (($params->get('show_author')) or ($params->get('show_category')) or ($params->get('show_parent_category'))
			or ($params->get('show_create_date')) or ($params->get('show_modify_date')) or ($params->get('show_publish_date'))
			or ($params->get('show_hits')));
		?>

		<?php
		if ($params->get('show_parent_category') && $this->item->parent_slug != '1:root') {
			?>
			<dd class="parent-category-name">
				<?php
				$title = $this->escape($this->item->parent_title);
				$url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->parent_slug)).'">'.$title.'</a>';
				if ($params->get('link_parent_category') and $this->item->parent_slug) {
					echo JText::sprintf('COM_CONTENT_PARENT', $url);
				} else {
					echo JText::sprintf('COM_CONTENT_PARENT', $title);
				}
				?>
			</dd>
			<?php
		}
		?>

		<?php
		if ($params->get('show_category')) {
			?>
			<dd class="category-name">
				<?php
				$title = $this->escape($this->item->category_title);
				$url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catslug)).'">'.$title.'</a>';
				if ($params->get('link_category') and $this->item->catslug) {
					echo JText::sprintf('COM_CONTENT_CATEGORY', $url);
				} else {
					echo JText::sprintf('COM_CONTENT_CATEGORY', $title);
				}
				?>
			</dd>
			<?php
		}
		?>

		<?php
		if ($params->get('show_create_date')) {
			?>
			<dd class="create">
				<?php echo JText::sprintf('COM_CONTENT_CREATED_DATE_ON', JHtml::_('date', $this->item->created, JText::_('DATE_FORMAT_LC2'))); ?>
			</dd>
			<?php
		}
		?>

		<?php
		if ($params->get('show_modify_date')) {
			?>
			<dd class="modified">
				<?php echo JText::sprintf('COM_CONTENT_LAST_UPDATED', JHtml::_('date', $this->item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
			</dd>
			<?php
		}
		?>


		<?php
		if ($params->get('show_publish_date')) {
			?>
			<div class="create_date">
				<?php echo JText::sprintf(JHtml::_('date', $this->item->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
			</div>
			<?php
		}
		?>


		<?php
		if ($params->get('show_author') && !empty($this->item->author )) {
			?>
			<dd class="createdby">
				<?php
				$author = $this->item->created_by_alias ? $this->item->created_by_alias : $this->item->author;
				if (!empty($this->item->contactid) && $params->get('link_author') == true) {
					$needle = 'index.php?option=com_contact&view=contact&id=' . $this->item->contactid;
					$menu = JFactory::getApplication()->getMenu();
					$item = $menu->getItems('link', $needle, true);
					$cntlink = !empty($item) ? $needle . '&Itemid=' . $item->id : $needle;
					echo JText::sprintf('COM_CONTENT_WRITTEN_BY', JHtml::_('link', JRoute::_($cntlink), $author));
				} else {
					echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $author);
				}
				?>
			</dd>
			<?php
		}
		?>



		<?php
		if ($params->get('show_hits')) {
			?>
			<dd class="hits">
				<?php echo JText::sprintf('COM_CONTENT_ARTICLE_HITS', $this->item->hits); ?>
			</dd>
			<?php
		}
		?>

		<?php
		if (isset ($this->item->toc)) {
			echo $this->item->toc;
		}
		?>

		<?php
		if (isset($urls) AND ((!empty($urls->urls_position) AND ($urls->urls_position=='0')) OR  ($params->get('urls_position')=='0' AND empty($urls->urls_position) ))
		OR (empty($urls->urls_position) AND (!$params->get('urls_position')))) {
			echo $this->loadTemplate('links');
		}
		?>

		<?php
		if ($params->get('access-view'))
		{
			?>
			<?php
			if (isset($images->image_fulltext) and !empty($images->image_fulltext)) {
				$imgfloat = (empty($images->float_fulltext)) ? $params->get('float_fulltext') : $images->float_fulltext;
				?>
				<div class="article_image">
					<img src="<?php echo htmlspecialchars($images->image_fulltext); ?>" alt="<?php echo htmlspecialchars($images->image_fulltext_alt); ?>" align="left"/>
				</div>
				<?php
			}
			?>
			<?php
			if (!empty($this->item->pagination) AND $this->item->pagination AND !$this->item->paginationposition AND !$this->item->paginationrelative) {
				echo $this->item->pagination;
			}
			?>
			<div class="article_text">
				<?php echo $this->item->text; ?>
			</div>
			<?php
			$article_url = htmlspecialchars($article_link, ENT_QUOTES);
			//$article_url = urlencode($article_url);
			$article_url = JURI::root(false, false, true).$article_url;


			if ($canEdit || $params->get('show_print_icon') || $params->get('show_email_icon') || $this->params->get('socials')) {
				?>
				<div class="article_socials">
					<?php
					if (!$this->print) {
						if ($this->params->get('socials')) {

							?>
							<div class="socials">
								<span>
									<a target="_blank" rel="nofollow" href="http://www.facebook.com/sharer.php?u=<?php echo $article_url; ?>&amp;title=<?php echo $article_title; ?>" title="Facebook"  onclick="window.open(this.href,'FaceBook','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=700,height=480,directories=no,location=no'); return false;">
										<img src="<?php echo JURI::root(); ?>templates/longcms/images/icons/facebook.png" alt="FaceBook" />
									</a>
								</span>
								<span>
									<a target="_blank" rel="nofollow" href="http://twitter.com/home?status=<?php echo $article_url; ?>&amp;title=<?php echo $article_title; ?>" title="Twitter"  onclick="window.open(this.href,'Twitter','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=700,height=480,directories=no,location=no'); return false;">
										<img src="<?php echo JURI::root(); ?>templates/longcms/images/icons/twitter.png" alt="Twitter"/>
									</a>
								</span>
								<?php
								$this->document->addScript('https://apis.google.com/js/plusone.js');
								?>
								<g:plusone size="medium" href="<?php echo $article_url ?>"></g:plusone>
								<?php
								$script = 'window.___gcfg = {lang: "en-GB"};
											  (function() {
											    var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
											    po.src = "https://apis.google.com/js/plusone.js";
											    var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
											  })();';
								$this->document->addScriptDeclaration( $script );
								?>

								<span>
									<fb:like href="<?php echo $article_url ?>" ref="content_<?php echo $this->item->id ?>" send="false" layout="button_count" width="450" show_faces="false"></fb:like>
								</span>
							</div>
							<?php
						}
						?>
						<div class="printemail_icon">
							<?php
                            if ($params->get('show_email_icon')) {
                                ?>
                                <span class="email-icon">
                                    <?php echo JHtml::_('icon.email',  $this->item, $params); ?>
                                </span>
                                <?php
                            }
                            ?>
                            <?php
                            if ($params->get('show_print_icon')) {
                                ?>
                                <span class="print-icon">
                                    <?php echo JHtml::_('icon.print_popup',  $this->item, $params); ?>
                                </span>
                                <?php
                            }
                            ?>
                        </div>
						<div class="cls"></div>
						<?php
					} else {
						?>
						<a onclick="window.print();return false;" href="javascript:void(0);" title="<?php echo JText::_('JGLOBAL_PRINT') ?>">
							<img alt="<?php echo JText::_('JGLOBAL_PRINT') ?>" src="<?php echo JURI::root()?>/media/system/images/printButton.png" />
						</a>
						<?php
					}
					?>
				</div>
				<?php
			}

			if (!$this->print && $params->get('fb_comments'))
			{
				?>
				<div class="article_comments">
					<fb:comments href="<?php echo $article_url ?>" width="940" num_posts="10" colorscheme="light" order_by="social"></fb:comments>
				</div>
				<?php
			}
			?>
			<?php
			if (!empty($this->item->pagination) AND $this->item->pagination AND $this->item->paginationposition AND !$this->item->paginationrelative) {
				echo $this->item->pagination;
			}
			?>

			<?php
			if (isset($urls) AND ((!empty($urls->urls_position)  AND ($urls->urls_position=='1')) OR ( $params->get('urls_position')=='1') )) {
				echo $this->loadTemplate('links');
			}

			//optional teaser intro text for guests
		} elseif ($params->get('show_noauth') == true and  $user->get('guest')) {
			echo $this->item->introtext;
			//Optional link to let them register to see the whole article.
			if ($params->get('show_readmore') && $this->item->fulltext != null) {
				$link1 = JRoute::_('index.php?option=com_users&view=login');
				$link = new JURI($link1);
				?>
				<p class="readmore">
					<a href="<?php echo $link; ?>">
						<?php
						$attribs = json_decode($this->item->attribs);

						if ($attribs->alternative_readmore == null) {
							echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
						} elseif ($readmore = $this->item->alternative_readmore) {
							echo $readmore;
							if ($params->get('show_readmore_title', 0) != 0) {
								echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
							}
						} elseif ($params->get('show_readmore_title', 0) == 0) {
							echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');
						} else {
							echo JText::_('COM_CONTENT_READ_MORE');
							echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
						}
						?>
					</a>
				</p>
				<?php
			}
		}
		?>
		<?php
		if (!empty($this->item->pagination) AND $this->item->pagination AND $this->item->paginationposition AND $this->item->paginationrelative) {
			echo $this->item->pagination;
		}
		?>

		<?php echo $this->item->event->afterDisplayContent; ?>
	</div>
</div>
