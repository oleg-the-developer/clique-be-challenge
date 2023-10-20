<?php

declare(strict_types=1);

namespace App\Helper;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Helper
{

    /**
     * Returns the excerpt if it exists or creates the excerpt
     * based on the $post_override or $post->post_content
     *
     * @param  mixed  $post_override  Pass a field to override the global $post
     * @param  int    $word_max       Pass an integer to override the word count
     *
     * @return null|string Raw stripped content
     */
    public static function generateExcept(mixed $post_override = null, int $word_max = 25)
    :?string
    {
        if ($post_override) {
            if (is_int($post_override)) {
                $excerpt = get_the_content('', true, $post_override);
            } elseif (is_object($post_override)) {
                $excerpt = $post_override->post_excerpt ?: $post_override->post_content ?: '';
            } elseif (is_string($post_override)) {
                $excerpt = $post_override;
            } else {
                $excerpt = '';
            }
        } else {
            $post = get_post();

            if ($post && Str::length($post->post_excerpt)) {
                $excerpt = $post->post_excerpt;
            } elseif ($post && Str::length($post->post_content)) {
                $excerpt = $post->post_content;
            } else {
                $excerpt = '';
            }
        }

        if ($excerpt) {
            $excerpt = strip_shortcodes($excerpt);
            $excerpt = strip_tags($excerpt);
            $excerpt = wp_trim_words($excerpt, $word_max, '');

            return html_entity_decode($excerpt);
        }

        return null;
    }

    /**
     * Method that filters internal array items
     * that would be multidimensional
     *
     * @param  mixed  $data  The data that needs to be parsed
     *
     * @return Collection|mixed
     */
    public static function filterArray(mixed $data)
    :mixed
    {
        if (!is_object($data) && !is_array($data)) {
            return $data;
        }

        return collect($data)
            ->filter(function ($data) {
                return is_object($data) || is_array($data)
                    ? static::filterArray($data)
                            ->isNotEmpty()
                    : $data;
            });
    }
}
