<?php
/** Load WordPress Administration Bootstrap */
if (file_exists('../../../wp-load.php')) {
    require_once("../../../wp-load.php");
} else if (file_exists('../../wp-load.php')) {
    require_once("../../wp-load.php");
} else if (file_exists('../wp-load.php')) {
    require_once("../wp-load.php");
} else if (file_exists('wp-load.php')) {
    require_once("wp-load.php");
} else if (file_exists('../../../../wp-load.php')) {
    require_once("../../../../wp-load.php");
} else if (file_exists('../../../../wp-load.php')) {
    require_once("../../../../wp-load.php");
} else {

    if (file_exists('../../../wp-config.php')) {
        require_once("../../../wp-config.php");
    } else if (file_exists('../../wp-config.php')) {
        require_once("../../wp-config.php");
    } else if (file_exists('../wp-config.php')) {
        require_once("../wp-config.php");
    } else if (file_exists('wp-config.php')) {
        require_once("wp-config.php");
    } else if (file_exists('../../../../wp-config.php')) {
        require_once("../../../../wp-config.php");
    } else if (file_exists('../../../../wp-config.php')) {
        require_once("../../../../wp-config.php");
    } else {
        echo '<p>Failed to load bootstrap.</p>';
        exit;
    }
}

global $wp_db_version;
if ($wp_db_version < 8201) {
    // Pre 2.6 compatibility (BY Stephen Rider)
    if (!defined('WP_CONTENT_URL')) {
        if (defined('WP_SITEURL'))
            define('WP_CONTENT_URL', WP_SITEURL . '/wp-content');
        else
            define('WP_CONTENT_URL', get_option('url') . '/wp-content');
    }
    if (!defined('WP_CONTENT_DIR'))
        define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
    if (!defined('WP_PLUGIN_URL'))
        define('WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins');
    if (!defined('WP_PLUGIN_DIR'))
        define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');
}

require_once(ABSPATH . 'wp-admin/admin.php');
inlinkz_selectCollection();
?>