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