<?php
/**
 * Admin Page Setting HTML JS and CSS for WP EazyAI Chatbot
 *
 * @package WP_EazyAI_Chatbot
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}


function wpeazyai_admin_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    if (isset($_POST['wpeazyai_save_settings'])) {
        if (!isset($_POST['wpeazyai_settings_nonce']) || !wp_verify_nonce(wp_unslash($_POST['wpeazyai_settings_nonce']), 'wpeazyai_settings')) {
            wp_die('Invalid nonce');
        }
        $api_key = sanitize_text_field($_POST['wpeazyai_api_key']);
        update_option('wpeazyai_api_key', $api_key);
        $post_types = isset($_POST['wpeazyai_post_types']) ? array_map('sanitize_text_field', $_POST['wpeazyai_post_types']) : [];
        update_option('wpeazyai_enabled', isset($_POST['wpeazyai_enabled']) ? 1 : 0);
        update_option('wpeazyai_selected_post_types', $post_types);
        update_option('wpeazyai_primary_color', sanitize_text_field($_POST['wpeazyai_primary_color']));
        update_option('wpeazyai_chat_bg_color', sanitize_text_field($_POST['wpeazyai_chat_bg_color']));
        update_option('wpeazyai_title', sanitize_text_field($_POST['wpeazyai_title']));
        update_option('wpeazyai_help_text', sanitize_text_field($_POST['wpeazyai_help_text']));
        update_option('wpeazyai_chat_icon', sanitize_text_field($_POST['wpeazyai_chat_icon']));
        update_option('wpeazyai_prebuilt_1', sanitize_text_field($_POST['wpeazyai_prebuilt_1']));
        update_option('wpeazyai_prebuilt_2', sanitize_text_field($_POST['wpeazyai_prebuilt_2']));
        update_option('wpeazyai_prebuilt_3', sanitize_text_field($_POST['wpeazyai_prebuilt_3']));
        update_option('wpeazyai_welcome_message', sanitize_text_field($_POST['wpeazyai_welcome_message']));
        update_option('wpeazyai_button_text', sanitize_text_field($_POST['wpeazyai_button_text']));

    }

    $selected_post_types = get_option('wpeazyai_selected_post_types', []);
    // enqueue Font Awesome CSS inline for admin page
    wp_add_inline_style('eazyai-chatbot-admin-fa', "@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');");
    wp_enqueue_script('wp-util');
    wp_enqueue_script('wpeazyai-admin-tabs', EAZYAI_CHATBOT_URL . 'assets/js/admin-tabs.js', array('jquery'), '1.0', true);
    $all_post_types = get_post_types(['public' => true], 'objects');
    // exclude post types that do not support editor
    $all_post_types = array_filter($all_post_types, function ($post_type) {
        return post_type_supports($post_type->name, 'editor');
    });
    $api_key = get_option('wpeazyai_api_key', '');
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('EazyAI Chatbot', 'wp-eazyai-chatbot'); ?></h1>
        
        <h2 class="nav-tab-wrapper">
            <a href="#config" class="nav-tab wpeazyai-nav-tab" data-tab="config"><?php echo esc_html__('Configuration', 'wp-eazyai-chatbot'); ?></a>
            <a href="#globaltags" class="nav-tab wpeazyai-nav-tab" data-tab="globaltags"><?php echo esc_html__('Generate Tags and Excerpts', 'wp-eazyai-chatbot'); ?></a>
            <a href="#bbpress" class="nav-tab wpeazyai-nav-tab" data-tab="bbpress"><?php echo esc_html__('BBPress', 'wp-eazyai-chatbot'); ?></a>
        </h2>

        

        <div id="config" class="tab-content wpeazyai-tab-pane" >
            <div style="margin-top:20px">
                <form method="post">
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="wpeazyai_api_key"><?php esc_html_e('OpenAI API Key:', 'wp-eazyai-chatbot'); ?></label></th>
                            <td>
                                <input 
                                    type="password" 
                                    name="wpeazyai_api_key" 
                                    id="wpeazyai_api_key" 
                                    class="regular-text" 
                                    value="<?php echo esc_attr($api_key); ?>" 
                                    required 
                                />
                                <p class="description">
                                    <?php 
                                    printf(
                                        /* translators: %s: OpenAI API keys URL */
                                        esc_html__('Get your API key from %s', 'wp-eazyai-chatbot'),
                                        '<a href="' . esc_url('https://platform.openai.com/account/api-keys') . '" target="_blank">' . esc_html__('OpenAI', 'wp-eazyai-chatbot') . '</a>'
                                    ); 
                                    ?>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><?php esc_html_e('Select Post Types for Knowledge Base:', 'wp-eazyai-chatbot'); ?></th>
                            <td>
                                <?php 
                                foreach ($all_post_types as $post_type) {
                                    $post_type_name = esc_attr($post_type->name);
                                    $is_checked = in_array($post_type->name, $selected_post_types, true);
                                ?>
                                    <label>
                                        <input 
                                            type="checkbox" 
                                            name="wpeazyai_post_types[]" 
                                            value="<?php echo esc_attr($post_type_name); ?>"
                                            <?php echo checked($is_checked, true, false); ?>
                                        />
                                        <?php echo esc_html($post_type->label); ?>
                                    </label><br/>
                                <?php 
                                } ?>
                                    
                                <p class="description">
                                    <?php esc_html_e('Select post types to include in the chatbot knowledge base, once selected, save settings, visit the Knowledge Base tab to process posts.', 'wp-eazyai-chatbot'); ?>
                                </p>
                                <?php wp_nonce_field('wpeazyai_settings', 'wpeazyai_settings_nonce'); ?>
                            </td>
                        </tr>
                        
                        <?php 
                        
                        ?>
                        <tr>
                            <th><h3><?php esc_html_e('Knowledge Base', 'wp-eazyai-chatbot'); ?></h3></th>
                            <td>
                                <div style="margin-top:20px">
                                <p><?php esc_html_e('Configure Post Type from Configurations, Use this button to add knowledge base to your chatbot.', 'wp-eazyai-chatbot'); ?></p>
                                <button <?php echo empty($selected_post_types) ? 'disabled="disabled"' : ''; ?> type="button" id="process_posts" class="button button-primary"><?php esc_html_e('Add Knowledge Base', 'wp-eazyai-chatbot'); ?></button>
                                
                                <div id="progress_bar" style="display:none; margin-top: 20px;">
                                    <div class="progress-label"><?php esc_html_e('Processing posts...', 'wp-eazyai-chatbot'); ?> <span id="progress_status">0%</span></div>
                                    <progress id="progress" value="0" max="100" style="width: 100%;"></progress>
                                </div>
                                <div id="process_log" style="margin-top: 20px;"></div>
                            </div>
                            </td>
                        </tr>

                        <tr>
                            <th colspan="2"><h3><?php esc_html_e('Chatbot Appearance', 'wp-eazyai-chatbot'); ?></h3></th>
                        </tr>

                        <tr>
                            <th scope="row"><label for="wpeazyai_enabled"><?php esc_html_e('Enable Chatbot:', 'wp-eazyai-chatbot'); ?></label></th>
                            <td><input type="checkbox" name="wpeazyai_enabled" id="wpeazyai_enabled" value="1" <?php checked(get_option('wpeazyai_enabled', true)); ?>></td>
                        </tr>

                        <tr>
                            <th scope="row"><label for="wpeazyai_primary_color"><?php esc_html_e('Primary Color:', 'wp-eazyai-chatbot'); ?></label></th>
                            <td><input type="color" name="wpeazyai_primary_color" id="wpeazyai_primary_color" value="<?php echo esc_attr(get_option('wpeazyai_primary_color', '#0066cc')); ?>"></td>
                        </tr>

                        <tr>
                            <th scope="row"><label for="wpeazyai_chat_bg_color"><?php esc_html_e('Chat Background Color:', 'wp-eazyai-chatbot'); ?></label></th>
                            <td><input type="color" name="wpeazyai_chat_bg_color" id="wpeazyai_chat_bg_color" value="<?php echo esc_attr(get_option('wpeazyai_chat_bg_color', '#f3f4f6')); ?>"></td>
                        </tr>

                        <tr>
                            <th scope="row"><label for="wpeazyai_title"><?php esc_html_e('Chatbot Title:', 'wp-eazyai-chatbot'); ?></label></th>
                            <td><input type="text" name="wpeazyai_title" id="wpeazyai_title" class="regular-text" value="<?php echo esc_attr(get_option('wpeazyai_title', 'Support')); ?>"></td>
                        </tr>

                        <tr>
                            <th scope="row"><label for="wpeazyai_help_text"><?php esc_html_e('Help Text:', 'wp-eazyai-chatbot'); ?></label></th>
                            <td><textarea name="wpeazyai_help_text" id="wpeazyai_help_text" rows="3" class="regular-text"><?php echo esc_textarea(get_option('wpeazyai_help_text', 'Ask our AI support assistant your questions about our platform, features, and services.')); ?></textarea></td>
                        </tr>

                        <tr>
                            <th scope="row"><label><?php esc_html_e('Chat Icon:', 'wp-eazyai-chatbot'); ?></label></th>
                            <td>
                                <div class="wpeazyai-icon-select">
                                    <?php
                                    $icons = array(
                                        'fa-solid fa-message' => __('Message', 'wp-eazyai-chatbot'),
                                        'fa-regular fa-comments' => __('Comments', 'wp-eazyai-chatbot'),
                                        'fa-regular fa-circle-question' => __('Question', 'wp-eazyai-chatbot'),
                                        'fa-solid fa-robot' => __('Robot', 'wp-eazyai-chatbot'),
                                        'fa-solid fa-headset' => __('Headset', 'wp-eazyai-chatbot')
                                    );
                                    foreach ($icons as $icon => $label) : ?>
                                    <label>
                                        <input type="radio" name="wpeazyai_chat_icon" value="<?php echo esc_attr($icon); ?>" <?php checked(get_option('wpeazyai_chat_icon', 'fa-regular fa-message'), $icon); ?>>
                                        <i class="<?php echo esc_attr($icon); ?>"></i> <?php echo esc_html($label); ?>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="wpeazyai_button_text"><?php esc_html_e('Chat Button Text:', 'wp-eazyai-chatbot'); ?></label></th>
                            <td><input type="text" name="wpeazyai_button_text" id="wpeazyai_button_text" class="regular-text" value="<?php echo esc_attr(get_option('wpeazyai_button_text', 'Help')); ?>"></td>
                        </tr>
                        <tr>
                            <th colspan="2"><h3><?php esc_html_e('Pre-built Messages', 'wp-eazyai-chatbot'); ?></h3></th>
                        </tr>

                        <tr>
                            <th scope="row"><label for="wpeazyai_prebuilt_1"><?php esc_html_e('Pre-built Message 1:', 'wp-eazyai-chatbot'); ?></label></th>
                            <td><input type="text" name="wpeazyai_prebuilt_1" id="wpeazyai_prebuilt_1" class="regular-text" value="<?php echo esc_attr(get_option('wpeazyai_prebuilt_1', 'How do I get started?')); ?>"></td>
                        </tr>

                        <tr>
                            <th scope="row"><label for="wpeazyai_prebuilt_2"><?php esc_html_e('Pre-built Message 2:', 'wp-eazyai-chatbot'); ?></label></th>
                            <td><input type="text" name="wpeazyai_prebuilt_2" id="wpeazyai_prebuilt_2" class="regular-text" value="<?php echo esc_attr(get_option('wpeazyai_prebuilt_2', 'What features are available?')); ?>"></td>
                        </tr>

                        <tr>
                            <th scope="row"><label for="wpeazyai_prebuilt_3"><?php esc_html_e('Pre-built Message 3:', 'wp-eazyai-chatbot'); ?></label></th>
                            <td><input type="text" name="wpeazyai_prebuilt_3" id="wpeazyai_prebuilt_3" class="regular-text" value="<?php echo esc_attr(get_option('wpeazyai_prebuilt_3', 'How can I contact support?')); ?>"></td>
                        </tr>

                        <tr>
                            <th scope="row"><label for="wpeazyai_welcome_message"><?php esc_html_e('Welcome Message:', 'wp-eazyai-chatbot'); ?></label></th>
                            <td><input type="text" name="wpeazyai_welcome_message" id="wpeazyai_welcome_message" class="regular-text" value="<?php echo esc_attr(get_option('wpeazyai_welcome_message', 'What can I help you with?')); ?>"></td>
                        </tr>
                    </table>

                    <style type="text/css">
                        .wpeazyai-icon-select {
                            display: flex;
                            flex-wrap: wrap;
                            gap: 15px;
                            margin: 5px 0;
                        }
                        .wpeazyai-icon-select label {
                            cursor: pointer;
                            display: inline-flex;
                            align-items: center;
                            gap: 5px;
                        }
                        .wpeazyai-icon-select input[type="radio"] {
                            margin-right: 5px;
                        }
                    </style>

                    <p class="submit">
                        <input type="submit" name="wpeazyai_save_settings" class="button button-primary" value="<?php echo esc_attr__('Save Settings', 'wp-eazyai-chatbot'); ?>">
                        <?php wp_nonce_field('wpeazyai_settings', 'wpeazyai_settings_nonce'); ?>
                    </p>
                </form>
            </div>
        </div>
        <div id="globaltags" class="tab-content wpeazyai-tab-pane" style="display:none">
            <?php 
            // Get all post types that support either taxonomies or excerpts
            $post_types = get_post_types(['public' => true], 'objects');
            $supporting_types = [];
            
            foreach ($post_types as $post_type) {
            if (post_type_supports($post_type->name, 'excerpt') || 
                get_object_taxonomies($post_type->name)) {
                $supporting_types[$post_type->name] = $post_type;
            }
            }
            ?>
            
            <div style="margin-top:20px">
            <h3><?php esc_html_e('Generate Content', 'wp-eazyai-chatbot'); ?></h3>
            <p><?php esc_html_e('Select a post type to generate content. Options will appear based on post type support.', 'wp-eazyai-chatbot'); ?></p>
            
            <div class="post-type-accordion">
            <?php foreach ($supporting_types as $name => $post_type): ?>
                <div class="accordion-item" data-post-type="<?php echo esc_attr($name); ?>">
                <div class="accordion-header">
                    <input type="radio" name="global_post_type" id="pt_<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($name); ?>">
                    <label for="pt_<?php echo esc_attr($name); ?>"><?php echo esc_html($post_type->label); ?></label>
                </div>
                
                <div class="accordion-content" style="display:none">
                    <?php if (post_type_supports($name, 'excerpt')): ?>
                    <div class="option-group">
                    <h4><?php esc_html_e('Excerpt Options', 'wp-eazyai-chatbot'); ?></h4>
                    <label>
                        <input type="checkbox" name="generate_excerpt" checked>
                        <?php esc_html_e('Generate Excerpts', 'wp-eazyai-chatbot'); ?>
                    </label>
                    <div class="sub-option">
                        <label>
                        <?php esc_html_e('Excerpt length (words):', 'wp-eazyai-chatbot'); ?>
                        <input type="number" name="excerpt_length" value="50" min="10" max="200">
                        </label>
                    </div>
                    </div>
                    <?php endif; ?>

                    <?php if (post_type_supports($name, 'post_tags') || is_object_in_taxonomy($name, 'post_tag')): ?>
                    <div class="option-group">
                    <h4><?php esc_html_e('Tags Options', 'wp-eazyai-chatbot'); ?></h4>
                    <label>
                        <input type="checkbox" name="generate_post_tag" checked>
                        <?php esc_html_e('Generate Tags', 'wp-eazyai-chatbot'); ?>
                    </label>
                    <div class="sub-option">
                        <label>
                        <?php esc_html_e('Number of tags:', 'wp-eazyai-chatbot'); ?>
                        <input type="number" name="number_of_post_tag" value="5" min="1" max="10">
                        </label>
                    </div>
                    </div>
                    <?php endif; ?>

                    <?php 
                    $taxonomies = get_object_taxonomies($name, 'objects');
                    // Remove post_tag from taxonomies array
                    unset($taxonomies['post_tag']);
                    if (!empty($taxonomies)): ?>
                    <div class="option-group taxonomy-options">
                    <h4><?php esc_html_e('Additional Taxonomy Options', 'wp-eazyai-chatbot'); ?></h4>
                    <label>
                        <?php esc_html_e('Select Taxonomy:', 'wp-eazyai-chatbot'); ?>
                        <select name="selected_taxonomy" class="taxonomy-selector">
                        <?php foreach ($taxonomies as $tax_name => $taxonomy): ?>
                        <option value="<?php echo esc_attr($tax_name); ?>">
                            <?php echo esc_html($taxonomy->labels->singular_name); ?>
                        </option>
                        <?php endforeach; ?>
                        </select>
                    </label>

                    <?php foreach ($taxonomies as $tax_name => $taxonomy): ?>
                    <div class="taxonomy-settings" data-taxonomy="<?php echo esc_attr($tax_name); ?>" style="display: none;">
                        <label>
                        <input type="checkbox" name="generate_<?php echo esc_attr($tax_name); ?>" checked>
                        <?php /* translators: %s is the taxonomy label (plural form) */
                        printf(esc_html__('Generate %s', 'wp-eazyai-chatbot'), esc_html($taxonomy->labels->name)); ?>
                        </label>
                        <div class="sub-option">
                        <label>
                            <?php 
                            /* Translators: %s is the taxonomy label (singular form) */
                            printf(esc_html__('Number of %s:', 'wp-eazyai-chatbot'), esc_html($taxonomy->labels->name)); ?>
                            <input type="number" name="number_of_<?php echo esc_attr($tax_name); ?>" 
                            value="5" min="1" max="10">
                        </label>
                        <label>
                            <?php esc_html_e('Generate for posts with:', 'wp-eazyai-chatbot'); ?>
                            <select name="<?php echo esc_attr($tax_name); ?>_threshold">
                            <option value="0"><?php esc_html_e('0 Terms', 'wp-eazyai-chatbot'); ?></option>
                            <option value="1"><?php esc_html_e('1 Term or less', 'wp-eazyai-chatbot'); ?></option>
                            <option value="2"><?php esc_html_e('2 Terms or less', 'wp-eazyai-chatbot'); ?></option>
                            <option value="3"><?php esc_html_e('3 Terms or less', 'wp-eazyai-chatbot'); ?></option>
                            </select>
                        </label>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                </div>
            <?php endforeach; ?>
            </div>

            <button type="button" id="generate_global_tags" class="button button-primary" style="margin-top: 20px;">
                <?php esc_html_e('Generate Selected Content', 'wp-eazyai-chatbot'); ?>
            </button>

            <div id="global_progress_bar" style="display:none; margin-top: 20px;">
                <div class="progress-label"><?php esc_html_e('Processing...', 'wp-eazyai-chatbot'); ?> <span id="global_progress_status">0%</span></div>
                <progress id="global_progress" value="0" max="100" style="width: 100%;"></progress>
            </div>
            <div id="global_process_log" style="margin-top: 20px;"></div>
            </div>

            <style>
            .post-type-accordion {
            border: 1px solid #ccc;
            margin: 20px 0;
            }
            .accordion-item {
            border-bottom: 1px solid #ccc;
            }
            .accordion-item:last-child {
            border-bottom: none;
            }
            .accordion-header {
            padding: 10px 15px;
            background: #f5f5f5;
            cursor: pointer;
            display: flex;
            align-items: center;
            }
            .accordion-header:hover {
            background: #f0f0f0;
            }
            .accordion-content {
            padding: 15px;
            background: #fff;
            }
            .toggle-icon {
            margin-left: auto;
            transition: transform 0.3s;
            }
            .option-group {
            margin-bottom: 20px;
            }
            .option-group h4 {
            margin: 0 0 10px 0;
            }
            .sub-option {
            margin-left: 25px;
            margin-top: 10px;
            }
            .sub-option label {
            display: block;
            margin-bottom: 5px;
            }
            </style>

            <script>
            jQuery(document).ready(function($) {
            // Accordion functionality
            // $('.accordion-header').click(function() {
            //     const $content = $(this).next('.accordion-content');
            //     const $icon = $(this).find('.toggle-icon');
                
            //     $content.slideToggle();
            //     $icon.css('transform', $content.is(':visible') ? 'rotate(180deg)' : 'rotate(0)');
            // });

            // Show options when radio is selected
            $('input[name="global_post_type"]').change(function() {
                const $content = $(this).closest('.accordion-header').next('.accordion-content');
                $('.accordion-content').not($content).slideUp();
                $content.slideDown();
                
                // Show first taxonomy settings by default
                const $taxonomySelector = $content.find('.taxonomy-selector');
                if ($taxonomySelector.length) {
                const firstTaxonomy = $taxonomySelector.val();
                $content.find('.taxonomy-settings').hide();
                $content.find(`.taxonomy-settings[data-taxonomy="${firstTaxonomy}"]`).show();
                }
            });

            // Handle taxonomy selector change
            $('.taxonomy-selector').change(function() {
                const selectedTaxonomy = $(this).val();
                const $container = $(this).closest('.taxonomy-options');
                $container.find('.taxonomy-settings').hide();
                $container.find(`.taxonomy-settings[data-taxonomy="${selectedTaxonomy}"]`).show();
            });
            });
            </script>
        </div>
        <div id="bbpress" class="tab-content wpeazyai-tab-pane" style="display:none">
            <div style="margin-top:20px">
                <h3><?php esc_html_e('Convert BBPress Topics to Posts', 'wp-eazyai-chatbot'); ?></h3>
                <p><?php esc_html_e('Select resolved topics to convert them into posts. This helps build your knowledge base.', 'wp-eazyai-chatbot'); ?></p>

                <?php
                // Check if BBPress is active
                if (!class_exists('bbPress')) {
                    echo '<div class="notice notice-warning"><p>BBPress is not installed or activated.</p></div>';
                    echo esc_html__('Please install and activate BBPress to use this feature.', 'wp-eazyai-chatbot');
                }
                else {
                // Get all resolved topics
                $args = array(
                    'post_type' => 'topic',
                    'post_status' => 'closed',
                    'posts_per_page' => -1
                );

                $topics = get_posts($args);

                // Get available post types
                $post_types = get_post_types(['public' => true], 'objects');
                unset($post_types['topic']);
                unset($post_types['reply']);
                unset($post_types['forum']);
                ?>

                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th scope="col" class="manage-column column-cb check-column">
                                <input type="checkbox" id="select-all-topics">
                            </th>
                            <th scope="col" class="manage-column">
                                <?php esc_html_e('Topic Title', 'wp-eazyai-chatbot'); ?>
                            </th>
                            <th scope="col" class="manage-column">
                                <?php esc_html_e('Forum', 'wp-eazyai-chatbot'); ?>
                            </th>
                            <th scope="col" class="manage-column">
                                <?php esc_html_e('Author', 'wp-eazyai-chatbot'); ?>
                            </th>
                            <th scope="col" class="manage-column">
                                <?php esc_html_e('Date', 'wp-eazyai-chatbot'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topics as $topic): ?>
                        <tr>
                            <td>
                                <input type="checkbox" name="topics[]" value="<?php echo esc_attr($topic->ID); ?>">
                            </td>
                            <td>
                                <a href="<?php echo esc_url(get_permalink($topic->ID)); ?>">
                                    <?php echo esc_html($topic->post_title); ?>
                                </a>
                            </td>
                            <td>
                                <?php echo esc_html(get_the_title(bbp_get_topic_forum_id($topic->ID))); ?>
                            </td>
                            <td>
                                <?php echo esc_html(get_the_author_meta('display_name', $topic->post_author)); ?>
                            </td>
                            <td>
                                <?php echo esc_html(get_the_date('', $topic->ID)); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="margin-top: 20px;">
                    <select id="target-post-type" class="regular-text">
                        <?php foreach ($post_types as $type => $obj): ?>
                        <option value="<?php echo esc_attr($type); ?>"><?php echo esc_html($obj->labels->singular_name); ?></option>
                        <?php endforeach; ?>
                    </select>

                    <button id="convert-topics" class="button button-primary">
                        <?php esc_html_e('Convert Selected Topics', 'wp-eazyai-chatbot'); ?>
                    </button>
                </div>

                <div id="conversion-progress" style="display:none; margin-top: 20px;">
                    <div class="progress-label" id="convertion-label"><?php esc_html_e('Converting...', 'wp-eazyai-chatbot'); ?> <span id="conversion-status">0%</span></div>
                    <progress id="conversion-bar" value="0" max="100" style="width: 100%;"></progress>
                </div>
                <div id="conversion-log" style="margin-top: 20px;"></div>

                <script>
                jQuery(document).ready(function($) {
                    $('#select-all-topics').change(function() {
                        $('input[name="topics[]"]').prop('checked', $(this).prop('checked'));
                    });

                    $('#convert-topics').click(function() {
                        var selectedTopics = $('input[name="topics[]"]:checked').map(function() {
                            return $(this).val();
                        }).get();

                        if (selectedTopics.length === 0) {
                            alert(wp.i18n.__('Please select at least one topic to convert.', 'wp-eazyai-chatbot'));
                            return;
                        }

                        if (!confirm(wp.i18n.__('Are you sure you want to convert ' + selectedTopics.length + ' selected topics?', 'wp-eazyai-chatbot'))) {
                            return;
                        }

                        var postType = $('#target-post-type').val();
                        var button = $(this);
                        button.prop('disabled', true);
                        $('#conversion-progress').show();
                        $('#conversion-log').empty();
                        $('#conversion-status').text('0%');
                        $('#conversion-bar').val(0);

                        var successCount = 0;
                        var failCount = 0;
                        var nonce = '<?php echo esc_js(wp_create_nonce("wpeazyai_convert")); ?>';
                        
                        function updateStatus() {
                            var total = successCount + failCount;
                            var progress = Math.round((total / selectedTopics.length) * 100);
                            $('#conversion-bar').val(progress);
                            $('#conversion-status').text(progress + '%');
                            $('#convertion-label').text(wp.i18n.__('Converting... (' + successCount + ' succeeded, ' + failCount + ' failed)', 'wp-eazyai-chatbot'));
                        }

                        // Process topics concurrently but with a limit
                        var maxConcurrent = 3;
                        var running = 0;
                        var index = 0;

                        function processNext() {
                            if (index >= selectedTopics.length) {
                                if (running === 0) {
                                    button.prop('disabled', false);
                                    $('#conversion-log').prepend('<div><strong>' + 
                                        wp.i18n.__('Conversion complete! Successfully converted: ' + successCount + ' topics, Failed: ' + failCount + ' topics', 'wp-eazyai-chatbot') + 
                                        '</strong></div>');
                                }
                                return;
                            }

                            running++;
                            var topicId = selectedTopics[index++];

                            $.post(ajaxurl, {
                                action: 'wpeazyai_convert_topic',
                                topic_id: topicId,
                                post_type: postType,
                                nonce: nonce
                            })
                            .done(function(response) {
                                if (response.success) {
                                    successCount++;
                                    $('#conversion-log').prepend('<div>' + 
                                        wp.i18n.__('Converted:', 'wp-eazyai-chatbot') + ' ' + 
                                        (response.data.title) + '</div>');
                                } else {
                                    failCount++;
                                    $('#conversion-log').prepend('<div class="error">' + 
                                        wp.i18n.__('Error converting topic:', 'wp-eazyai-chatbot') + ' ' + 
                                        (response.data.message) + '</div>');
                                }
                            })
                            .fail(function(xhr, status, error) {
                                failCount++;
                                $('#conversion-log').prepend('<div class="error">' + 
                                    wp.i18n.__('AJAX Error:', 'wp-eazyai-chatbot') + ' ' + 
                                    (error) + '</div>');
                            })
                            .always(function() {
                                running--;
                                updateStatus();
                                if (running < maxConcurrent) {
                                    processNext();
                                }
                            });

                            if (running < maxConcurrent && index < selectedTopics.length) {
                                processNext();
                            }
                        }

                        // Start initial batch
                        for (var i = 0; i < Math.min(maxConcurrent, selectedTopics.length); i++) {
                            processNext();
                        }
                    });
                });
                </script>
                <?php } // else ?>
            </div>
        </div>
        <!-- bbpress tab -->
    </div>

    <style>
    .tab-content { padding: 15px 0; }
    </style>

    <script>
    jQuery(document).ready(function($) {
        $('.nav-tab').click(function(e) {
            e.preventDefault();
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            $('.tab-content').hide();
            $('#' + $(this).data('tab')).show();
        });

        // Original process_posts click handler code...
    });
    </script>

    <script>
    jQuery(document).ready(function($) {
        $('#process_posts').click(function() {
            if (!confirm(wp.i18n.__('This will clear existing embeddings. Continue?', 'wp-eazyai-chatbot'))) return;
            
            var button = $(this);
            button.prop('disabled', true);
            $('#progress_bar').show();
            $('#process_log').empty();

            // First, get all posts to process
            $.post(ajaxurl, {
                action: 'wpeazyai_get_posts_count',
                nonce: '<?php echo esc_js(wp_create_nonce("wpeazyai_process")); ?>'
            }, function(response) {
                if (response.success) {
                    processPostsBatch(0, response.data.total);
                }
            });
        });

        function processPostsBatch(offset, total) {
            $.post(ajaxurl, {
                action: 'wpeazyai_process_posts_batch',
                offset: offset,
                nonce: '<?php echo esc_js(wp_create_nonce("wpeazyai_process")); ?>'
            }, function(response) {
                if (response.success) {
                    var progress = Math.round((offset + response.data.processed) / total * 100);
                    jQuery('#progress').val(progress);
                    jQuery('#progress_status').text(progress + '%');
                    jQuery('#process_log').prepend('<div>' + wp.escapeHtml(response.data.message) + '</div>');

                    if (offset + response.data.processed < total) {
                        processPostsBatch(offset + response.data.processed, total);
                    } else {
                        jQuery('#process_posts').prop('disabled', false);
                        jQuery('#process_log').prepend('<div><strong>' + wp.i18n.__('Processing complete!', 'wp-eazyai-chatbot') + '</strong></div>');
                    }
                } else {
                    jQuery('#process_log').prepend('<div class="error" style="color:red">' + wp.escapeHtml(response.data.message) + '</div>');
                    jQuery('#process_posts').prop('disabled', false);
                    
                    // Show error details if available
                    if (response.data.error) {
                        jQuery('#process_log').prepend('<div class="error-details" style="color:#666; font-size:0.9em">' + 
                            wp.escapeHtml(response.data.error) + '</div>');
                    }
                    
                    // Update progress to reflect failure
                    var failedProgress = Math.round((offset) / total * 100);
                    jQuery('#progress').val(failedProgress);
                    jQuery('#progress_status').text(failedProgress + '% ' + 
                        wp.i18n.__('(Error occurred)', 'wp-eazyai-chatbot'));
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                // Handle AJAX failure
                $('#process_log').prepend('<div class="error" style="color:red">AJAX Error: ' + textStatus + '</div>');
                $('#process_posts').prop('disabled', false);
                $('#progress_status').text('Error');
            });
        }

        $('#generate_global_tags').click(function() {
            var postType = $('input[name="global_post_type"]:checked').val();
            
            if (!postType) {
                alert(wp.i18n.__('Please select a post type', 'wp-eazyai-chatbot'));
                return;
            }

            // Get selected options for the post type
            var $content = $(`input[name="global_post_type"][value="${postType}"]`).closest('.accordion-header').next('.accordion-content');
            
            // Build options object
            var options = {
                generate_excerpt: $content.find('input[name="generate_excerpt"]').is(':checked'),
                excerpt_length: $content.find('input[name="excerpt_length"]').val(),
                generate_post_tag: $content.find('input[name="generate_post_tag"]').is(':checked'),
                number_of_post_tag: $content.find('input[name="number_of_post_tag"]').val()
            };

            // Add taxonomy options
            var selectedTaxonomy = $content.find('.taxonomy-selector').val();
            if (selectedTaxonomy) {
                options.taxonomy = selectedTaxonomy;
                options[`generate_${selectedTaxonomy}`] = $content.find(`input[name="generate_${selectedTaxonomy}"]`).is(':checked');
                options[`number_of_${selectedTaxonomy}`] = $content.find(`input[name="number_of_${selectedTaxonomy}"]`).val();
                options[`${selectedTaxonomy}_threshold`] = $content.find(`select[name="${selectedTaxonomy}_threshold"]`).val();
            }

            if (!confirm(wp.i18n.__('This will generate content for all posts of type: ', 'wp-eazyai-chatbot') + postType + '. ' + wp.i18n.__('Continue?', 'wp-eazyai-chatbot'))) {
                return;
            }

            var button = $(this);
            button.prop('disabled', true);
            $('#global_progress_bar').show();
            $('#global_process_log').empty();

            // Get all posts of selected type with taxonomy threshold
            $.post(ajaxurl, {
                action: 'wpeazyai_get_posts',
                post_type: postType,
                taxonomy: selectedTaxonomy,
                threshold: options[`${selectedTaxonomy}_threshold`],
                nonce: '<?php echo esc_js(wp_create_nonce("wpeazyai_process")); ?>'
            }, function(response) {
                if (response.success) {
                    processGlobalBatch(0, response.data.posts, options);
                } else {
                    $('#global_process_log').prepend('<div class="error">' + response.data.message + '</div>');
                    button.prop('disabled', false);
                }
            });

            function processGlobalBatch(index, posts, options) {
                if (index >= posts.length) {
                    jQuery('#generate_global_tags').prop('disabled', false);
                    jQuery('#global_process_log').prepend('<div><strong>' + wp.i18n.__('Processing complete!', 'wp-eazyai-chatbot') + '</strong></div>');
                    if(posts.length == 0) {
                        $('#global_progress_status').text('100%');
                        $('#global_progress').val(100);
                        jQuery('#global_process_log').prepend('<div><strong>' + wp.i18n.__('No posts found to process.', 'wp-eazyai-chatbot') + '</strong></div>');
                    }
                    return;
                }
                
                var progress = posts.length === 0 ? 100 : Math.round((index + 1) / posts.length * 100);
                $('#global_progress').val(progress);
                $('#global_progress_status').text(progress + '%');

                var requests = [];

                // Add excerpt request if enabled
                if (options.generate_excerpt) {
                    requests.push(
                        $.post(ajaxurl, {
                            action: 'wpeazyai_get_excerpt',
                            post_id: posts[index],
                            excerpt_length: options.excerpt_length,
                            nonce: '<?php echo esc_js(wp_create_nonce("wpeazyai_process")); ?>'
                        })
                    );
                }

                // Add tags request if enabled
                if (options.generate_post_tag) {
                    requests.push(
                        $.post(ajaxurl, {
                            action: 'wpeazyai_get_tags',
                            post_id: posts[index],
                            number_of_tags: options.number_of_post_tag,
                            nonce: '<?php echo esc_js(wp_create_nonce("wpeazyai_process")); ?>'
                        })
                    );
                }

                // Add taxonomy request if enabled
                if (options[`generate_${options.taxonomy}`]) {
                    requests.push(
                        $.post(ajaxurl, {
                            action: 'wpeazyai_get_taxonomy_terms',
                            post_id: posts[index],
                            taxonomy: options.taxonomy,
                            number_of_terms: options[`number_of_${options.taxonomy}`],
                            nonce: '<?php echo esc_js(wp_create_nonce("wpeazyai_process")); ?>'
                        })
                    );
                }

                $.when.apply($, requests).done(function() {
                    var results = {};
                    
                    // Process results from all requests
                    for (var i = 0; i < arguments.length; i++) {
                        var response = arguments[i][0];
                        if (response.success) {
                            if (response.data.excerpt) results.excerpt = response.data.excerpt;
                            if (response.data.tags) results.tags = response.data.tags;
                            if (response.data.categories) results.categories = response.data.categories;
                            if (response.data.taxonomy_terms) results.taxonomy_terms = response.data.taxonomy_terms;
                        }
                    }

                    // Update the post with all results
                    $.post(ajaxurl, {
                        action: 'wpeazyai_set_terms',
                        post_id: posts[index],
                        excerpt: results.excerpt || '',
                        tags: results.tags || [],
                        categories: results.categories || [],
                        taxonomy: options.taxonomy,
                        taxonomy_terms: results.taxonomy_terms || [],
                        nonce: '<?php echo esc_js(wp_create_nonce("wpeazyai_process")); ?>'
                    }).done(function(setTermsResponse) {
                        if (setTermsResponse.success) {
                            $('#global_process_log').prepend('<div>Updated post ' + posts[index] + '</div>');
                        } else {
                            $('#global_process_log').prepend('<div class="error">Error updating post ' + posts[index] + '</div>');
                        }
                        processGlobalBatch(index + 1, posts, options);
                    });
                }).fail(function() {
                    $('#global_process_log').prepend('<div class="error">Error processing post ' + posts[index] + '</div>');
                    processGlobalBatch(index + 1, posts, options);
                });
            }
        });
    });
    </script>
    <?php
}