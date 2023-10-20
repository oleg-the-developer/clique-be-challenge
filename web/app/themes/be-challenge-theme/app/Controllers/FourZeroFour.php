<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Traits\SageTraits;
use Illuminate\Support\Collection;
use Sober\Controller\Controller;

class FourZeroFour extends Controller
{

    use SageTraits;
    /**
     * Dedicated 404 Controller
     *
     * @var string
     */
    protected $template = '404';

    /**
     * ACF Field Values
     *
     * @var string[]
     */
    protected $acf = [];

    /**
     * ACF options for the error page
     *
     * @return Collection
     */
    public function error()
    :Collection
    {
        return collect(
            [
                'title' => 'Error 404',
                'content' => "Sorry the page you're looking for cannot be found.",
                'btn_class' => 'error-class',
                'image' => 71,
            ]
        );
    }
}
