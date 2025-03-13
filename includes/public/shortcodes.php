<?php

/**
 * Shortcodes for WP EazyAI Chatbot (Bootstrap 5)
 *
 * @package WP_EazyAI_Chatbot
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Add shortcode to wp_footer
add_action('wp_footer', 'wpeazyai_chatbot_wp_footer');
function wpeazyai_chatbot_wp_footer() {
    echo do_shortcode('[wpeazyai_chatbot]');
}

function wpeazyai_chatbot_shortcode() {
    ob_start();
    
    // Check if enabled
    if ( !get_option('wpeazyai_enabled', true) ) {
        return '';
    }
    ?>
    <div id="eazyai-chatbox">
        <div id="eazyai-chatbox-inner-wrapper">
            <div id="eazyai-chatbox-header">
                <div id="eazyai-chatbox-header-text">
                    <strong><?php echo esc_html(get_option('wpeazyai_title', 'Support')); ?></strong>
                    <p><?php echo esc_html(get_option('wpeazyai_help_text', 'Ask our AI support assistant your questions about our platform, features, and services.')); ?></p>
                </div>
                <button id="EazyBotReload">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            <div class="eazybotchatbox">
                <div class="eazyai-msg user placeholder">
                    <p></p>
                </div>
                <div class="eazyai-msg bot">
                    <img src="<?php echo esc_url(EAZYAI_CHATBOT_URL . 'assets/icons/bot.png'); ?>" alt="Chatbot Avatar" />
                    <p><?php echo esc_attr(get_option('wpeazyai_welcome_message', 'What can I help you with?')); ?></p>
                </div>
            </div>
            <div id="eazyai-chatbox-footer">
                <div id="suggested-questions">
                    <p><?php esc_html_e('Not sure what to ask?', 'wp-eazyai'); ?></p>
                    <button onclick="$('#eazybotinput').val($(this).text()); $('#eazybotsend').click();">
                        <?php echo esc_html(get_option('wpeazyai_prebuilt_1', 'How do I get started?')); ?>
                    </button>
                    <button onclick="$('#eazybotinput').val($(this).text()); $('#eazybotsend').click();">
                        <?php echo esc_html(get_option('wpeazyai_prebuilt_2', 'What features are available?')); ?>
                    </button>
                    <button onclick="$('#eazybotinput').val($(this).text()); $('#eazybotsend').click();">
                        <?php echo esc_html(get_option('wpeazyai_prebuilt_3', 'How can I contact support?')); ?>
                    </button>
                </div>
                <div id="eazyai-input-container">
                    <input id="eazybotinput" type="text" placeholder="Send a message..." />
                    <button id="eazybotsend">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <button id="eazyai-help-button" onclick="EazyBotToggleChat()">
        <div>
            <i class="fas <?php echo esc_attr(get_option('wpeazyai_chat_icon', 'fa-question')); ?>" id="help-icon"></i>
            <i class="fas fa-times" id="close-icon"></i>
            <?php if ( !empty( get_option('wpeazyai_button_text')) ) : ?>
            <span> <?php echo esc_html(get_option('wpeazyai_button_text', 'Help')); ?> </span>
            <?php endif; ?>
        </div>
    </button>

    <?php
    return ob_get_clean();
}
add_shortcode('wpeazyai_chatbot', 'wpeazyai_chatbot_shortcode');
