<?php

namespace Modules\Service\Traits;

use Modules\Service\Entities\ServiceCategory;

trait ServiceTrait
{
    public function getAllSubServiceCategoryIds($categoryId)
    {
        $cat = ServiceCategory::active()->find($categoryId);
        $allCats = [];
        if (!is_null($cat)) {
            $allCats = $cat->getAllRecursiveChildren()->pluck('id')->toArray();
        }

        return $allCats;
    }
}
