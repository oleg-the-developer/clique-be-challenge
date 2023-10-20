<?php

declare(strict_types = 1);

namespace App\Helper;

use Illuminate\Support\Str;

class ImageHelper
{

    /**
     * Returns an HTML 5 formatted picture tag with source sets for webp and the original image
     * file format. Also supports adding in captions and caption text.
     *
     * @param  int    $src_id      The Image ID
     * @param  array{alt:String, caption:Bool|String, figcaption_class:String, figure_class:String, id:String, image_class:String, pic_class: String, property:String, role:String, size:String}
     *  $img_attrs Additional parameters for generating the proper image output
     *             alt: The image alt tag override
     *             caption: The image caption or if you want it to generate one from the media library pass the true boolean.
     *             figcaption_class: The class for the figure caption tag
     *             figure_class: The class for the figure tag
     *             id: The image ID attribute tag
     *             image_class: The class for the image tag
     *             pic_class: The image for the picture tag
     *             property: The image property type, defaults to image
     *             role: The image role attribute
     *             size: The image return size, defaults to large
     * @param  array  $data_attrs  Pass an array of data attributes for use with JS
     * @param  array  $aria_attrs  Any aria attributes needed for the image
     *
     * @return string Returns a formatted picture HTML element with source and img tags
     */
    public static function imgSrcSet(int $src_id, array $img_attrs = [], array $data_attrs = [], array $aria_attrs = [])
    :string
    {
        $get_img_data = collect(self::imgSrcSetArr($src_id, $img_attrs));
        $img_attrs = collect($img_attrs);

        if ($get_img_data->isEmpty() && is_user_logged_in()) {
            return sprintf(
                "<p><b>%s <code>%s</code>:</b> %s</p>",
                __("Image ID", 'sage'),
                $src_id,
                __("Unable to generate data for this image ID. Please ensure the right data is being passed.", 'sage')
            );
        }

        // Empty array for data attributes return
        $da_return = [];
        $aa_return = [];

        // Classes for the HTML tags
        $properties  = $get_img_data['prop'] ?? '';
        $image_class = $img_attrs->get('image_class', false);
        $pic_class   = $img_attrs->get('pic_class', false);
        $image_id    = $img_attrs->get('id', false);

        // Image Information
        $source    = $get_img_data->get('url', false);
        $src_set   = $get_img_data->get('src_set', false);
        $src_sizes = $get_img_data->get('src_set_sizes', false);
        $get_webp  = $get_img_data->get('webp', false);
        $img_alt   = $get_img_data->get('alt', '');
        $image_type = $get_img_data->get('img_type', '');
        $is_svg = ($image_type && Str::contains($image_type, 'svg'));

        // Caption Data
        $fig_class   = $img_attrs->get('figure_class', false);
        $caption     = $img_attrs->get('caption', false);
        $caption_text = '';

        // Sets up the role attributes
        $role_attributes = $img_attrs->get(
            'role',
            $get_img_data->get('role', 'img')
        );

        if ($data_attrs) {
            $da_return = self::mapDataAttributes($data_attrs);
        }

        if ($aria_attrs) {
            $aa_return = self::mapAriaAttributes($aria_attrs);
        }

        /**
         * The webp output in a source tag
         */
        $webp_image = $get_webp ? sprintf(
            '<source srcset="%s" sizes="%s" type="image/webp">',
            $get_webp,
            $src_sizes,
        ) : '';

        /**
         * The original image output  in a source tag
         */
        $original_image = $src_set ? sprintf(
            '<source srcset="%s" sizes="%s" type="%s">',
            $src_set,
            $src_sizes,
            $image_type,
        ) : '';

        if ($img_attrs->has('caption')) {
            // Caption
            if ($caption) {
                $caption_text = !is_bool($caption) ? $caption : wp_get_attachment_caption($src_id);

                // The Caption
                $caption = $caption_text ? sprintf(
                    '<figcaption class="wp-caption-text %1$s" content="%2$s" property="v:caption"><p>%2$s</p></figcaption>',
                    $img_attrs->get('figcaption_class', ''),
                    html_entity_decode($caption_text)
                ) : '';
            }

            // Check to see if the caption exists.
            if ($caption && !$img_alt) {
                $img_alt = $caption_text;
            }
        }

        // Builds out the img HTML tag
        $image_build = sprintf(
            '<img src="%1$s" role="%4$s"alt="%2$s" property="v:%3$s" %5$s %6$s content="%1$s" %7$s %8$s />',
            esc_url($source),
            $img_alt,
            $properties,
            $role_attributes,
            $image_class ? "class=\"$image_class\"" : '',
            $image_id ? "id=\"$image_id\"" : '',
            implode(' ', $da_return),
            implode(' ', $aa_return),
        );

        // Returns a wrapper with a figure tag and a figcaption tag
        if ($caption_text) {
            return sprintf(
                '<figure %3$s><picture %4$s>%2$s %1$s</picture>%5$s</figure>',
                $image_build,
                !$is_svg ? $webp_image . $original_image : '',
                $fig_class ? "class=\"wp-caption $fig_class\"" : '',
                $pic_class ? "class=\"$pic_class\"" : '',
                $caption,
            );
        }

        // Returns with or without the webp/original source depending on if it is an SVG or not
        return sprintf(
            '<picture %3$s>%2$s %1$s</picture>',
            $image_build,
            !$is_svg ? $webp_image . $original_image : '',
            $pic_class ? "class=\"$pic_class\"" : '',
        );
    }

    /**
     * Since this data is used in a few spots, I wanted to be able to just parse and return the data as is
     * as well as have the content all in the same spot
     *
     * @param  int    $src_id
     * @param  array  $img_attrs
     *
     * @return array
     */
    public static function imgSrcSetArr(int $src_id, array $img_attrs = [])
    :array
    {
        // $img_attrs
        $img_size = $img_attrs['size'] ?? 'large';
        $img_prop = $img_attrs['property'] ?? 'image';

        // Image Information
        $img_src       = wp_get_attachment_image_url($src_id, $img_size);
        $src_set       = wp_get_attachment_image_srcset($src_id, $img_size) ?: '';
        $src_set_sizes = wp_get_attachment_image_sizes($src_id, $img_size) ?: '';
        $src_alt       = $img_attrs['alt'] ?? '';

        if (!$src_alt) {
            $src_alt = get_post_meta($src_id, '_wp_attachment_image_alt', true)
                ?: get_the_title($src_id);
        }

        if (is_array($src_alt)) {
            $src_alt = $src_alt[0];
        }

        // Grabs the image extension
        if ($img_src) {
            $image_type = Str::replace('.', '', Str::substr($img_src, strrpos($img_src, '.')));

            return [
                'alt'           => html_entity_decode($src_alt),
                'img_type'      => "image/$image_type",
                'prop'          => $img_prop,
                'role'          => 'img',
                'src_set'       => $src_set,
                'src_set_sizes' => $src_set_sizes,
                'url'           => $img_src,
                'webp'          => self::getWebp($src_set),
            ];
        }

        return [];
    }

    /**
     * Generates a proper formatted return of post images for Vue
     *
     * @param  int     $post_thumbnail_id  The image ID
     * @param  string  $image_size         The size of the image to return. Defaults to large
     * @param  bool    $include_caption    Whether to return the caption data text
     *
     * @return array|string
     */
    public static function generateImgData(int $post_thumbnail_id, string $image_size = 'large', bool $include_caption = false)
    :array|string
    {
        if (!$post_thumbnail_id) {
            return '';
        }

        $post_meta_alt = get_post_meta($post_thumbnail_id, '_wp_attachment_image_alt', true);
        $source        = wp_get_attachment_image_url($post_thumbnail_id, $image_size);

        $img_data = collect(
            [
                'alt'    => $post_meta_alt ? html_entity_decode($post_meta_alt) : html_entity_decode(get_the_title($post_thumbnail_id)),
                'caption' => $include_caption ? wp_get_attachment_caption($post_thumbnail_id) : '',
                'src'    => $source,
                'srcset' => wp_get_attachment_image_srcset($post_thumbnail_id, $image_size),
                'sizes'  => wp_get_attachment_image_sizes($post_thumbnail_id, $image_size) ?: '',
            ],
        )
            ->filter();

        if ($img_data->isNotEmpty()) {
            $image_type = Str::replace('.', '', Str::substr($source, strrpos($source, '.')));
            $img_data->put('type', "image/$image_type");

            // Handles the webp portion of the image
            $webp_srcset = self::getWebp($img_data->get('srcset'));
            if ($webp_srcset) {
                $img_data->put('webpSrcset', $webp_srcset);
            }

            return $img_data->toArray();
        }

        return [];
    }

    /**
     * Checks if there are any webp generated images in the uploads directory
     * and returns them as a concatenated string
     *
     * @param  string  $src_set  The srcset that WP generates for the image
     *
     * @return string
     */
    protected static function getWebp(string $src_set)
    :string
    {
        // Creation of webp support
        return collect(explode(', ', $src_set))
            ->map(fn ($srcset) => self::searchWebP(explode(' ', $srcset)))
            ->filter()
            ->implode(', ');
    }

    /**
     * Parse out the location of the webp file
     *
     * @param  array  $img_file The image array return
     *
     * @return string
     */
    protected static function searchWebP(array $img_file)
    :string
    {
        $home_url = get_home_url();
        if (!empty($img_file) && $img_file[0]) {
            $item_size = $img_file[1] ?? '';
            $extension = substr($img_file[0], strrpos($img_file[0], '.'));
            $webp_image = match ($extension) {
                '.jpg', '.png', '.jpeg', '.gif'  => self::matchWebpImage($extension, $img_file[0], $home_url, $item_size),
                default => '',
            };
        }

        return $webp_image ?? '';
    }

    /**
     * Method used for the match case to grab the webp image
     *
     * @param string $extension The file extension
     * @param string $img_file  The image file path
     * @param string $home_url  The Site URL
     * @param string $item_size The dimensions of the image
     *
     * @return string
     */
    protected static function matchWebpImage(string $extension, string $img_file, string $home_url, string $item_size)
    :string
    {
        $webp_file      = Str::replace($extension, '.webp', $img_file);
        $webp_file_path = parse_url($webp_file)['path'];
        $file_check     = WP_CONTENT_DIR . Str::replace('/app', '', $webp_file_path);
        if (file_exists($file_check)) {
            return "$home_url$webp_file_path $item_size";
        }
        return '';
    }

    /**
     * Maps the data attributes
     *
     * @param  array  $data_attributes An array of data attributes to map for output
     *
     * @return array
     */
    protected static function mapDataAttributes(array $data_attributes)
    :array
    {
        return collect($data_attributes)
            ->map(fn ($att_val, $att_type) => "data-$att_type=\"$att_val\"")
            ->toArray();
    }

    /**
     * Maps the aria attributes
     *
     * @param  array  $aria_attributes An array of aria attributes to map for output
     *
     * @return array
     */
    protected static function mapAriaAttributes(array $aria_attributes)
    :array
    {
        return collect($aria_attributes)
            ->map(fn ($att_val, $att_type) => "$att_type=\"$att_val\"")
            ->toArray();
    }
}
