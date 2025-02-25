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


function wpeazyai_enqueue_chatbot_scripts() {
    if (!get_option('wpeazyai_enabled', true)) {
        return;
    }
    wp_enqueue_style('wpeazyai-chatbot-bootstrap', EAZYAI_CHATBOT_URL. 'assets/lib/bootstrap/css/bootstrap.min.css');
    wp_enqueue_script('jquery');
    wp_enqueue_script('wpeazyai-chatbot-custom-js', EAZYAI_CHATBOT_URL . 'assets/js/custom.js?v=' . wp_rand(), array('jquery'), null, true);
    wp_enqueue_style('wpeazyai-chatbot-custom-css', EAZYAI_CHATBOT_URL . 'assets/css/custom.css?v=' . wp_rand());
    wp_enqueue_style('wpeazyai-chatbot-font-awesome', EAZYAI_CHATBOT_URL. 'assets/lib/fontawesome/css/all.min.css');
    wp_enqueue_style('wpeazyai-dynamic-style', admin_url('admin-ajax.php?action=wpeazyai_dynamic_css'));

    $welcome_message = get_option('wpeazyai_welcome_message', 'What can I help you with?');
    wp_localize_script('wpeazyai-chatbot-custom-js', 'eazyai_chatbot_vars', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'bot_avatar' => EAZYAI_CHATBOT_URL . 'assets/icons/bot.png',
        'welcome_message' => $welcome_message,
    ));
}
add_action('wp_enqueue_scripts', 'wpeazyai_enqueue_chatbot_scripts');