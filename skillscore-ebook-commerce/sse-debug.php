<?php
/**
 * TEMPORARY STANDALONE DEBUG FILE
 * DELETE THIS FILE after the activation error is resolved.
 *
 * Access in your browser:
 *   https://yourdomain.com/wp-content/plugins/skillscore-ebook-commerce/sse-debug.php
 *
 * It loads WordPress and then requires each plugin class one by one,
 * printing OK or the exact PHP error on screen.
 */

// Simple key guard — prevents public access
if ( empty( $_GET['go'] ) ) {
    die( 'Add ?go=1 to the URL to run the debug check.' );
}

// Force full error display — bypasses WordPress sandbox buffering
ini_set( 'display_errors', 1 );
ini_set( 'display_startup_errors', 1 );
error_reporting( E_ALL );

// ── Load WordPress ────────────────────────────────────────────────────────────
// Walk up to the WordPress root (plugin is 3 dirs deep inside wp-content)
$wp_load = dirname( __FILE__ ) . '/../../../wp-load.php';
if ( ! file_exists( $wp_load ) ) {
    die( 'Cannot find wp-load.php at: ' . htmlspecialchars( $wp_load ) );
}
require_once $wp_load;
// ─────────────────────────────────────────────────────────────────────────────

header( 'Content-Type: text/plain; charset=utf-8' );

echo "=== SkillScore Ebook Commerce — Debug Loader ===\n\n";
echo "PHP Version  : " . PHP_VERSION . "\n";
echo "WP Version   : " . get_bloginfo( 'version' ) . "\n";
echo "Plugin Dir   : " . plugin_dir_path( __FILE__ ) . "\n\n";

$includes_dir = plugin_dir_path( __FILE__ ) . 'includes/';

// Define plugin constants the same way the main file does
if ( ! defined( 'SKILLSCORE_EBOOK_VERSION' ) ) {
    define( 'SKILLSCORE_EBOOK_VERSION',        '1.0.0' );
    define( 'SKILLSCORE_EBOOK_PLUGIN_DIR',     plugin_dir_path( __FILE__ ) );
    define( 'SKILLSCORE_EBOOK_PLUGIN_URL',     plugin_dir_url( __FILE__ ) );
    define( 'SKILLSCORE_EBOOK_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}

$files = [
    'class-ebook-cpt.php',
    'class-shortcodes.php',
    'class-payment-handler.php',
    'class-download-handler.php',
    'class-voice-preview.php',
    'class-admin-settings.php',
    'class-sample-generator.php',
];

echo "--- Loading each include file ---\n";

foreach ( $files as $file ) {
    $path = $includes_dir . $file;
    echo "Loading: {$file} ... ";
    if ( ! file_exists( $path ) ) {
        echo "FILE NOT FOUND!\n";
        continue;
    }
    // require — any fatal error will print here immediately
    require_once $path;
    echo "OK\n";
}

echo "\n--- Instantiating SkillScore_Ebook_Core ---\n";
$core = new SkillScore_Ebook_Core();
echo "OK\n";

echo "\n=== All checks passed — plugin should activate cleanly. ===\n";
echo "\nIf you see this message, the issue is NOT in class loading.\n";
echo "Delete this file (sse-debug.php) from the plugin folder.\n";
