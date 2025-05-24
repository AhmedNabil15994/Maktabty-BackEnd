<?php

namespace Modules\Catalog\Enums;

class ProductType extends \SplEnum
{
    const __default = self::Product;
    const Product = "product";
    const Note = "note";

    public function __construct()
    {
        parent::__construct("product");
    }
}
