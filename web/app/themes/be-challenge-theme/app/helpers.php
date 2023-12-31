<?php

declare(strict_types=1);

namespace App;

use Illuminate\Contracts\Container\BindingResolutionException;
use Roots\Sage\Config;
use Roots\Sage\Container;

/**
 * Get the sage container.
 *
 * @param  null            $abstract
 * @param  array           $parameters
 * @param  Container|null  $container
 *
 * @return Container|mixed
 * @throws BindingResolutionException
 */
function sage($abstract = null, array $parameters = [], Container $container = null)
{
    $container = $container ?: Container::getInstance();
    if (!$abstract) {
        return $container;
    }

    return $container->bound($abstract)
        ? $container->makeWith($abstract, $parameters)
        : $container->makeWith("sage.{$abstract}", $parameters);
}

/**
 * Get / set the specified configuration value.
 *
 * If an array is passed as the key, we will assume you want to set an array of values.
 *
 * @param  array|string  $key
 * @param  mixed         $default
 *
 * @return mixed|Config
 * @copyright Taylor Otwell
 * @link      https://github.com/laravel/framework/blob/c0970285/src/Illuminate/Foundation/helpers.php#L254-L265
 */
function config($key = null, $default = null)
{
    if (is_null($key)) {
        return sage('config');
    }
    if (is_array($key)) {
        return sage('config')->set($key);
    }

    return sage('config')->get($key, $default);
}

/**
 * @param  string  $file
 * @param  array   $data
 *
 * @return string
 * @throws BindingResolutionException
 */
function template(string $file, array $data = [])
:string
{
    if (!is_admin() && remove_action('wp_head', 'wp_enqueue_scripts', 1)) {
        wp_enqueue_scripts();
    }

    return sage('blade')->render($file, $data);
}

/**
 * Retrieve path to a compiled blade view
 *
 * @param         $file
 * @param  array  $data
 *
 * @return string
 * @throws BindingResolutionException
 */
function template_path($file, array $data = [])
:string
{
    return sage('blade')->compiledPath($file, $data);
}

/**
 * @param $asset
 *
 * @return string
 * @throws BindingResolutionException
 */
function asset_path($asset)
:string
{
    return sage('assets')->getUri($asset);
}

/**
 * @param  string|string[]  $templates  Possible template files
 *
 * @return array
 */
function filter_templates($templates)
:array
{
    $paths         = apply_filters('sage/filter_templates/paths', [
        'views',
        'resources/views',
    ]);
    $paths_pattern = "#^(" . implode('|', $paths) . ")/#";

    return collect($templates)
        ->map(function ($template) use ($paths_pattern) {
            /** Remove .blade.php/.blade/.php from template names */
            $template = preg_replace('#\.(blade\.?)?(php)?$#', '', ltrim($template));

            /** Remove partial $paths from the beginning of template names */
            if (strpos($template, '/')) {
                $template = preg_replace($paths_pattern, '', $template);
            }

            return $template;
        })
        ->flatMap(function ($template) use ($paths) {
            return collect($paths)
                ->flatMap(function ($path) use ($template) {
                    return [
                        "{$path}/{$template}.blade.php",
                        "{$path}/{$template}.php",
                    ];
                })
                ->concat([
                             "{$template}.blade.php",
                             "{$template}.php",
                         ]);
        })
        ->filter()
        ->unique()
        ->all();
}

/**
 * @param  string|string[]  $templates  Relative path to possible template files
 *
 * @return string Location of the template
 */
function locate_template($templates)
:string
{
    return \locate_template(filter_templates($templates));
}

/**
 * Determine whether to show the sidebar
 *
 * @return bool
 */
function display_sidebar()
:bool
{
    static $display;
    isset($display) || $display = apply_filters('sage/display_sidebar', false);

    return $display;
}
