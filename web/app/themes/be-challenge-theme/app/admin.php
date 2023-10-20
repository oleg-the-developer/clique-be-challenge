<?php

declare(strict_types = 1);

namespace App;

/**
 * Theme customizer
 */
add_action('customize_register', function (\WP_Customize_Manager $wp_customize) {
    // Add postMessage support
    $wp_customize->get_setting('blogname')->transport = 'postMessage';
    $wp_customize->selective_refresh->add_partial('blogname', [
        'selector' => '.brand',
        'render_callback' => function () {
            bloginfo('name');
        }
    ]);
});

/**
 * Enqueues some CSS and JS files for ACF styling.
 */
add_action('admin_enqueue_scripts', function () {
    wp_register_style('admin-styles', get_template_directory_uri() . '/admin_assets/css/admin_styles.min.css', [], null);

    wp_enqueue_style('admin-styles');
});

/**
 * Customizer JS
 */
add_action('customize_preview_init', function () {
    wp_enqueue_script('sage/customizer.js', asset_path('scripts/customizer.js'), ['customize-preview'], null, true);
});

/**
 * Since the posts and comments aren't being used, we'll remove it
 */
add_action('admin_menu', function () {
    remove_menu_page('edit.php');
    remove_menu_page('edit-comments.php');
});

add_action('admin_bar_menu', function ($wp_admin_bar) {
    $wp_admin_bar->remove_node('new-post');
}, 999);

add_action('wp_dashboard_setup', function () {
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
}, 999);

/**
 * Uses .env var to hide the admin bar
 */
if (env("HIDE_ADMIN_BAR")) {
    add_filter('show_admin_bar', '__return_false');
}
