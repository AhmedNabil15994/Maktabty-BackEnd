<?php

namespace Modules\Transaction\Services;

class UPaymentService
{
    /*
     * Test CREDENTIALS
     */
    const MERCHANT_ID = "1201";
    const USERNAME = "test";
    const PASSWORD = "test";
    const API_KEY = "jtest123";

    protected $paymentMode = 'test_mode';
    protected $test_mode = 1;
    protected $whitelabled = true;
    protected $paymentUrl = "https://api.upayments.com/test-payment";
    protected $apiKey = '';
    protected $charges = 0.350;
    protected $cc_charges = 2.7;

    public function __construct()
    {
        if (config('setting.payment_gateway.upayment.payment_mode') == 'live_mode') {
            $this->paymentMode = 'live_mode';
            $this->test_mode = false;
            $this->whitelabled = false;
            $this->paymentUrl = "https://api.upayments.com/payment-request";
            $this->apiKey = password_hash(config('setting.payment_gateway.upayment.' . $this->paymentMode . '.API_KEY') ?? self::API_KEY, PASSWORD_BCRYPT);
        } else {
            $this->apiKey = config('setting.payment_gateway.upayment.' . $this->paymentMode . '.API_KEY') ?? self::API_KEY;
        }

        $this->charges = config("setting.payment_gateway.upayment.{$this->paymentMode}.charges", $this->charges);
        $this->cc_charges = config("setting.payment_gateway.upayment.{$this->paymentMode}.cc_charges", $this->cc_charges);
    }

    public function send($order, $payment, $type = 'api-order')
    {
        if (auth()->check()) {
            $user = [
                'name' => auth()->user()->name ?? '',
                'email' => auth()->user()->email ?? '',
                'mobile' => auth()->user()->calling_code ?? '' . auth()->user()->mobile ?? '',
            ];
        } else {
            $checkDomain = fetchLiveDomain(env('APP_URL'));
            $domain = $checkDomain == false ? 'example.com' : $checkDomain;
            $user = [
                'name' => $order->orderAddress->username ?? 'Guest User',
                'email' => $order->orderAddress->email ?? 'guest@' . $domain,
                'mobile' => $order->orderAddress->mobile ?? '000000000',
            ];
        }

        $extraMerchantsData = array();
        $extraMerchantsData['amounts'][0] = $this->test_mode ? $order['total'] : priceWithCurrenciesCode($order['total'], true, false);
        $extraMerchantsData['charges'][0] = $this->charges;
        $extraMerchantsData['chargeType'][0] = 'fixed'; // or 'percentage'
        $extraMerchantsData['cc_charges'][0] = $this->cc_charges; // or 'percentage'
        $extraMerchantsData['cc_chargeType'][0] = 'percentage'; // or 'percentage'
        $extraMerchantsData['ibans'][0] = config('setting.payment_gateway.upayment.' . $this->paymentMode . '.IBAN') ?? '';

        $url = $this->paymentUrls($type);

        $fields = [
            'api_key' => $this->apiKey,
            'merchant_id' => config('setting.payment_gateway.upayment.' . $this->paymentMode . '.MERCHANT_ID') ?? self::MERCHANT_ID,
            'username' => config('setting.payment_gateway.upayment.' . $this->paymentMode . '.USERNAME') ?? self::USERNAME,
            'password' => stripslashes(config('setting.payment_gateway.upayment.' . $this->paymentMode . '.PASSWORD') ?? self::PASSWORD),
            'order_id' => $order['id'],
            'CurrencyCode' => $this->test_mode ? 'KWD' : priceWithCurrenciesCode($order['total'], false, true),
            'CstFName' => $user['name'] ?? 'Unknown',
            'CstEmail' => $user['email'] ?? 'test@example.com',
            'CstMobile' => $user['mobile'] ?? '000000000',
            'success_url' => $url['success'],
            'error_url' => $url['failed'],
            'ExtraMerchantsData' => json_encode($extraMerchantsData),
            'test_mode' => $this->test_mode, // 1 == test mode enabled
            'whitelabled' => $this->whitelabled, // false == in live mode
            'payment_gateway' => $payment, // knet / cc
            'reference' => $order['id'],
            'notifyURL' => $url['webhooks'],
            'total_price' => $this->test_mode ? $order['total'] : priceWithCurrenciesCode($order['total'], true, false),
        ];

        $fields_string = http_build_query($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->paymentUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);
        $server_output = json_decode($server_output, true);

        if (isset($server_output['status']) && $server_output['status'] == 'errors') {
            return ['status' => false];
        }
        return ['status' => true, 'url' => $server_output['paymentURL']];
    }

    public function paymentUrls($orderType)
    {
        if ($orderType == 'api-order') {
            $url['success'] = url(route('api.orders.success'));
            $url['failed'] = url(route('api.orders.failed'));
            $url['webhooks'] = url(route('api.orders.webhooks'));
        } else {
            $url['success'] = url(route('frontend.orders.success'));
            $url['failed'] = url(route('frontend.orders.failed'));
            $url['webhooks'] = url(route('frontend.orders.webhooks'));
        }
        return $url;
    }

}
