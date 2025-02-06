<?php

/**
 * Enqueue Scripts and css for frontend for WP EazyAI Chatbot
 *
 * @package WP_EazyAI_Chatbot
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_wpeazyai_dynamic_css', 'wpeazyai_generate_dynamic_css'); // For logged-in users
add_action('wp_ajax_nopriv_wpeazyai_dynamic_css', 'wpeazyai_generate_dynamic_css'); // For guests

function wpeazyai_generate_dynamic_css() {
header("Content-type: text/css; charset: UTF-8");
// Load WordPress functions if not already available
if (!function_exists('esc_attr')) {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
}
$primary_color = esc_attr(get_option('wpeazyai_primary_color', '#0066cc'));
$chat_bg_color = esc_attr(get_option('wpeazyai_chat_bg_color', '#f3f4f6'));

echo esc_html("
.eazybot-bg-primary, .eazybot-bg-primary:hover, .eazybot-bg-primary:focus  {
    background-color: " . esc_attr($primary_color) . " !important; ;
}
.eazybot-bg-primary-foreground {
    background-color: " . esc_attr($primary_color) . " !important;
}
.eazybot-text-primary-foreground {
    color: #FFFFFF;
}
");
exit;
}