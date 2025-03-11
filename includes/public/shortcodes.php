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
    <div id="eazyai-chatbox" class="position-fixed bottom-0 end-0 m-4 bg-white rounded shadow-lg overflow-hidden z-150 d-none" style="width: 400px;">
        <div class="eazybot-bg-primary bg-primary text-white p-3 d-flex align-items-center justify-content-between">
            <div class="text-center text-white">
                <strong class="h5 d-block"><?php echo esc_html(get_option('wpeazyai_title', 'Support')); ?></strong>
                <p class="mb-0"><?php echo esc_html(get_option('wpeazyai_help_text', 'Ask our AI support assistant your questions about our platform, features, and services.')); ?></p>
            </div>
            <button class="btn btn-link text-white p-0" id="EazyBotReload">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
        <div class="p-3 overflow-auto eazybotchatbox" style="height: calc(70vh - 130px); min-height: 300px; max-height: 400px;">
            <div class="d-flex align-items-start mb-3">
                <img class="rounded-circle me-2" src="<?php echo esc_url(EAZYAI_CHATBOT_URL . 'assets/icons/bot.png'); ?>" alt="Chatbot Avatar" style="width: 32px; height: 32px;" />
                <div class="bg-light p-2 rounded">
                    <p class="text-dark mb-0"><?php echo esc_attr(get_option('wpeazyai_welcome_message', 'What can I help you with?')); ?></p>
                </div>
            </div>
        </div>
        <div id="suggested-questions" class="px-3 py-2 border-top">
            <p class="text-muted small mb-2"><?php esc_html_e('Not sure what to ask?', 'wp-eazyai'); ?></p>
            <button onclick="$('#eazybotinput').val($(this).text()); $('#eazybotsend').click();" class="btn btn-light w-100 text-start p-2 mb-1 small">
                <?php echo esc_html(get_option('wpeazyai_prebuilt_1', 'How do I get started?')); ?>
            </button>
            <button onclick="$('#eazybotinput').val($(this).text()); $('#eazybotsend').click();" class="btn btn-light w-100 text-start p-2 mb-1 small">
                <?php echo esc_html(get_option('wpeazyai_prebuilt_2', 'What features are available?')); ?>
            </button>
            <button onclick="$('#eazybotinput').val($(this).text()); $('#eazybotsend').click();" class="btn btn-light w-100 text-start p-2 mb-1 small">
                <?php echo esc_html(get_option('wpeazyai_prebuilt_3', 'How can I contact support?')); ?>
            </button>
        </div>
        <div class="d-flex align-items-center p-2 border-top">
            <input id="eazybotinput" type="text" class="form-control me-2" placeholder="Send a message..." />
            <button id="eazybotsend" class="btn btn-secondary">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <button id="eazyai-help-button" class="eazybot-bg-primary position-fixed bottom-0 end-0 bg-primary text-white p-3 rounded-circle shadow-lg z-150 border-0" onclick="EazyBotToggleChat()">
        <div class="d-flex align-items-center gap-2">
            <i class="fas <?php echo esc_attr(get_option('wpeazyai_chat_icon', 'fa-question')); ?>" id="help-icon"></i>
            <i class="fas fa-times d-none" id="close-icon"></i>
            <?php if ( !empty( get_option('wpeazyai_button_text')) ) : ?>
            <span> <?php echo esc_html(get_option('wpeazyai_button_text', 'Help')); ?> </span>
            <?php endif; ?>
        </div>
    </button>

    <?php
    return ob_get_clean();
}
add_shortcode('wpeazyai_chatbot', 'wpeazyai_chatbot_shortcode');
