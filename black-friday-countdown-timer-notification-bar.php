<?php
/**
 * Plugin Name:       Black Friday Countdown Timer Notification Bar
 * Plugin URI:        https://github.com/Yiannistaos/black-friday-countdown-timer-notification-bar-wordpress-plugin
 * Description:       A tiny WordPress plugin which displays a fixed notification bar at the top of the page with a countdown timer and a message about a special offer e.g. Black Friday.
 * Version:           1.1.1
 * Author:            Yiannis Christodoulou (web357), Thodoris Gkitsos (theogk)
 * Author URI:        https://www.web357.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       black-friday-countdown-timer-notification-bar
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'BF_COUNTDOWN_VERSION', '1.1.1' );
define( 'BF_COUNTDOWN_BASE_FILE', 'black-friday-countdown-timer-notification-bar/black-friday-countdown-timer-notification-bar.php' );

// Register CSS and JS files
add_action('init', 'w357_register_script');
function w357_register_script() {
    if( empty( get_option('bf_countdown_enabled') ) ) return;

    wp_register_script( 'black-friday-countdown-timer-notification-bar', plugins_url('/js/script.js', __FILE__), false, BF_COUNTDOWN_VERSION, true );
    wp_register_style( 'black-friday-countdown-timer-notification-bar', plugins_url('/css/style.css', __FILE__), false, BF_COUNTDOWN_VERSION, 'all');

}

add_action('wp_enqueue_scripts', 'w357_enqueue_style');
function w357_enqueue_style(){
    if( empty( get_option('bf_countdown_enabled') ) ) return;

    wp_enqueue_script('black-friday-countdown-timer-notification-bar');
    wp_enqueue_style( 'black-friday-countdown-timer-notification-bar' );
    wp_localize_script( 'black-friday-countdown-timer-notification-bar', 'bf_pass_args',
        [
            'bf_diff_time' => bf_return_time_distance_in_seconds(),
            'bf_end_text'  => bf_get_end_text()
        ]
    );
}

add_action('admin_enqueue_scripts', 'w357_admin_enqueue_scripts');
function w357_admin_enqueue_scripts( $hook ) {

    if ( 'settings_page_bf-countdown-settings' !== $hook ) return;

    // datetime picker
    wp_enqueue_script( 'jquery-ui-datetimepicker', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js', [ 'jquery' ], BF_COUNTDOWN_VERSION, true );

    wp_enqueue_style( 'jquery-ui-datetimepicker', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css', [], BF_COUNTDOWN_VERSION );

    // custom script for backend
    wp_enqueue_script( 'black-friday-countdown-timer-notification-bar-admin', plugins_url('/js/script.admin.js', __FILE__), [ 'jquery', 'jquery-ui-datetimepicker' ], BF_COUNTDOWN_VERSION, true );
}

function bf_return_time_distance_in_seconds() {
    return strtotime( bf_get_time_from_settings() ) - current_time('timestamp');
}

function bf_get_time_from_settings() {
    //just get black friday 2020 for now

    $end_datetime = !empty( get_option( 'bf_end_datetime' ) ) ? esc_attr( get_option( 'bf_end_datetime' ) ) : '2022-12-31 23:59:59';

    return $end_datetime; // '2020-11-27 23:59:59'
}

function bf_get_end_text() {
    return !empty( get_option( 'bf_countdown_end_text' ) ) ? wp_kses_post( get_option( 'bf_countdown_end_text' ) ) : 'The Black Friday 2022 has passed. Thank you! :)';
}

// The HTML Code
$hook = function_exists( 'wp_body_open' ) || has_action('wp_body_open') ? 'wp_body_open' : 'wp_head';
add_action( 'wp_head', 'w357_black_friday_countdown_notification_bar_html' );
function w357_black_friday_countdown_notification_bar_html() {
    if( empty( get_option('bf_countdown_enabled') ) ) return;

    $text_top    = !empty( get_option( 'bf_countdown_main_text' ) ) ? wp_kses_post( get_option( 'bf_countdown_main_text' ) ) : '<span class="blackfriday-txt">BLACK FRIDAY</span> Special Offer! 50% OFF!';
    $text_bottom = !empty( get_option( 'bf_countdown_coupon_text' ) ) ? wp_kses_post( get_option( 'bf_countdown_coupon_text' ) ) : 'Use the discount coupon code:';
    $coupon_code = !empty( get_option( 'bf_countdown_coupon_code' ) ) ? esc_attr( get_option( 'bf_countdown_coupon_code' ) ) : '';
    $button_text = !empty( get_option( 'bf_countdown_button_text' ) ) ? esc_attr( get_option( 'bf_countdown_button_text' ) ) : 'Buy Now';
    $button_url  = !empty( get_option( 'bf_countdown_button_url' ) ) ? esc_url( get_option( 'bf_countdown_button_url' ) ) : 'https://www.your-domain.com/pricing';
    $maybe_hide  = empty( get_option( 'bf_countdown_coupon_code' ) ) ? 'blackfriday-hide-coupon-line' : '';

    $html = <<<HTML
    <div id="blackfriday-html-toolbar" class="blackfriday-html-toolbar">
        <div class="blackfriday-html-toolbar-inner">
            <div id="blackfriday-html-toolbar-content" class="blackfriday-html-toolbar-content">

                <div class="blackfriday-countdown-timer-outer"> 
                    <span id="blackfriday-countdown-timer"></span> 
                </div>
                
                <div class="blackfriday-text-outer"> 
                    <div class="blackfriday-txt-top">
                        {$text_top}
                    </div>
                    <div class="blackfriday-txt-bottom {$maybe_hide}">
                        {$text_bottom} <span class="blackfriday-coupon-code">{$coupon_code}</span>
                    </div>
                </div>
                
                <div class="blackfriday-btn-outer"> 
                    <a href="{$button_url}" class="blackfriday-buynow-btn">{$button_text}</a>
                </div>
            </div>
        </div>
    </div>
HTML;
    echo $html;
}


/**
 * Settings Section
 */

/**
 * Add settings page.
 */
add_action( 'admin_menu', 'bf_add_settings_page' );
function bf_add_settings_page()
{
    // This page will be under "Settings"
    add_options_page(
        'Black Friday Countdown Settings',
        'Black Friday Countdown',
        'manage_options',
        'bf-countdown-settings',
        'bf_create_settings_page'
    );
}


/**
 * Render settings page.
 */
function bf_create_settings_page() {

    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    </div>
    <div class="wrap bf-countdown-settings">
        <form method="post" action="options.php">
            <?php
            // This prints out all hidden setting fields
            settings_fields( 'bf_countdown_option_group' );
            do_settings_sections( 'bf-countdown-settings' );
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * Register and add settings.
 */
add_action( 'admin_init', 'bf_countdown_settings' );
function bf_countdown_settings()
{
    // First add_settings_section, before add_settings_field
    add_settings_section (
        'bf_countdown_settings_section', // ID
        'Countdown Timer Settings', // Title
        'bf_print_settings_info', // Callback
        'bf-countdown-settings' // Page
    );

    //add settings fields

    add_settings_field (
        'bf_countdown_enabled', // ID
        'Enable Countdown Top Bar:', // Title
        'bf_countdown_enabled_callback', // Callback
        'bf-countdown-settings', // Page
        'bf_countdown_settings_section' // Section
    );

    add_settings_field (
        'bf_countdown_main_text', // ID
        'Main headline:', // Title
        'bf_countdown_main_text_callback', // Callback
        'bf-countdown-settings', // Page
        'bf_countdown_settings_section' // Section
    );

    add_settings_field (
        'bf_countdown_coupon_text', // ID
        'Before coupon text:', // Title
        'bf_countdown_coupon_text_callback', // Callback
        'bf-countdown-settings', // Page
        'bf_countdown_settings_section' // Section
    );

    add_settings_field (
        'bf_countdown_coupon_code', // ID
        'Coupon code:', // Title
        'bf_countdown_coupon_code_callback', // Callback
        'bf-countdown-settings', // Page
        'bf_countdown_settings_section' // Section
    );

    add_settings_field (
        'bf_end_datetime', // ID
        'End DateTime:', // Title
        'bf_end_datetime_callback', // Callback
        'bf-countdown-settings', // Page
        'bf_countdown_settings_section' // Section
    );

    add_settings_field (
        'bf_countdown_button_text', // ID
        'Button text:', // Title
        'bf_countdown_button_text_callback', // Callback
        'bf-countdown-settings', // Page
        'bf_countdown_settings_section' // Section
    );

    add_settings_field (
        'bf_countdown_button_url', // ID
        'Button URL:', // Title
        'bf_countdown_button_url_callback', // Callback
        'bf-countdown-settings', // Page
        'bf_countdown_settings_section' // Section
    );

    add_settings_field (
        'bf_countdown_end_text', // ID
        'Text if countdown ends:', // Title
        'bf_countdown_end_text_callback', // Callback
        'bf-countdown-settings', // Page
        'bf_countdown_settings_section' // Section
    );

    register_setting (
        'bf_countdown_option_group', // Option group
        'bf_countdown_enabled', // Option name
        [ 'sanitize_callback' => 'bf_sanitize_input' ] // Sanitize
    );

    register_setting (
        'bf_countdown_option_group', // Option group
        'bf_countdown_main_text', // Option name
        [ 'sanitize_callback' => 'bf_sanitize_input' ] // Sanitize
    );

    register_setting (
        'bf_countdown_option_group', // Option group
        'bf_countdown_coupon_text', // Option name
        [ 'sanitize_callback' => 'bf_sanitize_input' ] // Sanitize
    );

    register_setting (
        'bf_countdown_option_group', // Option group
        'bf_countdown_coupon_code', // Option name
        [ 'sanitize_callback' => 'bf_sanitize_input' ] // Sanitize
    );

    register_setting (
        'bf_countdown_option_group', // Option group
        'bf_end_datetime', // Option name
        [ 'sanitize_callback' => 'bf_sanitize_input' ] // Sanitize
    );

    register_setting (
        'bf_countdown_option_group', // Option group
        'bf_countdown_button_text', // Option name
        [ 'sanitize_callback' => 'bf_sanitize_input' ] // Sanitize
    );

    register_setting (
        'bf_countdown_option_group', // Option group
        'bf_countdown_button_url', // Option name
        [ 'sanitize_callback' => 'bf_sanitize_url' ] // Sanitize
    );

    register_setting (
        'bf_countdown_option_group', // Option group
        'bf_countdown_end_text', // Option name
        [ 'sanitize_callback' => 'bf_sanitize_input' ] // Sanitize
    );
}

/**
 * Sanitize each setting field as needed.
 */
 function bf_sanitize_input( $input ) {
    $new_input = '';

    if ( isset( $input ) ) {
        $new_input =  wp_kses_post( $input );
    }

    return $new_input;
}

function bf_sanitize_url( $input ) {
    $new_input = '';

    if ( isset( $input ) ) {
        $new_input =  esc_url_raw( $input );
    }

    return $new_input;
}

/**
 * Settings section info.
 */
function bf_print_settings_info() {
    print 'Choose your settings.';
}


/**
 * Settings fields callbacks.
 */
function bf_countdown_end_text_callback() {
    printf(
        '<input type="text" id="bf_countdown_end_text" name="bf_countdown_end_text" style="width:100%%;" value="%s" />',
        wp_kses_post( get_option( 'bf_countdown_end_text', 'The Black Friday 2020 has passed. Thank you! :)' ) )
    );
}

function bf_countdown_main_text_callback() {
    printf(
        '<input type="text" id="bf_countdown_main_text" name="bf_countdown_main_text" style="width:100%%;" value="%s" />',
        wp_kses_post( get_option( 'bf_countdown_main_text', '<span class=\'blackfriday-txt\'>BLACK FRIDAY</span> Special Offer! 50% OFF!' ) )
    );
}

function bf_countdown_coupon_text_callback() {
    printf(
        '<input type="text" id="bf_countdown_coupon_text" name="bf_countdown_coupon_text" style="width:100%%;" value="%s" />',
        wp_kses_post( get_option( 'bf_countdown_coupon_text', 'Use the discount coupon code:' ) )
    );
}

function bf_countdown_coupon_code_callback() {
    printf(
        '<input type="text" id="bf_countdown_coupon_code" name="bf_countdown_coupon_code" value="%s" />',
        esc_attr( get_option( 'bf_countdown_coupon_code', 'blackfriday2k20' ) )
    );
}

function bf_end_datetime_callback() {
    printf(
        '<input type="text" id="bf_end_datetime" name="bf_end_datetime" value="%s" />',
        esc_attr( get_option( 'bf_end_datetime', '2020-11-27 23:59:59' ) )
    );
}

function bf_countdown_button_text_callback() {
    printf(
        '<input type="text" id="bf_countdown_button_text" name="bf_countdown_button_text" class="regular-text" value="%s" />',
        wp_kses_post( get_option( 'bf_countdown_button_text', 'Buy Now' ) )
    );
}

function bf_countdown_button_url_callback() {
    printf(
        '<input type="text" id="bf_countdown_button_url" name="bf_countdown_button_url" style="width:100%%;" value="%s" />',
        esc_url(get_option('bf_countdown_button_url', 'https://www.your-domain.com/pricing'))
    );
}

function bf_countdown_enabled_callback() {
    $checked = get_option('bf_countdown_enabled') == 1 ? 'checked' : '';
    echo '<input type="checkbox" id="bf_countdown_enabled" name="bf_countdown_enabled" value="1" ' . $checked . ' />';
}


/**
 * Add Settings link in plugin page.
 *
 * @param   array $actions The actions array.
 * @param   string $plugin_file Path to the plugin file relative to the plugins directory.
 * @return  array The actions array.
 * @since   1.0.0
 */
add_filter( 'plugin_action_links', 'bf_countdown_plugin_action_links', 10, 2 );
function bf_countdown_plugin_action_links( $actions, $plugin_file ) {

	$this_plugin = BF_COUNTDOWN_BASE_FILE;

	if ( $plugin_file == $this_plugin ) {

		$settings_link = '<a href="' . admin_url( 'options-general.php?page=bf-countdown-settings' ) . '">Settings</a>';
		array_unshift( $actions, $settings_link );
	}

	return $actions;
}