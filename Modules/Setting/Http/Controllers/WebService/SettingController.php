<?php

namespace Modules\Setting\Http\Controllers\WebService;

use Illuminate\Support\Arr;
use Modules\Apps\Http\Controllers\WebService\WebServiceController;

class SettingController extends WebServiceController
{
    public function index()
    {
        $settingExceptions = ['payment_gateway', 'custom_codes', 'order_status', 'products'];
        // $settings = Arr::except(config('setting'), $settingExceptions);

        $paymentExceptions = ['payment_mode', 'live_mode', 'test_mode', 'client_commissions', 'account_type', 'commissions', 'client_commissions', 'status'];
        $supportedPayments = config('setting.payment_gateway') ?? [];
        $customSupportedPayments = [];
        $supportedPayments = collect($supportedPayments)->reject(function ($item) {
            return !isset($item['status']) || $item['status'] != 'on';
        })->map(function ($item, $k) use ($paymentExceptions, &$customSupportedPayments) {
            foreach ($paymentExceptions as $key => $value) {
                if (isset($item[$value])) {
                    unset($item[$value]);
                }
            }

            if ($k == 'cash') {
                $title = locale() == 'ar' ? 'الدفع عند الإستلام' : 'Cash on delivery';
            } else {
                $title = $item['title_' . locale()] ?? null;
            }

            $customSupportedPayments[] = [
                'key' => $k,
                'title' => $title,
            ];
        });

        $settings = Arr::except(config('setting'), $settingExceptions);
        $settings = array_merge($settings, ['supported_payments' => $customSupportedPayments]);

        return $this->response($settings);
    }
}
