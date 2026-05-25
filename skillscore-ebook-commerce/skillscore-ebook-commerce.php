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

// ─── TEMPORARY DEBUG BLOCK — remove once error is found ───────────────────────
// Force PHP to log all errors regardless of server settings.
ini_set('log_errors', 1);
ini_set('display_errors', 0); // keep off so it doesn't break AJAX
error_reporting(E_ALL);

// Write a timestamped step to every writable log location.
function _sse_log($step) {
    $msg = date('[Y-m-d H:i:s]') . ' SSE-STEP ' . $step . PHP_EOL;
    error_log('SKILLSCORE: step=' . $step); // → PHP / server error log
    @file_put_contents(__DIR__ . '/activation-debug.log',   $msg, FILE_APPEND);
    @file_put_contents(sys_get_temp_dir() . '/skillscore-debug.log', $msg, FILE_APPEND);
    if (defined('WP_CONTENT_DIR')) {
        @file_put_contents(WP_CONTENT_DIR . '/skillscore-debug.log', $msg, FILE_APPEND);
    }
}

// Capture any fatal error at shutdown.
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR], true)) {
        $msg = date('[Y-m-d H:i:s]') . ' SSE-FATAL: ' . $error['message']
             . ' in ' . $error['file'] . ' on line ' . $error['line'] . PHP_EOL;
        error_log('SKILLSCORE FATAL: ' . $error['message'] . ' in ' . $error['file'] . ':' . $error['line']);
        @file_put_contents(__DIR__ . '/activation-debug.log',   $msg, FILE_APPEND);
        @file_put_contents(sys_get_temp_dir() . '/skillscore-debug.log', $msg, FILE_APPEND);
        if (defined('WP_CONTENT_DIR')) {
            @file_put_contents(WP_CONTENT_DIR . '/skillscore-debug.log', $msg, FILE_APPEND);
        }
    }
});

_sse_log('1-plugin-file-loaded  PHP=' . PHP_VERSION);
// ─── END TEMPORARY DEBUG BLOCK ────────────────────────────────────────────────

/**
 * Current plugin version.
 */
define('SKILLSCORE_EBOOK_VERSION', '1.0.0');
define('SKILLSCORE_EBOOK_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SKILLSCORE_EBOOK_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SKILLSCORE_EBOOK_PLUGIN_BASENAME', plugin_basename(__FILE__));

_sse_log('2-constants-defined');

/**
 * The code that runs during plugin activation.
 */
function activate_skillscore_ebook() {
    _sse_log('5-activation-hook-start');
    require_once SKILLSCORE_EBOOK_PLUGIN_DIR . 'includes/class-activator.php';
    _sse_log('6-activator-required');
    SkillScore_Ebook_Activator::activate();
    _sse_log('7-activation-complete');
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

_sse_log('3-hooks-registered');

/**
 * The core plugin class.
 */
_sse_log('3a-requiring-core');
require SKILLSCORE_EBOOK_PLUGIN_DIR . 'includes/class-ebook-core.php';
_sse_log('3b-core-required');

/**
 * Begins execution of the plugin.
 */
function run_skillscore_ebook() {
    _sse_log('4-run-start');
    $plugin = new SkillScore_Ebook_Core();
    _sse_log('4a-core-instantiated');
    $plugin->run();
    _sse_log('4b-run-complete');
}

_sse_log('3c-calling-run');
run_skillscore_ebook();
_sse_log('3d-run-returned');
