<?php

namespace Modules\Service\ViewComposers\Dashboard;

use Illuminate\View\View;
use Modules\Service\Repositories\Dashboard\ServiceRepository as ServiceRepo;

class ServiceStatisticsComposer
{
    public $servicesCount = [];

    public function __construct(ServiceRepo $service)
    {
        $this->servicesCount = $service->servicesCount();
    }

    /**
     * Bind data to the view.
     *
     * @param View $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with([
            'servicesCount' => $this->servicesCount,
        ]);
    }
}
