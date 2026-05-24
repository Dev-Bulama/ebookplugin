<?php
/**
 * Plugin Name: SkillScore Ebook Commerce
 * Plugin URI: https://skillscoreit.com
 * Description: A comprehensive ebook commerce solution with audio previews, multiple payment gateways, and secure downloads.
 * Version: 1.0.0
 * Author: SkillScore IT Solutions and Training
 * Author URI: https://skillscoreit.com
 * Developer: Tijani Bulama
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: skillscore-ebook
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.0
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * TEMPORARY DEBUG — captures fatal errors during activation.
 * Remove this entire block once the error is identified and fixed.
 */
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR], true)) {
        $msg = date('[Y-m-d H:i:s]') . ' SKILLSCORE FATAL: ' . $error['message']
             . ' in ' . $error['file'] . ' on line ' . $error['line'] . PHP_EOL;

        // 1. PHP error log (always writable — check /var/log/php*.log or Apache/Nginx error log)
        error_log(trim($msg));

        // 2. System temp dir (e.g. /tmp/skillscore-debug.log)
        file_put_contents(sys_get_temp_dir() . '/skillscore-debug.log', $msg, FILE_APPEND);

        // 3. Plugin directory (may fail if not writable — no @ so the error appears in PHP log)
        file_put_contents(__DIR__ . '/activation-debug.log', $msg, FILE_APPEND);

        // 4. wp-content directory as a fallback
        if (defined('WP_CONTENT_DIR')) {
            file_put_contents(WP_CONTENT_DIR . '/skillscore-debug.log', $msg, FILE_APPEND);
        }
    }
});

/**
 * Current plugin version.
 */
define('SKILLSCORE_EBOOK_VERSION', '1.0.0');
define('SKILLSCORE_EBOOK_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SKILLSCORE_EBOOK_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SKILLSCORE_EBOOK_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_skillscore_ebook() {
    require_once SKILLSCORE_EBOOK_PLUGIN_DIR . 'includes/class-activator.php';
    SkillScore_Ebook_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_skillscore_ebook() {
    require_once SKILLSCORE_EBOOK_PLUGIN_DIR . 'includes/class-deactivator.php';
    SkillScore_Ebook_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_skillscore_ebook');
register_deactivation_hook(__FILE__, 'deactivate_skillscore_ebook');

/**
 * The core plugin class.
 */
require SKILLSCORE_EBOOK_PLUGIN_DIR . 'includes/class-ebook-core.php';

/**
 * Begins execution of the plugin.
 */
function run_skillscore_ebook() {
    $plugin = new SkillScore_Ebook_Core();
    $plugin->run();
}

run_skillscore_ebook();
