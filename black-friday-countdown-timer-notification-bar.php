<?php
/**
 * Plugin Name:       Black Friday Countdown Timer Notification Bar
 * Plugin URI:        https://www.web357.com/
 * Description:       A tiny WordPress plugin which displays a fixed notification bar at the top of the page with a countdown timer and a message about a special offer e.g. Black Friday.
 * Version:           1.0.0
 * Author:            Yiannis Christodoulou
 * Author URI:        https://www.web357.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       black-friday-countdown-timer-notification-bar
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Register CSS and JS files
add_action('init', 'w357_register_script');
function w357_register_script() {
    wp_register_script( 'black-friday-countdown-timer-notification-bar', plugins_url('/js/script.js', __FILE__), array(), '1.0.0' );
    wp_register_style( 'black-friday-countdown-timer-notification-bar', plugins_url('/css/style.css', __FILE__), false, '1.0.0', 'all');
}

add_action('wp_enqueue_scripts', 'w357_enqueue_style');
function w357_enqueue_style(){
   wp_enqueue_script('black-friday-countdown-timer-notification-bar');
   wp_enqueue_style( 'black-friday-countdown-timer-notification-bar' );
}

// The HTML Code
add_action('wp_head', 'w357_black_friday_countdown_notification_bar_html');
function w357_black_friday_countdown_notification_bar_html() {
    $html = <<<HTML
    <div id="blackfriday-html-toolbar" class="blackfriday-html-toolbar blackfriday-html-toolbar-top">
        <div class="blackfriday-html-toolbar-inner">
            <div id="blackfriday-html-toolbar-content" class="blackfriday-html-toolbar-content">

                <div class="blackfriday-countdown-timer-outer"> 
                    <span id="blackfriday-countdown-timer"></span> 
                </div>
                
                <div class="blackfriday-text-outer"> 
                    <div class="blackfriday-txt-top">
                        <span class="blackfriday-txt">BLACK FRIDAY</span> Special Offer! 50% OFF! 
                    </div>
                    <div class="blackfriday-txt-bottom">
                        Use the discount coupon code: <span class="blackfriday-coupon-code">blackfriday2k20</span>
                    </div>
                </div>
                
                <div class="blackfriday-btn-outer"> 
                    <a href="https://www.web357.com/joomla-pricing?utm_source=Web357BlackFriday2020&amp;utm_medium=Web357BlackFriday2020-BuyNowLink&amp;utm_content=Web357BlackFriday2020-BuyNowLink&amp;utm_campaign=Web357BlackFriday2020BuyNowLink" class="blackfriday-buynow-btn">Buy Now</a>
                </div>
            </div>
        </div>
    </div>
HTML;
    echo $html;
}