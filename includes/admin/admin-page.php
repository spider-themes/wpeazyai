<?php

/**
 * Register Admin Page for WP EazyAI Chatbot
 *
 * @package WP_EazyAI_Chatbot
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}



/**
 * 3. Admin page/utility to chunk posts and store embeddings.
 */
add_action('admin_menu', 'wpeazyai_admin_menu');
function wpeazyai_admin_menu() {
    $hook = add_menu_page(
        __('EazyAI Chatbot', 'wp-eazyai-chatbot'),
        __('EazyAI Chatbot', 'wp-eazyai-chatbot'),
        'manage_options',
        'wp-eazyai-chatbot',
        'wpeazyai_admin_page',
        'dashicons-format-chat'
    );

    // Load screen options when this page loads
    add_action("load-$hook", 'wpeazyai_add_screen_options');
}

// add admin scripts
function wpeazyai_admin_scripts($hook) {
    // Only load on post editor screens.
    if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
        return;
    }
    wp_localize_script('jquery', 'wpeazyai_ajax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'post_id' => get_the_ID(),
        'nonce' => wp_create_nonce('wpeazyai_process')
    ));

    if (post_type_supports(get_post_type(), 'excerpt')) {
        wp_enqueue_script('eazyai-chatbot-admin-js', EAZYAI_CHATBOT_URL . 'assets/js/admin.js', array('wp-element'), wp_rand(), true);
        
    }
    if (post_type_supports(get_post_type(), 'post_tag') || post_type_supports(get_post_type(), 'category') || get_post_type() == 'post') {        
        wp_enqueue_script('eazyai-chatbot-tags-js', EAZYAI_CHATBOT_URL . 'assets/js/tags.js', array('wp-element'), wp_rand(), true);
    }
    
}
add_action('admin_enqueue_scripts', 'wpeazyai_admin_scripts');
function wpeazyai_add_screen_options() {
    $option = 'per_page';
    $args = array(
        'label' => __('Topics per page', 'wp-eazyai-chatbot'),
        'default' => 20,
        'option' => 'topics_per_page'
    );
    add_screen_option($option, $args);
}
// Add this function to save the screen options
function wpeazyai_set_screen_option($status, $option, $value) {
    if ('topics_per_page' === $option) {
        return $value;
    }
    return $status;
}
add_filter('set-screen-option', 'wpeazyai_set_screen_option', 10, 3);