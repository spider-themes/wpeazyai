<?php
/*
Plugin Name: EazyAI Chatbot
Plugin URI:  https://fiverr.com/wpfixit
Description: Integrating OpenAI embeddings for a chatbot, renamed to EazyAI Chatbot, with additional features such as knowledge base, tags, excerpts, and BBPress integration.
Version:     2.0.0
Author:      wpfixit
License:     GPLv2 or later
text-domain: 'wp-eazyai-chatbot'
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
define('EAZYAI_CHATBOT_DIR', plugin_dir_path(__FILE__));
define('EAZYAI_CHATBOT_URL', plugin_dir_url(__FILE__));
define('EAZYAI_CHATBOT_VERSION', '1.0');


// Core files
require_once EAZYAI_CHATBOT_DIR . 'includes/admin/admin-page.php';
require_once EAZYAI_CHATBOT_DIR . 'includes/admin/settings.php';

// API handlers
require_once EAZYAI_CHATBOT_DIR . 'includes/api/openai-handler.php';
require_once EAZYAI_CHATBOT_DIR . 'includes/api/chat-handler.php';

// Database handlers 
require_once EAZYAI_CHATBOT_DIR . 'includes/database/db-handler.php';

// Public files
require_once EAZYAI_CHATBOT_DIR . 'includes/public/shortcodes.php';
require_once EAZYAI_CHATBOT_DIR . 'includes/public/chat-interface.php';
require_once EAZYAI_CHATBOT_DIR . 'includes/public/css.php';

// Utility functions
require_once EAZYAI_CHATBOT_DIR . 'includes/utils/helper-functions.php';

/**
 * 1. Create a custom table for storing embeddings.
 */
register_activation_hook(__FILE__, 'wpeazyai_create_table');
function wpeazyai_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wpeazyai_embeddings';
    $charset_collate = $wpdb->get_charset_collate();

    // If you want to store embeddings in JSON format, use LONGTEXT
    $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `post_id` BIGINT UNSIGNED NOT NULL,
        `chunk_index` INT NOT NULL,
        `chunk_text` LONGTEXT NOT NULL,
        `embedding` LONGTEXT NOT NULL,
        PRIMARY KEY (`id`)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}