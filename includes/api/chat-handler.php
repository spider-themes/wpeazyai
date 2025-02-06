<?php


/**
 * Handle OPENAI Chats for WP EazyAI Chatbot
 *
 * @package WP_EazyAI_Chatbot
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}


/**
 * 5. AJAX handler to generate chatbot responses using top matching chunks.
 */
add_action('wp_ajax_wpeazyai_chatbot_response', 'wpeazyai_chatbot_ajax_handler');
add_action('wp_ajax_nopriv_wpeazyai_chatbot_response', 'wpeazyai_chatbot_ajax_handler');
function wpeazyai_chatbot_ajax_handler() {
    $user_input = isset($_POST['message']) ? sanitize_text_field($_POST['message']) : '';
    $api_key    = get_option('wpeazyai_api_key');
    if (empty($user_input) || empty($api_key)) {
        wp_send_json_error(['message' => 'Invalid request.']);
    }

    // 5a. Generate embedding for user query
    $query_embedding = wpeazyai_generate_embedding($user_input, $api_key);
    if (!$query_embedding) {
        wp_send_json_error(['message' => 'Failed to generate query embedding.']);
    }

    // 5b. Retrieve all embeddings
    global $wpdb;
    $table_name = $wpdb->prefix . 'wpeazyai_embeddings';
    $results = $wpdb->get_results("SELECT id, post_id, chunk_index, chunk_text, embedding FROM $table_name");

    // 5c. Compute cosine similarity (naive approach)
    $similarities = [];
    foreach ($results as $row) {
        $chunk_embedding = json_decode($row->embedding, true);
        $similarity = wpeazyai_cosine_similarity($query_embedding, $chunk_embedding);
        $similarities[] = [
            'row'        => $row,
            'similarity' => $similarity,
        ];
    }

    // Sort by similarity desc, pick top 3
    usort($similarities, function ($a, $b) {
        return $b['similarity'] <=> $a['similarity'];
    });
    $top_matches = array_slice($similarities, 0, 3);

    // Build context
    $context = "";
    $references = [];
    foreach ($top_matches as $match) {
        $row = $match['row'];
        $references[] = [
            'post_id'    => $row->post_id,
            'chunk_idx'  => $row->chunk_index,
            'similarity' => $match['similarity'],
            'link'       => get_permalink($row->post_id),
            'title'      => get_the_title($row->post_id),
        ];
        $context .= "Snippet (similarity=" . round($match['similarity'], 3) . "):\n";
        $context .= $row->chunk_text . "\n\n";
    }

    // 5d. Construct system message and user message with context
    $system_message = "You are an AI support assistant. Your responses should be:
- Based only on the provided context
- Professional and helpful
- Concise (2-3 sentences max)
- Clear about uncertainty when context is insufficient";

    $user_message = "### CONTEXT START ###\n$context\n### CONTEXT END ###\n\n"
                 . "Based on the above context only, please answer this question: $user_input";

    // 5e. Call OpenAI's Chat Completion API
    $chat_endpoint = 'https://api.openai.com/v1/chat/completions';
    $chat_body = [
        'model'       => 'gpt-3.5-turbo',
        'messages'    => [
            ['role' => 'system', 'content' => $system_message],
            ['role' => 'user', 'content' => $user_message]
        ],
        'max_tokens'  => 300,
        'temperature' => 0.7
    ];

    $chat_response = wp_remote_post($chat_endpoint, [
        'headers' => [
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        ],
        'body'    => json_encode($chat_body),
        'timeout' => 30,
    ]);

    if (is_wp_error($chat_response)) {
        wp_send_json_error(['message' => 'Error connecting to OpenAI.']);
    }

    $chat_data = json_decode(wp_remote_retrieve_body($chat_response), true);
    //print_r($chat_data);
    if (!isset($chat_data['choices'][0]['message']['content'])) {
        wp_send_json_error(['message' => 'Invalid response from OpenAI.','error'=>json_encode($chat_data)]);
    }

    $final_answer = trim($chat_data['choices'][0]['message']['content']);

    // Return success response
    wp_send_json_success([
        'answer'     => $final_answer,
        'references' => $references
    ]);
}