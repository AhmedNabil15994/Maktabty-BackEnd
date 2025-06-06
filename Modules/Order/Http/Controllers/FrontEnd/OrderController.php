<?php

namespace Modules\Order\Http\Controllers\FrontEnd;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\MessageBag;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Catalog\Repositories\FrontEnd\ProductRepository as Product;
use Modules\Catalog\Traits\ShoppingCartTrait;
use Modules\Order\Entities\OrderAddons;
use Modules\Order\Entities\OrderCard;
use Modules\Order\Entities\OrderGift;
use Modules\Order\Events\ActivityLog;

//use Modules\Transaction\Services\PaymentService;

//use Modules\Transaction\Services\UPaymentTestService;

use Modules\Order\Http\Requests\FrontEnd\CreateOrderRequest;
use Modules\Order\Repositories\FrontEnd\OrderRepository as Order;
use Modules\Shipping\Traits\ShippingTrait;
use Modules\Transaction\Services\MyFatoorahPaymentService;
use Modules\Transaction\Services\TapPaymentService;
use Modules\Transaction\Services\UPaymentService;
use Modules\Transaction\Traits\PaymentTrait;
use Modules\User\Entities\Address;

class OrderController extends Controller
{
    use ShoppingCartTrait, ShippingTrait;

    protected $payment;
    protected $order;
    protected $product;

    public function __construct(Order $order, UPaymentService $payment, Product $product)
    {
        $this->payment = $payment;
        $this->order = $order;
        $this->product = $product;
    }

    public function index()
    {
        $ordersIDs = isset($_COOKIE[config('core.config.constants.ORDERS_IDS')]) && !empty($_COOKIE[config('core.config.constants.ORDERS_IDS')]) ? (array) \GuzzleHttp\json_decode($_COOKIE[config('core.config.constants.ORDERS_IDS')]) : [];

        if (auth()->user()) {
            $orders = $this->order->getAllByUser($ordersIDs);
            return view('order::frontend.orders.index', compact('orders'));
        } else {
            $orders = count($ordersIDs) > 0 ? $this->order->getAllGuestOrders($ordersIDs) : [];
            return view('order::frontend.orders.index', compact('orders'));
        }
    }

    public function invoice($id)
    {
        if (auth()->user()) {
            $order = $this->order->findByIdWithUserId($id);
        } else {
            $order = $this->order->findGuestOrderById($id);
        }

        if (!$order) {
            return abort(404);
        }

        if (count($order->orderGifts) > 0) {
            $order->orderGifts = $this->mapOrderGifts($order->orderGifts);
        }

        $order->orderProducts = $order->orderProducts->mergeRecursive($order->orderVariations);

        return view('order::frontend.orders.details', compact('order'));
    }

    public function reOrder($id)
    {
        $order = $this->order->findByIdWithUserId($id);
        if (!$order) {
            return abort(404);
        }

        if (count($order->orderGifts) > 0) {
            $order->orderGifts = $this->mapOrderGifts($order->orderGifts);
        }
        $order->orderProducts = $order->orderProducts->mergeRecursive($order->orderVariations);
        return view('order::frontend.orders.re-order', compact('order'));
    }

    public function guestInvoice()
    {
        $savedID = [];
        if (isset($_COOKIE[config('core.config.constants.ORDERS_IDS')]) && !empty($_COOKIE[config('core.config.constants.ORDERS_IDS')])) {
            $savedID = (array) \GuzzleHttp\json_decode($_COOKIE[config('core.config.constants.ORDERS_IDS')]);
        }
        $id = count($savedID) > 0 ? $savedID[count($savedID) - 1] : 0;
        $order = $this->order->findByIdWithGuestId($id);
        if (!$order) {
            abort(404);
        }

        if (count($order->orderGifts) > 0) {
            $order->orderGifts = $this->mapOrderGifts($order->orderGifts);
        }

        $order->orderProducts = $order->orderProducts->mergeRecursive($order->orderVariations);
        return view('order::frontend.orders.invoice', compact('order'))->with([
            'alert' => 'success', 'status' => __('order::frontend.orders.index.alerts.order_success'),
        ]);
    }

    public function createOrder(CreateOrderRequest $request)
    {
        $errors1 = [];
        $errors2 = [];
        $errors3 = [];
        $errors4 = [];

        $address = $request->radio_address_type == 'selected_address' ? Address::find($request->selected_address_id) : null;

        if ($address) {

            $this->setShippingTypeByAddress($address);
            $shippingValidateAddress = $this->shipping->validateAddress($request, $address);
        } else {

            $this->setShippingTypeByRequest($request);
            $shippingValidateAddress = $this->shipping->validateAddress($request);
        }

        if ($shippingValidateAddress[0]) {

            $errors = new MessageBag([
                'productCart' => [$shippingValidateAddress],
            ]);
            return redirect()->back()->with(["errors" => $errors]);
        } else {

            $request->merge(['address_type' => $shippingValidateAddress['addressType'], 'json_data' => $shippingValidateAddress['jsonData']]);
        }

        $payment = $request['payment'] != 'cash' ? PaymentTrait::getPaymentGateway($request['payment']) : 'cash';

        if ($request['payment'] != 'cash' && !$payment) {

            return redirect()->back()->with([
                'alert' => 'danger', 'status' => __('order::frontend.orders.index.alerts.payment_not_supported_now'),
            ]);
        } elseif (
            $payment == 'cash' && $request->has('json_data') && isset($request->json_data['country_id'])
            && count((array) config('setting.payment_gateway.cash.supported_countries', []))
            && !in_array($request->json_data['country_id'], (array) config('setting.payment_gateway.cash.supported_countries', []))
        ) {

            return redirect()->back()->with([
                'alert' => 'danger', 'status' => __('order::frontend.orders.index.alerts.country_not_support_cash_payment'),
            ]);
        }

        foreach (getCartContent() as $key => $item) {

            if ($item->attributes->product->product_type == 'product') {
                $cartProduct = $item->attributes->product;
                $product = $this->product->findOneProduct($cartProduct->id);
                if (!$product) {
                    return redirect()->back()->with([
                        'alert' => 'danger', 'status' => __('cart::api.cart.product.not_found') . $cartProduct->id,
                    ]);
                }

                $product->product_type = 'product';
            } else {
                $cartProduct = $item->attributes->product;
                $product = $this->product->findOneProductVariant($cartProduct->id);
                if (!$product) {
                    return redirect()->back()->with([
                        'alert' => 'danger', 'status' => __('cart::api.cart.product.not_found') . $cartProduct->id,
                    ]);
                }

                $product->product_type = 'variation';
            }

            $productFound = $this->productFound($product, $item);
            if ($productFound) {
                $errors1[] = $productFound;
            }

            $activeStatus = $this->checkActiveStatus($product, $request);
            if ($activeStatus) {
                $errors2[] = $activeStatus;
            }

            if (!is_null($product->qty)) {

                $maxQtyInCheckout = $this->checkMaxQtyInCheckout($product, $item->quantity, $cartProduct->qty);

                if ($maxQtyInCheckout) {
                    $errors3[] = $maxQtyInCheckout;
                }
            }
        }

        if ($errors1 || $errors2 || $errors3 || $errors4) {
            $errors = new MessageBag([
                'productCart' => $errors1,
                'productCart2' => $errors2,
                'productCart3' => $errors3,
                'productCart4' => $errors4,
            ]);
            return redirect()->back()->with(["errors" => $errors]);
        }

        $order = $this->order->create($request);
        if (!$order) {
            return $this->redirectToFailedPayment();
        }

        $this->shipping->createShipment($request, $order);

        if ($request['payment'] != 'cash') {
            $redirect = $payment->send($order, 'online', 'frontend-order');

            if (isset($redirect['status'])) {

                if ($redirect['status'] == true && isset($redirect['url'])) {
                    return redirect()->away($redirect['url']);
                } else {
                    return back()->withInput()->withErrors(['payment' => 'Online Payment not valid now']);
                }
            }

            return 'field';
        }
        return $this->redirectToPaymentOrOrderPage($request, $order);
    }

    public function webhooks(Request $request)
    {
        $this->order->updateOrder($request);
    }

    public function success(Request $request)
    {
        $order = $this->order->updateOrder($request);
        return $order ? $this->redirectToPaymentOrOrderPage($request) : $this->redirectToFailedPayment();
    }

    public function successTap(Request $request)
    {
        $data = (new TapPaymentService())->getTransactionDetails($request);

        $request = PaymentTrait::buildTapRequestData($data, $request);

        if ($request->Result == 'CAPTURED') {
            return $this->success($request);
        }
        return $this->failed($request);
    }

    public function myFatoorahCallBack(Request $request)
    {
        $data = (new MyFatoorahPaymentService())->GetPaymentStatus($request->paymentId, 'paymentId');

        $request = PaymentTrait::buildMyFatoorahRequestData($data, $request);

        if ($request->Result == 'CAPTURED') {
            return $this->success($request);
        }
        return $this->failed($request);
    }

    public function failed(Request $request)
    {
        $this->order->updateOrder($request);
        return $this->redirectToFailedPayment();
    }

    public function redirectToPaymentOrOrderPage($data, $order = null)
    {
        $order = ($order == null) ? $this->order->findById($data['OrderID']) : $this->order->findById($order->id);
        try {

            if ($this->sendNotifications($order)) {
            }
        } catch (\Exception $e) {
            info($e);
        }
        $this->clearCart();
        return $this->redirectToInvoiceOrder($order);
    }

    public function redirectToInvoiceOrder($order)
    {
        ################# Start Store Guest Orders In Browser Cookie ######################
        if (isset($_COOKIE[config('core.config.constants.ORDERS_IDS')]) && !empty($_COOKIE[config('core.config.constants.ORDERS_IDS')])) {
            $cookieArray = (array) \GuzzleHttp\json_decode($_COOKIE[config('core.config.constants.ORDERS_IDS')]);
        }
        $cookieArray[] = $order['id'];
        setcookie(
            config('core.config.constants.ORDERS_IDS'),
            \GuzzleHttp\json_encode($cookieArray),
            time() + (5 * 365 * 24 * 60 * 60),
            '/'
        ); // expires at 5 year
        ################# End Store Guest Orders In Browser Cookie ######################

        if (auth()->user()) {
            return redirect()->route('frontend.orders.invoice', $order->id)->with([
                'alert' => 'success', 'status' => __('order::frontend.orders.index.alerts.order_success'),
            ]);
        }

        return redirect()->route('frontend.orders.guest.invoice');
    }

    public function redirectToFailedPayment()
    {
        return redirect()->route('frontend.checkout.index')->with([
            'alert' => 'danger', 'status' => __('order::frontend.orders.index.alerts.order_failed'),
        ]);
    }

    public function sendNotifications($order)
    {
        $this->fireLog($order);

        if ($order->orderAddress) {
            //            Notification::route('mail', $order->orderAddress->email)->notify(
            //                (new UserNewOrderNotification($order))->locale(locale())
            //            );
        }

        //        Notification::route('mail', config('setting.contact_us.email'))->notify(
        //            (new AdminNewOrderNotification($order))->locale(locale())
        //        );
    }

    public function fireLog($order)
    {
        try {
            $dashboardUrl = LaravelLocalization::localizeUrl(url(route('dashboard.orders.show', [$order->id, 'current_orders'])));
            $data = [
                'id' => $order->id,
                'type' => 'orders',
                'url' => $dashboardUrl,
                'description_en' => 'New Order',
                'description_ar' => 'طلب جديد ',
            ];
            event(new ActivityLog($data));
        } catch (\Exception $e) {
            info($e);
        }
    }

    private function mapOrderGifts($orderGifts)
    {
        return $orderGifts->map(function ($item) {
            $result = [];
            foreach ($item->products_ids as $i => $value) {
                if (isset($value['type']) && $value['type'] == 'product') {
                    $prd = $this->product->findById($value['id']);
                    $result[$i]['title'] = $prd->title;
                } else {
                    $prd = $this->product->findVariantProductById($value['id']);
                    $prdName = generateVariantProductData($prd->product, $prd->product_variant_id, $prd->productValues->pluck('option_value_id')->toArray())['name'];
                    $result[$i]['title'] = $prdName;
                }
            }
            $item->products = array_values($result);
            return $item;
        });
    }
}
