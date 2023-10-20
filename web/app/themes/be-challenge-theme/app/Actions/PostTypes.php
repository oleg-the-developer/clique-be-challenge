<?php

namespace App\Actions;

use App\Traits\SagePosts;

class PostTypes
{

    use SagePosts;

    private array $postTypes = [];

    /**
     * Registers the post types
     */
    public function __construct()
    {
        $this->postTypes = [
            // Post Type Name => Post Type Registration Method
            'faqs' => static::faqsPostType(),
        ];

        if (!empty($this->postTypes)) {
            add_action('init', function () {
                collect($this->postTypes)
                    ->each(function ($pt_args, $pt_name) {
                        register_post_type($pt_name, $pt_args);
                    });
            }, 0);
        }
    }

    /**
     * Registers the FAQ Post Type using
     * the postTypeArray trait method
     *
     * @return array
     *@uses postTypeArray()
     *
     */
    protected function faqsPostType()
    :array
    {
        return static::postTypeArray(
            'FAQ',
            'dashicons-format-status',
            __("This post type holds the questions and answers for an FAQ section.", 'sage'),
            'post',
            [
                // Register only what fields we need from WP
                'supports' => ['title', 'editor', 'excerpt', 'revisions']
            ]
        );
    }
}
