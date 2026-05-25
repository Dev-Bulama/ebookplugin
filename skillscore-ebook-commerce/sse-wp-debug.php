<?php
/**
 * FINAL DEBUG — loads WordPress but disables its error interceptor.
 * Access: https://yourdomain.com/wp-content/plugins/skillscore-ebook-commerce/sse-wp-debug.php?go=1
 * DELETE after use.
 */
if (empty($_GET['go'])) die('Add ?go=1 to the URL.');

// ── Disable WordPress "There has been a critical error" page ──────────────────
define('WP_DISABLE_FATAL_ERROR_HANDLER', true);

// ── Force raw PHP error display ───────────────────────────────────────────────
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ── Load WordPress ────────────────────────────────────────────────────────────
$wp_load = dirname(__FILE__) . '/../../../wp-load.php';
require_once $wp_load;

// Re-assert display_errors in case WordPress overrode it
ini_set('display_errors', 1);

header('Content-Type: text/plain; charset=utf-8');

echo "=== SSE WordPress Debug ===\n\n";
echo "PHP Version : " . PHP_VERSION . "\n";
echo "WP Version  : " . get_bloginfo('version') . "\n\n";

// ── Check if our classes are ALREADY defined (loaded by WordPress) ────────────
$our_classes = [
    'SkillScore_Ebook_CPT',
    'SkillScore_Ebook_Shortcodes',
    'SkillScore_Ebook_Payment_Handler',
    'SkillScore_Ebook_Download_Handler',
    'SkillScore_Ebook_Voice_Preview',
    'SkillScore_Ebook_Admin_Settings',
    'SkillScore_Sample_Generator',
    'SkillScore_Ebook_Core',
];

echo "--- Class status after wp-load.php ---\n";
foreach ($our_classes as $cls) {
    echo str_pad($cls, 45) . ': ' . (class_exists($cls, false) ? 'ALREADY DEFINED' : 'not defined') . "\n";
}
echo "\n";

// ── Check active plugins list ─────────────────────────────────────────────────
$active = get_option('active_plugins', []);
echo "--- Active plugins ---\n";
foreach ($active as $p) {
    $marker = (strpos($p, 'skillscore') !== false) ? '  <-- OUR PLUGIN' : '';
    echo $p . $marker . "\n";
}
echo "\n";

// ── Check paused (error-crashed) plugins ─────────────────────────────────────
$paused = get_option('_paused_plugins', []);
echo "--- Paused (fatal-errored) plugins ---\n";
if (empty($paused)) {
    echo "(none)\n";
} else {
    foreach ($paused as $slug => $data) {
        echo $slug . ": " . print_r($data, true) . "\n";
    }
}
echo "\n";

// ── Now try loading our class files ──────────────────────────────────────────
if (!defined('SKILLSCORE_EBOOK_VERSION')) {
    define('SKILLSCORE_EBOOK_VERSION',        '1.0.0');
    define('SKILLSCORE_EBOOK_PLUGIN_DIR',     __DIR__ . '/');
    define('SKILLSCORE_EBOOK_PLUGIN_URL',     plugin_dir_url(__FILE__));
    define('SKILLSCORE_EBOOK_PLUGIN_BASENAME', plugin_basename(__FILE__));
}

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

echo "--- Loading include files ---\n";
foreach ($files as $f) {
    echo "Loading {$f} ... ";
    flush();
    require_once $dir . $f;
    echo "OK\n";
    flush();
}

echo "\n--- Instantiating SkillScore_Ebook_Core ---\n";
flush();
$core = new SkillScore_Ebook_Core();
echo "OK\n\n";
echo "=== All checks passed. Plugin should activate. ===\n";
