<?php


/*
|================================================================================
|                             Back-END ROUTES
|================================================================================
*/
Route::prefix('dashboard')->middleware(['dashboard.auth', 'permission:dashboard_access'])->group(function () {

    foreach (["service.php", "service_orders.php", "categories.php"] as $value) {
        require(module_path('Service', 'Routes/Dashboard/' . $value));
    }
});
