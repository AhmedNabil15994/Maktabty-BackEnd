<?php

namespace Modules\Catalog\ViewComposers\Dashboard;

use Illuminate\View\View;
use Modules\Catalog\Repositories\Dashboard\EducationalInstitutionRepository as EducationalInstitutionRepo;

class EducationalInstitutionComposer
{
    public $activeEducationalInstitutions = [];

    public function __construct(EducationalInstitutionRepo $educationalInstitution)
    {
        $this->activeEducationalInstitutions = $educationalInstitution->getAllActive();
    }

    /**
     * Bind data to the view.
     *
     * @param View $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('activeEducationalInstitutions', $this->activeEducationalInstitutions);
    }
}
