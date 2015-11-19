<?php
/**
 * @package	LongCMS.Site
 * @subpackage	com_blank
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<deals>
	<?php
	if (!empty($this->items)) {
		$dealItemid = JMenu::getItemid('com_deals', 'deal');
		$a = 1;
		$site_url = JURI::root(false, false, true);
		foreach($this->items as $deal) {
			$price = $deal->getPrice();
			$old_price = $deal->getOldPrice();
			$title = $deal->getTitle();
			$id = $deal->getId();
			$link = $site_url.JRoute::_('index.php?option=com_deals&view=deal&id='.$id.'&Itemid='.$dealItemid);
			$sold = $deal->getSold();

			$jdate = JFactory::getDate($deal->publish_down);
			$jdate->setTimeZone('Asia/Tbilisi');
			$finish = $jdate->format(null, true);



			$text = $deal->getText();
			$text = JFilterOutput::encode($text);
			$image = $site_url.'/'.$deal->getImage(1, 'image10');
			$percent = $deal->getDiscount();
			?>
			<sale id="<?php echo $id ?>">
				<link><?php echo $link ?></link>
				<title><?php echo $title ?></title>
				<image><?php echo $image ?></image>
				<text><?php echo $text ?></text>
				<time><?php echo $finish ?></time>
				<price><?php echo $price ?></price>
				<percent><?php echo $percent ?></percent>
			</sale>
			<?php
		}
	}
	?>
</deals>
