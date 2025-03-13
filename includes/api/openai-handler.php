<?php
/**
 * Handles all OpenAI for WP EazyAI Chatbot
 *
 * @package WP_EazyAI_Chatbot
 * @since   1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 2. Utility function to generate embeddings from OpenAI.
 */
function wpeazyai_generate_embedding( $text, $api_key ) {
	// Trim and clean up text
	$text        = trim( $text );
	$embed_model = get_option( 'wpeazyai_embedding_model', 'text-embedding-ada-002' );
	$endpoint    = 'https://api.openai.com/v1/embeddings';
	$body        = [
		'model' => $embed_model, // popular embeddings model
		'input' => $text,
	];

	$response = wp_remote_post( $endpoint, [
		'headers' => [
			'Content-Type'  => 'application/json',
			'Authorization' => 'Bearer ' . $api_key,
		],
		'body'    => json_encode( $body ),
		'timeout' => 30,
	] );

	if ( is_wp_error( $response ) ) {
		return null;
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( isset( $data['error'] ) ) {
		$error_msg  = $data['error']['message'] ?? 'Unknown error';
		$error_type = $data['error']['type'] ?? 'unknown';

		if ( strpos( $error_msg, 'Rate limit' ) !== false ) {
			throw new Exception( 'Rate limit exceeded. Please try again later.' );
		}

		if ( strpos( $error_msg, 'Invalid API key' ) !== false ) {
			throw new Exception( 'Invalid API key. Please check your OpenAI API settings.' );
		}

		throw new Exception( 'OpenAI API Error: ' . esc_html( $error_msg ) );
	}

	if ( isset( $data['data'][0]['embedding'] ) ) {
		return $data['data'][0]['embedding'];
	}

	throw new Exception( 'Failed to generate embedding. Unexpected response format.' );
}

/** another openai ajax request which generated excerpt from post content */
add_action( 'wp_ajax_wpeazyai_get_excerpt', 'wpeazyai_get_excerpt_ajax_handler' );
add_action( 'wp_ajax_nopriv_wpeazyai_get_excerpt', 'wpeazyai_get_excerpt_ajax_handler' );
function wpeazyai_get_excerpt_ajax_handler() {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wpeazyai_process' ) ) {
		wp_send_json_error( [ 'message' => 'Invalid security token.' ] );
	}
	$post_id        = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
	$excerpt_length = isset( $_POST['excerpt_length'] ) ? intval( $_POST['excerpt_length'] ) : 50;
	if ( $post_id <= 0 ) {
		wp_send_json_error( [ 'message' => 'Invalid request.' ] );
	}

	$post         = get_post( $post_id );
	$post_content = $post->post_content;
	if ( ! $post ) {
		wp_send_json_error( [ 'message' => 'Post not found.' ] );
	}

	$api_key = get_option( 'wpeazyai_api_key' );
	if ( empty( $api_key ) ) {
		wp_send_json_error( [ 'message' => 'API key not found.' ] );
	}
	$model         = get_option( 'wpeazyai_model', 'gpt-3.5-turbo' );
	$chat_endpoint = 'https://api.openai.com/v1/chat/completions';
	$chat_body     = [
		'model'       => $model,
		'messages'    => [
			[
				'role'    => 'system',
				'content' => "Generate a concise excerpt from the following content in approximately {$excerpt_length} words."
			],
			[
				'role'    => 'user',
				'content' => wp_strip_all_tags( $post_content )
			]
		],
		'max_tokens'  => 150,
		'temperature' => 0.7
	];

	$chat_response = wp_remote_post( $chat_endpoint, [
		'headers' => [
			'Content-Type'  => 'application/json',
			'Authorization' => 'Bearer ' . $api_key,
		],
		'body'    => json_encode( $chat_body ),
		'timeout' => 30,
	] );

	if ( is_wp_error( $chat_response ) ) {
		wp_send_json_error( [ 'message' => 'Error connecting to OpenAI.' ] );
	}

	$chat_data = json_decode( wp_remote_retrieve_body( $chat_response ), true );
	if ( ! isset( $chat_data['choices'][0]['message']['content'] ) ) {
		wp_send_json_error( [ 'message' => 'Invalid response from OpenAI.' ] );
	}

	$excerpt = trim( $chat_data['choices'][0]['message']['content'] );

	if ( empty( $excerpt ) ) {
		$excerpt = wp_trim_words( $post->post_content, 50 );
	}

	wp_send_json_success( [ 'excerpt' => $excerpt ] );
}

add_action( 'wp_ajax_wpeazyai_get_taxonomy_terms', 'wpeazyai_get_taxonomy_terms_ajax_handler' );
add_action( 'wp_ajax_nopriv_wpeazyai_get_taxonomy_terms', 'wpeazyai_get_taxonomy_terms_ajax_handler' );
function wpeazyai_get_taxonomy_terms_ajax_handler() {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wpeazyai_process' ) ) {
		wp_send_json_error( [ 'message' => 'Invalid security token.' ] );
	}
	$post_id         = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
	$taxonomy        = isset( $_POST['taxonomy'] ) ? sanitize_key( $_POST['taxonomy'] ) : '';
	$number_of_terms = isset( $_POST['number_of_terms'] ) ? intval( $_POST['number_of_terms'] ) : 5;
	$model           = get_option( 'wpeazyai_model', 'gpt-3.5-turbo' );
	if ( $post_id <= 0 || empty( $taxonomy ) ) {
		wp_send_json_error( [ 'message' => 'Invalid request parameters.' ] );
	}

	$post = get_post( $post_id );
	if ( ! $post ) {
		wp_send_json_error( [ 'message' => 'Post not found.' ] );
	}

	$api_key = get_option( 'wpeazyai_api_key' );
	if ( empty( $api_key ) ) {
		wp_send_json_error( [ 'message' => 'API key not found.' ] );
	}

	$chat_endpoint = 'https://api.openai.com/v1/chat/completions';
	$chat_body     = [
		'model'       => $model,
		'messages'    => [
			[
				'role'    => 'system',
				'content' => "Generate only $number_of_terms comma-separated {$taxonomy} terms based on the following content. Keep terms simple and relevant."
			],
			[
				'role'    => 'user',
				'content' => wp_strip_all_tags( $post->post_content )
			]
		],
		'max_tokens'  => 150,
		'temperature' => 0.7
	];

	$chat_response = wp_remote_post( $chat_endpoint, [
		'headers' => [
			'Content-Type'  => 'application/json',
			'Authorization' => 'Bearer ' . $api_key,
		],
		'body'    => json_encode( $chat_body ),
		'timeout' => 30,
	] );

	if ( is_wp_error( $chat_response ) ) {
		wp_send_json_error( [ 'message' => 'Error connecting to OpenAI.' ] );
	}

	$chat_data = json_decode( wp_remote_retrieve_body( $chat_response ), true );
	if ( ! isset( $chat_data['choices'][0]['message']['content'] ) ) {
		wp_send_json_error( [ 'message' => 'Invalid response from OpenAI.' ] );
	}

	$terms = explode( ',', $chat_data['choices'][0]['message']['content'] );
	$terms = array_map( 'trim', $terms );
	$terms = array_filter( $terms );

	$term_ids       = [];
	$existing_terms = wp_get_post_terms( $post_id, $taxonomy, [ 'fields' => 'ids' ] );

	if ( ! is_wp_error( $existing_terms ) ) {
		$term_ids = $existing_terms;
	}

	foreach ( $terms as $term ) {
		$term_check = term_exists( $term, $taxonomy );
		if ( ! $term_check ) {
			$term_check = wp_insert_term( $term, $taxonomy );
		}
		if ( ! is_wp_error( $term_check ) ) {
			$term_ids[] = is_array( $term_check ) ? $term_check['term_id'] : $term_check;
		}
	}

	wp_send_json_success( [
		'taxonomy_terms' => $term_ids,
		'content'        => $chat_data['choices'][0]['message']['content']
	] );
}


/** write another openai ajax request which generated tags and categories from post content */
add_action( 'wp_ajax_wpeazyai_get_tags', 'wpeazyai_get_tags_ajax_handler' );
add_action( 'wp_ajax_nopriv_wpeazyai_get_tags', 'wpeazyai_get_tags_ajax_handler' );
function wpeazyai_get_tags_ajax_handler() {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wpeazyai_process' ) ) {
		wp_send_json_error( [ 'message' => 'Invalid security token.' ] );
	}
	$post_id        = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
	$number_of_tags = isset( $_POST['number_of_tags'] ) ? intval( $_POST['number_of_tags'] ) : 5;
	if ( $post_id <= 0 ) {
		wp_send_json_error( [ 'message' => 'Invalid request.' ] );
	}

	$post         = get_post( $post_id );
	$post_content = $post->post_content;
	if ( ! $post ) {
		wp_send_json_error( [ 'message' => 'Post not found.' ] );
	}

	$api_key = get_option( 'wpeazyai_api_key' );
	if ( empty( $api_key ) ) {
		wp_send_json_error( [ 'message' => 'API key not found.' ] );
	}
	$model         = get_option( 'wpeazyai_model', 'gpt-3.5-turbo' );
	$chat_endpoint = 'https://api.openai.com/v1/chat/completions';
	$chat_body     = [
		'model'       => $model,
		'messages'    => [
			[
				'role'    => 'system',
				'content' => "Return only comma seperated $number_of_tags categories based on the following content."
			],
			[
				'role'    => 'user',
				'content' => 'Return only comma seperated ' . $number_of_tags . ' categories based on the following content: '
				             . wp_strip_all_tags( $post_content )
			]
		],
		'max_tokens'  => 150,
		'temperature' => 0.7
	];

	$chat_response = wp_remote_post( $chat_endpoint, [
		'headers' => [
			'Content-Type'  => 'application/json',
			'Authorization' => 'Bearer ' . $api_key,
		],
		'body'    => json_encode( $chat_body ),
		'timeout' => 30,
	] );

	if ( is_wp_error( $chat_response ) ) {
		wp_send_json_error( [ 'message' => 'Error connecting to OpenAI.' ] );
	}

	$chat_data = json_decode( wp_remote_retrieve_body( $chat_response ), true );
	if ( ! isset( $chat_data['choices'][0]['message']['content'] ) ) {
		wp_send_json_error( [ 'message' => 'Invalid response from OpenAI.' ] );
	}

	$tags = explode( ',', $chat_data['choices'][0]['message']['content'] );
	$tags = array_map( 'trim', $tags );
	$tags = array_filter( $tags );
	// insert them into post tags and categories and return ids
	$tag_ids = [];
	$cat_ids = [];
	// Get existing tags and categories
	$existing_tags = wp_get_post_terms( $post_id, 'post_tag', array( 'fields' => 'ids' ) );
	$existing_cats = wp_get_post_terms( $post_id, 'category', array( 'fields' => 'ids' ) );

	// Append existing tags and categories to arrays
	if ( ! is_wp_error( $existing_tags ) ) {
		$tag_ids = array_merge( $tag_ids, $existing_tags );
	}
	if ( ! is_wp_error( $existing_cats ) ) {
		$cat_ids = array_merge( $cat_ids, $existing_cats );
	}
	foreach ( $tags as $tag ) {
		// Check if tag length is too long (e.g., more than 50 characters)
		if ( strlen( $tag ) > 50 ) {
			wp_send_json_error( [ 'message' => 'Generated category/tag is too long: ' . esc_html( $tag ) ] );

			return;
		}
		$term = term_exists( $tag, 'post_tag' );
		$cat  = term_exists( $tag, 'category' );
		if ( ! $term ) {
			$term = wp_insert_term( $tag, 'post_tag' );
		}
		if ( ! $cat ) {
			$cat = wp_insert_term( $tag, 'category' );
		}
		$tag_ids[] = $term['term_id'];
		$cat_ids[] = $cat['term_id'];
	}
	// try to associate the post with the generated tags and categories
	//wp_set_post_terms($post_id, $tag_ids, 'post_tag', true);
	//wp_set_post_terms($post_id, $cat_ids, 'category', true);
	wp_send_json_success( [
		'message'    => 'Tags and categories generated successfully',
		'tags'       => $tag_ids,
		'categories' => $cat_ids,
		'content'    => $chat_data['choices'][0]['message']['content']
	] );
}

/**
 * Call a custom AI function to generate a comprehensive post.
 * This function should analyze the full conversation and return an associative array:
 * [
 *    'title'      => (string) Generated title,
 *    'content'    => (string) Generated content,
 *    'excerpt'    => (string) Generated excerpt,
 *    'taxonomies' => (array)  Taxonomies e.g. ['category' => [...], 'post_tag' => [...]]
 * ]
 * Users can control the tone, excerpt length, and taxonomy limit via POST vars.
 */
function wpeazyai_generate_post( $title, $conversation, $tone, $excerpt_length, $taxonomy_limit ) {
	$api_key = get_option( 'wpeazyai_api_key' );
	if ( empty( $api_key ) ) {
		return null;
	}

	$model         = get_option( 'wpeazyai_model', 'gpt-3.5-turbo' );
	$chat_endpoint = 'https://api.openai.com/v1/chat/completions';

	$system_message
		= "You are an expert content generator. Your task is to create a comprehensive post based on a provided conversation. Use a {$tone} tone. The output must include a title, content body, a concise excerpt of roughly {$excerpt_length} words, and relevant taxonomies. For taxonomies, generate two lists: one for categories and one for post tags, each limited to {$taxonomy_limit} simple and relevant items. Respond in valid JSON format with these keys: 'title', 'content', 'excerpt', and 'taxonomies'. The 'taxonomies' value should be an object with keys 'category' and 'post_tag' containing arrays of terms.";
	$user_message
		= "Generate a blog post or documentation based on the following forum topic {$title}\n\n. The topic contains a discussion about an issue—rewrite it as a structured post that clearly presents the problem and its solution for other users: Topic Title: {$title}\n\nConversation:\n{$conversation}\n\nPlease generate the complete post accordingly.";

	$chat_body = [
		'model'       => $model,
		'messages'    => [
			[
				'role'    => 'system',
				'content' => $system_message
			],
			[
				'role'    => 'user',
				'content' => $user_message
			]
		],
		'max_tokens'  => 600,
		'temperature' => 0.7,
	];

	$response = wp_remote_post( $chat_endpoint, [
		'headers' => [
			'Content-Type'  => 'application/json',
			'Authorization' => 'Bearer ' . $api_key,
		],
		'body'    => json_encode( $chat_body ),
		'timeout' => 30,
	] );

	if ( is_wp_error( $response ) ) {
		return null;
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( ! isset( $data['choices'][0]['message']['content'] ) ) {
		return null;
	}

	$generated      = trim( $data['choices'][0]['message']['content'] );
	$generated_post = json_decode( $generated, true );

	if ( json_last_error() !== JSON_ERROR_NONE || ! isset( $generated_post['title'], $generated_post['content'], $generated_post['excerpt'] ) ) {
		// Fallback if the response is not in valid JSON format.
		$generated_post = [
			'title'      => $title,
			'content'    => $conversation,
			'excerpt'    => wp_trim_words( $conversation, $excerpt_length, '...' ),
			'taxonomies' => [
				'category' => [],
				'post_tag' => []
			]
		];
	}

	return $generated_post;
}