<?php

declare(strict_types=1);

namespace App\Controllers;

use Illuminate\Support\Collection;
use Sober\Controller\Controller;
use WP_Query;

class FrontPage extends Controller {

    /**
     * ACF Field Values
     *
     * @var string[]
     */
    protected $acf = [
        'hero_title',
        'hero_content',
        'why_title',
        'why_subtitle',
        'why_content',
        'why_image',
        'about_title',
        'about_content',
        'about_image',
        'faq_title',
        'faq_subtitle',
        'faq_content',
        'faq_repeater_title',
        'expand_accordion',
        'collapse_accordion',
    ];

    /**
     * Fetch all posts of the "FAQ" custom post type.
     *
     * @return \Illuminate\Support\Collection
     */
    public function faq() {
        $args = [
            'post_type' => 'faqs',
            'posts_per_page' => -1,
        ];

        $query = new WP_Query($args);

        return new Collection($query->posts);
    }
}
