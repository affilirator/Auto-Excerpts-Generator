<?php
/**
 * Plugin Name: Auto Excerpts Generator
 * Description: Automatically adds excerpts to posts and pages without excerpts using OpenAI API.
 * Version: 1.20
 * Author: Patrick Mahinge
 * Author URI: https://mahinge.com/portfolio/
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Add admin menu for settings
function aeg_add_admin_menu() {
    add_options_page('Auto Excerpt Generator', 'Auto Excerpt Generator', 'manage_options', 'auto_excerpt_generator', 'aeg_options_page');
}
add_action('admin_menu', 'aeg_add_admin_menu');

// Initialize plugin settings
function aeg_settings_init() {
    register_setting('pluginPage', 'aeg_settings');
    add_settings_section('aeg_pluginPage_section', __('Your section description', 'wordpress'), null, 'pluginPage');
    add_settings_field('aeg_api_key', __('OpenAI API Key', 'wordpress'), 'aeg_api_key_render', 'pluginPage', 'aeg_pluginPage_section');
}
add_action('admin_init', 'aeg_settings_init');

// Render API key input field
function aeg_api_key_render() {
    $options = get_option('aeg_settings');
    ?>
    <input type='text' name='aeg_settings[aeg_api_key]' value='<?php echo esc_attr($options['aeg_api_key'] ?? ''); ?>'>
    <?php
}

// Options page HTML
function aeg_options_page() {
    ?>
    <form action='options.php' method='post'>
        <h2>Auto Excerpt Generator</h2>
        <?php
        settings_fields('pluginPage');
        do_settings_sections('pluginPage');
        submit_button();
        ?>
    </form>
    <form method="post">
        <input type="hidden" name="aeg_generate_excerpts" value="1">
        <?php submit_button(__('Generate Excerpts for Published Posts', 'wordpress'), 'secondary'); ?>
    </form>
    <?php
}

// Handle generate excerpts request
function aeg_handle_generate_excerpts_request() {
    $api_key = get_option('aeg_settings')['aeg_api_key'] ?? '';

    if (!current_user_can('manage_options') || empty($_POST['aeg_generate_excerpts']) || empty($api_key)) {
        return;
    }

    // Fetch published posts without excerpts
    $args = [
        'post_type' => 'any',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => [
            [
                'key' => 'aeg_excerpt_generated',
                'compare' => 'NOT EXISTS',
            ],
        ],
    ];

    $posts = get_posts($args);

    foreach ($posts as $post) {
        aeg_generate_excerpt($post->ID);
    }

    // Provide feedback to the admin
    add_action('admin_notices', function () {
        echo '<div class="notice notice-success"><p>' . __('Excerpts have been generated for published posts.', 'wordpress') . '</p></div>';
    });
}
add_action('admin_init', 'aeg_handle_generate_excerpts_request');

// Generate excerpt for a given post ID
function aeg_generate_excerpt($post_id) {
    if (wp_is_post_revision($post_id) || get_post_field('post_excerpt', $post_id)) {
        return;
    }

    $post_content = get_post_field('post_content', $post_id);
    $api_key = get_option('aeg_settings')['aeg_api_key'];

    if (empty($api_key)) {
        return;
    }

    $response = wp_remote_post('https://api.openai.com/v1/chat/completions', [
        'headers' => [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json',
        ],
        'body' => json_encode([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => "Using an empathetic tone,generate a short summary of no more than 3 sentences for this content: $post_content"]
            ],
            'max_tokens' => 50,
        ]),
    ]);

    if (is_wp_error($response)) {
        error_log('API Request Error: ' . $response->get_error_message());
        return;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (isset($data['choices'][0]['message']['content'])) {
        $excerpt = sanitize_text_field($data['choices'][0]['message']['content']);
        remove_action('save_post', 'aeg_generate_excerpt');
        wp_update_post([
            'ID' => $post_id,
            'post_excerpt' => $excerpt,
        ]);
        add_action('save_post', 'aeg_generate_excerpt');

        add_post_meta($post_id, 'aeg_excerpt_generated', true);
    }
}

// Generate excerpts for older posts without excerpts on admin_init
function aeg_generate_excerpts_for_old_posts() {
    $api_key = get_option('aeg_settings')['aeg_api_key'] ?? '';

    if (empty($api_key)) {
        return;
    }

    $args = [
        'post_type' => 'any',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => [
            [
                'key' => 'aeg_excerpt_generated',
                'compare' => 'NOT EXISTS',
            ],
        ],
    ];

    $posts = get_posts($args);

    foreach ($posts as $post) {
        aeg_generate_excerpt($post->ID);
    }
}
add_action('admin_init', 'aeg_generate_excerpts_for_old_posts');
