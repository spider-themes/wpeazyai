<?php
/**
 * Handles all database WP EazyAI Chatbot
 *
 * @package WP_EazyAI_Chatbot
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_wpeazyai_convert_topic', 'wpeazyai_convert_topic_ajax_handler');
add_action('wp_ajax_nopriv_wpeazyai_convert_topic', 'wpeazyai_convert_topic_ajax_handler');
function wpeazyai_convert_topic_ajax_handler() {
    check_ajax_referer('wpeazyai_convert', 'nonce');
    
    $topic_id = isset($_POST['topic_id']) ? intval($_POST['topic_id']) : 0;
    $post_type = isset($_POST['post_type']) ? sanitize_text_field(wp_unslash($_POST['post_type'])) : '';
    
    if ($topic_id <= 0 || empty($post_type)) {
        wp_send_json_error(['message' => 'Invalid request.']);
    }
    
    $topic = get_post($topic_id);
    if (!$topic) {
        wp_send_json_error(['message' => 'Topic not found.']);
    }
    
    $post_id = wp_insert_post([
        'post_title'   => $topic->post_title,
        'post_content' => $topic->post_content,
        'post_status'  => 'publish',
        'post_type'    => $post_type,
    ]);
    
    if (is_wp_error($post_id)) {
        wp_send_json_error(['message' => $post_id->get_error_message()]);
    }
    
    wp_send_json_success(['title' => $topic->post_title]);
}

add_action('wp_ajax_wpeazyai_set_terms', 'wpeazyai_set_terms_ajax_handler');
add_action('wp_ajax_nopriv_wpeazyai_set_terms', 'wpeazyai_set_terms_ajax_handler');
function wpeazyai_set_terms_ajax_handler() {
    check_ajax_referer('wpeazyai_process', 'nonce');
    
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $excerpt = isset($_POST['excerpt']) ? sanitize_text_field(wp_unslash($_POST['excerpt'])) : '';
    $tags = isset($_POST['tags']) ? array_map('intval', $_POST['tags']) : [];
    $categories = isset($_POST['categories']) ? array_map('intval', $_POST['categories']) : [];
    
    if ($post_id <= 0) {
        wp_send_json_error(['message' => 'Invalid request.']);
    }
    
    $post = get_post($post_id);
    if (!$post) {
        wp_send_json_error(['message' => 'Post not found.']);
    }
    
    // Update post excerpt
    wp_update_post([
        'ID'           => $post_id,
        'post_excerpt' => $excerpt,
    ]);
    
    // Update post tags and categories
    wp_set_post_terms($post_id, $tags, 'post_tag', true);
    wp_set_post_terms($post_id, $categories, 'category', true);
    
    wp_send_json_success();
}


add_action('wp_ajax_wpeazyai_get_posts', 'wpeazyai_get_posts_ajax_handler');
add_action('wp_ajax_nopriv_wpeazyai_get_posts', 'wpeazyai_get_posts_ajax_handler');
function wpeazyai_get_posts_ajax_handler() {
    check_ajax_referer('wpeazyai_process', 'nonce');
    
    $post_type = isset($_POST['post_type']) ? sanitize_text_field(wp_unslash($_POST['post_type'])) : '';
    $taxonomy = isset($_POST['taxonomy']) ? sanitize_text_field(wp_unslash($_POST['taxonomy'])) : '';
    $threshold = isset($_POST['threshold']) ? intval($_POST['threshold']) : 0;
    
    if (empty($post_type)) {
        wp_send_json_error(['message' => 'Invalid request.']);
    }
    
    global $wpdb;
    
    // Base query for published posts of specified type
    $query = $wpdb->prepare("
        SELECT p.ID 
        FROM {$wpdb->posts} p
        WHERE p.post_type = %s 
        AND p.post_status = 'publish'", 
        $post_type
    );
    
    // Add taxonomy threshold condition if taxonomy is specified
    if (!empty($taxonomy)) {
        $query = $wpdb->prepare("
            SELECT p.ID
            FROM {$wpdb->posts} p
            LEFT JOIN (
                SELECT object_id, COUNT(*) as term_count
                FROM {$wpdb->term_relationships} tr
                JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                WHERE tt.taxonomy = %s
                GROUP BY object_id
            ) terms ON p.ID = terms.object_id
            WHERE p.post_type = %s 
            AND p.post_status = 'publish'
            AND (terms.term_count IS NULL OR terms.term_count <= %d)",
            $taxonomy,
            $post_type,
            $threshold
        );
    }
    
    $posts = $wpdb->get_col($query);
    
    wp_send_json_success(['posts' => $posts]);
}

add_action('wp_ajax_wpeazyai_get_posts_count', 'wpeazyai_get_posts_count');
function wpeazyai_get_posts_count() {
    check_ajax_referer('wpeazyai_process', 'nonce');
    
    $post_types = get_option('wpeazyai_selected_post_types', []);
    $count = 0;
    foreach ($post_types as $post_type) {
        $count += wp_count_posts($post_type)->publish;
    }
    
    wp_send_json_success(['total' => $count]);
}

add_action('wp_ajax_wpeazyai_process_posts_batch', 'wpeazyai_process_posts_batch');
function wpeazyai_process_posts_batch() {
    check_ajax_referer('wpeazyai_process', 'nonce');
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'wpeazyai_embeddings';
    $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
    $batch_size = 5; // Process 5 posts per batch
    
    $api_key = get_option('wpeazyai_api_key');
    $post_types = get_option('wpeazyai_selected_post_types', []);
    
    if ($offset === 0) {
        $wpdb->query($wpdb->prepare("TRUNCATE TABLE %i", $table_name));
    }
    
    $posts = get_posts([
        'post_type' => $post_types,
        'posts_per_page' => $batch_size,
        'offset' => $offset,
        'post_status' => 'publish',
    ]);
    
    $processed = 0;
    foreach ($posts as $post) {
        $content = wp_strip_all_tags($post->post_content);
        $chunks = str_split($content, 800);
        
        foreach ($chunks as $chunk_index => $chunk) {
            try {
                $embedding = wpeazyai_generate_embedding($chunk, $api_key);
                if ($embedding) {
                    $wpdb->insert($table_name, [
                        'post_id' => $post->ID,
                        'chunk_index' => $chunk_index, 
                        'chunk_text' => $chunk,
                        'embedding' => wp_json_encode($embedding),
                    ]);
                }
            } catch (Exception $e) {
                wp_send_json_error([
                    'message' => sprintf('Error processing post %d: %s', $post->ID, $e->getMessage()),
                    'processed' => $processed
                ]);
                return;
            }
        }
        $processed++;
    }
    
    wp_send_json_success([
        'processed' => $processed,
        'message' => sprintf('Processed %d posts (offset: %d)', $processed, $offset)
    ]);
}