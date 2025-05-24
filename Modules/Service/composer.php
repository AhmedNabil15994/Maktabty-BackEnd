<?php

view()->composer([
    'notification::dashboard.notifications.*',
    'slider::dashboard.sliders.*',
    'order::dashboard.shared._filter',
    'order::dashboard.orders.create',
], \Modules\Service\ViewComposers\Dashboard\ServiceComposer::class);

view()->composer(['apps::dashboard.index'], \Modules\Service\ViewComposers\Dashboard\ServiceStatisticsComposer::class);

// Dashboard ViewComposr
view()->composer([
    'service::dashboard.service_categories.*',
    'service::dashboard.services.*',
], \Modules\Service\ViewComposers\Dashboard\ServiceCategoryComposer::class);
