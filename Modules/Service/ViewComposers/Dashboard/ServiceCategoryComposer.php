<?php

namespace Modules\Service\ViewComposers\Dashboard;

use Modules\Service\Repositories\Dashboard\ServiceCategoryRepository as ServiceCategory;
use Illuminate\View\View;
use Cache;

class ServiceCategoryComposer
{
    public $mainCategories;
    public $sharedActiveCategories;
    public $allCategories;

    public function __construct(ServiceCategory $category)
    {
        $this->mainCategories = $category->mainCategories();
        $this->sharedActiveCategories = $category->getAllActive();
        $this->allCategories = $category->getAll();
    }

    /**
     * Bind data to the view.
     *
     * @param View $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with(['mainCategories' => $this->mainCategories, 'sharedActiveCategories' => $this->sharedActiveCategories, 'allCategories' => $this->allCategories]);
    }
}
