<?php

namespace App\Data;

class SearchData
{
    /**
     * var string
     */
    public $q = '';

    /**
     * @var category[]
     */
    public $categories = [];

    /**
     * var null\integer
     */
    public $max;

    /**
     * var null\integer
     */
    public $min;
}