<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helper\Helper;
use Illuminate\Support\Collection;
use Sober\Controller\Controller;
use App\Traits\SageTraits;
use function App\asset_path;

class App extends Controller
{

    use SageTraits;

    /**
     * The site name
     *
     * @return string|void
     */
    public function siteName()
    {
        return get_bloginfo('name');
    }

    /**
     * The page title
     *
     * @return string|void
     */
    public static function title()
    {
        if (is_home()) {
            $home_page = get_option('page_for_posts', true);
            if ($home_page) {
                return get_the_title($home_page);
            }
            return __('Latest Posts', 'sage');
        }
        if (is_archive()) {
            return get_the_archive_title();
        }
        if (is_search()) {
            return sprintf(__('Search Results for %s', 'sage'), get_search_query());
        }
        if (is_404()) {
            return __('Not Found', 'sage');
        }
        return get_the_title();
    }

    /**
     * The home url used for the site.
     *
     * @return string|void
     */
    public function siteUrl()
    {
        return home_url();
    }

    /**
     * Forces any front end user to be redirected to the homepage
     *
     * @return bool
     */
    public static function templateRedirect(): bool
    {
        return wp_redirect(home_url(), 301);
    }

    /**
     * Grabs the site logo
     *
     * @return int
     */
    public function siteLogo()
    :int
    {
        // #TODO Remove hardcoded value
        return 6;
    }

}
