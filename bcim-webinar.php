<?php
/**
 * Plugin Name:       BCIM Webinar System
 * Plugin URI:        http://e-learning.bcintermedia.pl/
 * Description:       System masowych wykładów w czasie rzeczywistym
 * Version:           0.5.1
 * Author:            Krzysztof Rawski
 * License:           GPL v2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       bcim-webinar
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
    die();
}

define('WEBINAR_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('WEBINAR_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once WEBINAR_PLUGIN_PATH . 'vendor/autoload.php';
require_once WEBINAR_PLUGIN_PATH . '/includes/activation.php';

use BCIM\Init;
$init = new Init();
$init->register_styles();

use BCIM\RestApi;
$rest_api = new RestApi();
$rest_api->registerCustomEndpoints();

use BCIM\Webinars;
$webinars = new Webinars();
$webinars->registerShortcodes();
$webinars->register_cpt();
$webinars->registerCptTemplate();
$webinars->register_custom_fields();

add_action('init', 'startSession');
add_action('wp_logout', 'endSession');
add_action('wp_login', 'endSession');

register_activation_hook(__FILE__, 'activate');

function activate()
{
    webinar_add_bcim_questions_table();
}
