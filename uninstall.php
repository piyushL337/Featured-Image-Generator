<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package Featured_Image_Generator
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('fig_settings');

// Delete any transients if added in the future
// delete_transient('fig_*');

// Note: We don't delete generated featured images as they are part of the media library
// and posts may still reference them. Users can delete them manually if needed.
