<?php
/**
 * RAW DEBUG — no WordPress loaded, so nothing can intercept the error.
 * Access: https://yourdomain.com/wp-content/plugins/skillscore-ebook-commerce/sse-raw.php?go=1
 * DELETE this file after the error is identified.
 */
if (empty($_GET['go'])) die('Add ?go=1 to the URL.');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Minimum constants so the class files don't exit at the top guard
if (!defined('ABSPATH'))   define('ABSPATH',   realpath(__DIR__ . '/../../../') . '/');
if (!defined('WPINC'))     define('WPINC',     'wp-includes');
if (!defined('SKILLSCORE_EBOOK_VERSION')) {
    define('SKILLSCORE_EBOOK_VERSION',        '1.0.0');
    define('SKILLSCORE_EBOOK_PLUGIN_DIR',     __DIR__ . '/');
    define('SKILLSCORE_EBOOK_PLUGIN_URL',     '');
    define('SKILLSCORE_EBOOK_PLUGIN_BASENAME','skillscore-ebook-commerce/skillscore-ebook-commerce.php');
}

// NOTE: WordPress is NOT loaded here — any fatal error will print on screen directly.

header('Content-Type: text/plain; charset=utf-8');
echo "PHP Version : " . PHP_VERSION . "\n";
echo "ABSPATH     : " . ABSPATH . "\n\n";

$dir   = __DIR__ . '/includes/';
$files = [
    'class-ebook-cpt.php',
    'class-shortcodes.php',
    'class-payment-handler.php',
    'class-download-handler.php',
    'class-voice-preview.php',
    'class-admin-settings.php',
    'class-sample-generator.php',
];

foreach ($files as $f) {
    echo "Loading {$f} ... ";
    flush();
    require_once $dir . $f;
    echo "OK\n";
    flush();
}

echo "\nAll files loaded OK.\n";
echo "The activation issue is NOT in class definitions — it must be triggered elsewhere.\n";
