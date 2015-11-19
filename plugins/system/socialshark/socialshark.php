<?php
/**
 * @package     	LongCMS.Plugin
 * @subpackage  System.Socialshark
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * LongCMS Socialshark plugin
 *
 * @package     	LongCMS.Plugin
 * @subpackage  System.Socialshark
 * @since       1.5
 */
class plgSystemSocialshark extends JPlugin
{

	/**
	 * Constructor.
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 *
	 * @since 1.5
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

	}

	/**
	 * Add the CSS for debug. We can't do this in the constructor because
	 * stuff breaks.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	public function onAfterRender()
	{
        $app = JFactory::getApplication();

        // Check that we are in the site application.
        if ($app->isAdmin())
        {
            return;
        }

        $document = JFactory::getDocument();
        // Only render for HTML output
        if ('html' !== $document->getType())
        {
            return;
        }

        $body = JResponse::getBody();

        $fb_pixel_id = $this->params->get('fb_pixel_id');




        ob_start();
        ?>
        <!-- Facebook Pixel Code -->
        <script>
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
        n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
        document,'script','//connect.facebook.net/en_US/fbevents.js');

        fbq('init', '1486514078312785');
        fbq('track', 'PageView');



        <?php

/*var_dump(htmlspecialchars($body));
die;*/

        $option = JRequest::getVar('option');
        $view = JRequest::getVar('view');
        $layout = JRequest::getVar('layout');
        $session = JFactory::getSession();

        $sess_pixel = $session->get('pixel_data', array());
        $session->set('pixel_data', array());

        if ($option == 'com_deals') {
            switch($view) {
                case 'deals':
                    if (!empty($_POST['searchword'])) {
                        ?>
                        fbq('track', 'Search');
                        <?php
                    }
                    if (!empty($sess_pixel)) {
                        $amt = $sess_pixel['amt'];
                        ?>
                        fbq('track', 'Purchase', {value: '0.00', currency: 'USD'});

                        (function() {
                        var _fbq = window._fbq || (window._fbq = []);
                        if (!_fbq.loaded) {
                        var fbds = document.createElement('script');
                        fbds.async = true;
                        fbds.src = '//connect.facebook.net/en_US/fbds.js';
                        var s = document.getElementsByTagName('script')[0];
                        s.parentNode.insertBefore(fbds, s);
                        _fbq.loaded = true;
                        }
                        })();
                        window._fbq = window._fbq || [];
                        window._fbq.push(['track', '6031186015977', {'value':'0.00','currency':'USD'}]);

                        <?php
                    }

                    break;


                case 'deal':
                    ?>
                    fbq('track', 'ViewContent');
                    <?php
                    break;

                case 'buy':
                    ?>
                    fbq('track', 'InitiateCheckout');
                    <?php
                    break;


            }
        } else if ($option == 'com_users') {
            switch($view) {
                case 'registration':
                    if ($layout == 'complete') {
                        ?>
                        fbq('track', 'CompleteRegistration');

                        (function() {
                        var _fbq = window._fbq || (window._fbq = []);
                        if (!_fbq.loaded) {
                        var fbds = document.createElement('script');
                        fbds.async = true;
                        fbds.src = '//connect.facebook.net/en_US/fbds.js';
                        var s = document.getElementsByTagName('script')[0];
                        s.parentNode.insertBefore(fbds, s);
                        _fbq.loaded = true;
                        }
                        })();
                        window._fbq = window._fbq || [];
                        window._fbq.push(['track', '6031310944177', {'value':'0.00','currency':'USD'}]);

                        <?php
                    }
                    break;
            }
        }


        ?>

        </script>
        <!-- End Facebook Pixel Code -->
        <?php
        $html = ob_get_clean();




        $body = str_replace('</head>', $html.'</head>', $body);

        JResponse::setBody($body);


	}

}
