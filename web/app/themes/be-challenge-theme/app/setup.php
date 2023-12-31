<?php

declare(strict_types = 1);

namespace App;

use Roots\Sage\Container;
use Roots\Sage\Assets\JsonManifest;
use Roots\Sage\Template\Blade;
use Roots\Sage\Template\BladeProvider;

/**
 * Theme assets
 */
add_action('wp_enqueue_scripts', function () {
    wp_register_style('sage/main.css', asset_path('styles/main.css'), false, null);
    // Separated Vue scripts to its own file
    wp_register_script('sage/manifest.js', asset_path('scripts/manifest.js'), ['jquery'], null, true);
    wp_register_script('sage/vendor.js', asset_path('scripts/vendor.js'), ['jquery'], null, true);
    wp_register_script('sage/main.js', asset_path('scripts/main.js'), ['sage/vendor.js'], null, true);

    // Enqueuing of Scripts and Styles
    wp_enqueue_script('sage/manifest.js');
    wp_enqueue_script('sage/vendor.js');
    wp_enqueue_script('sage/main.js');

    if (is_single() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    wp_enqueue_style('sage/main.css');
}, 100);

/**
 * Theme setup
 */
add_action('after_setup_theme', function () {
    /**
     * Enable features from Soil when plugin is activated
     *
     * @link https://roots.io/plugins/soil/
     */
    add_theme_support('soil-clean-up');
    add_theme_support('soil-jquery-cdn');
    add_theme_support('soil-nav-walker');
    add_theme_support('soil-nice-search');
    add_theme_support('soil-relative-urls');

    /**
     * Enable plugins to manage the document title
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
     */
    add_theme_support('title-tag');

    /**
     * Register navigation menus
     *
     * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
     */
    register_nav_menus(
        [
            'primary_navigation' => __('Primary Navigation', 'sage'),
            'footer_navigation' => __('Footer Navigation', 'sage'),
        ]
    );

    /**
     * Enable post thumbnails
     *
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support('post-thumbnails');

    /**
     * Enable HTML5 markup support
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
     */
    add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form']);

    /**
     * Enable selective refresh for widgets in customizer
     *
     * @link https://developer.wordpress.org/themes/advanced-topics/customizer-api/#theme-support-in-sidebars
     */
    add_theme_support('customize-selective-refresh-widgets');

    /**
     * Use main stylesheet for visual editor
     *
     * @see resources/assets/styles/layouts/_tinymce.scss
     */
    add_editor_style(asset_path('styles/main.css'));


    /**
     * Updates and sets the WordPress image sizes
     */
    $options = [
        'thumbnail_size_w'    => 300,
        'thumbnail_size_h'    => 0,
        'thumbnail_crop'      => 0,
        'medium_size_w'       => 768,
        'medium_size_h'       => 0,
        'medium_large_size_w' => 1024,
        'medium_large_size_h' => 0,
        'large_size_w'        => 1280,
        'large_size_h'        => 0,
    ];

    collect($options)
        ->each(function ($opt_val, $opt_key) {
            update_option($opt_key, $opt_val, true);
        });

    /**
     * Adds a 1920w banner image size.
     * Change the width if supporting browser size higher than 1920
     */
    add_image_size('banner', 1920, 0, false);
}, 20);

/**
 * Register sidebars
 */
add_action('widgets_init', function () {
    $config = [
        'before_widget' => '<section class="widget %1$s %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ];
    register_sidebar(
        [
            'name' => __('Primary', 'sage'),
            'id'   => 'sidebar-primary',
        ] + $config
    );
    register_sidebar(
        [
            'name' => __('Footer', 'sage'),
            'id'   => 'sidebar-footer',
        ] + $config
    );
});

/**
 * Updates the `$post` variable on each iteration of the loop.
 * Note: updated value is only available for subsequently loaded views, such as partials
 */
add_action('the_post', function ($post) {
    sage('blade')->share('post', $post);
});

/**
 * Setup Sage options
 */
add_action('after_setup_theme', function () {
    /**
     * Add JsonManifest to Sage container
     */
    sage()->singleton('sage.assets', function () {
        return new JsonManifest(config('assets.manifest'), config('assets.uri'));
    });

    /**
     * Add Blade to Sage container
     */
    sage()->singleton('sage.blade', function (Container $app) {
        $cachePath = config('view.compiled');
        if (!file_exists($cachePath)) {
            wp_mkdir_p($cachePath);
        }
        (new BladeProvider($app))->register();
        return new Blade($app['view']);
    });

    /**
     * Create @asset() Blade directive
     */
    sage('blade')->compiler()->directive('asset', function ($asset) {
        return "<?= " . __NAMESPACE__ . "\\asset_path({$asset}); ?>";
    });

    /**
     * Create @vardump() Blade directive
     */
    sage('blade')->compiler()->directive('vardump', function ($variable) {
        return "<pre><?php var_dump($variable); ?></pre>";
    });
});

/**
 * Disable Emoji's
 */
add_action('init', function () {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

    /**
     * Filter out the tinymce emoji plugin.
     */
    add_filter('tiny_mce_plugins', function ($plugins) {
        if (is_array($plugins)) {
            return array_diff($plugins, ['wpemoji']);
        } else {
            return [];
        }
    });
});
