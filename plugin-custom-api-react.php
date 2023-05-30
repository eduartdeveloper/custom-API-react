<?php
/*
Plugin Name: Custom API Plugin for React
Version: 1.0.0
Description: Provides a custom REST API for managing posts.
Author: Jose Eduardo Rendón Valencia
*/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Include the necessary files.
require_once plugin_dir_path(__FILE__) . 'class-custom-api-plugin.php';

// Initialize the plugin.
function custom_api_plugin_init() {
    $custom_api_plugin = new Custom_API_Plugin();
    $custom_api_plugin->init();
}
add_action('plugins_loaded', 'custom_api_plugin_init');
