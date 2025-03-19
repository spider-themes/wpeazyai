<?php
/**
 * Helper functions for WP EazyAI Chatbot
 *
 * @package WP_EazyAI_Chatbot
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Cosine similarity helper for EazyAI Chatbot.
 */
function wpeazyai_cosine_similarity($vecA, $vecB) {
    $dot = 0.0;
    $normA = 0.0;
    $normB = 0.0;
    $len = min(count($vecA), count($vecB));
    for ($i = 0; $i < $len; $i++) {
        $dot += $vecA[$i] * $vecB[$i];
        $normA += $vecA[$i] ** 2;
        $normB += $vecB[$i] ** 2;
    }
    if ($normA == 0.0 || $normB == 0.0) {
        return 0.0;
    }
    return $dot / (sqrt($normA) * sqrt($normB));
}

/**
 * Eazy AI Chatbot include in EazyDocs Assistant
 */
$position_opt = get_option('wpeazyai_chatbox_position', 'after-tabs');
$position = ($position_opt === 'before-tabs') ? 0 : 50;

add_filter('eazydocs_assistant_tab', function ($tabs) {
    // Ensure the function exists before using it
    if ( ! function_exists('is_plugin_active')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    // Check if both plugins are active and merge is enabled
    if (
        is_plugin_active('eazydocs/eazydocs.php') &&
        is_plugin_active('eazydocs-pro/eazydocs.php') &&
        get_option('wpeazyai_enabled', false) == 1 &&
        get_option('wpeazyai_merge_eazydocs', false) == 1
    ) {
        $tabs[] = [
            'id'      => 'wpeazyai_merge_eazydocs',
            'heading' => get_option('wpeazyai_chatbot_label', 'Ai Chat'),
            'content' => do_shortcode('[wpeazyai_chatbot]'),
        ];
    }

    return $tabs;
}, (int) $position);