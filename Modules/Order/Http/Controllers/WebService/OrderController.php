<?php

namespace Modules\Order\Http\Controllers\WebService;

use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

//use Modules\Order\Http\Requests\WebService\CreateOrderRequestOld;
use Modules\Apps\Http\Controllers\WebService\WebServiceController;
use Modules\Cart\Traits\CartTrait;
use Modules\Catalog\Repositories\WebService\CatalogRepository as Catalog;
use Modules\Company\Repositories\WebService\CompanyRepository as Company;
use Modules\Order\Entities\OrderStatusesHistory;
use Modules\Order\Entities\PaymentStatus;
//use Modules\Transaction\Services\PaymentService;
use Modules\Order\Events\ActivityLog;

//use Modules\Order\Repositories\WebService\OrderRepositoryOld as Order;
use Modules\Order\Http\Requests\WebService\CreateOrderRequest;
use Modules\Order\Http\Requests\WebService\RateRequest;
use Modules\Order\Jobs\SendOrderToMultipleJob;
use Modules\Order\Repositories\WebService\OrderRepository as Order;
use Modules\Order\Repositories\WebService\RateRepository as Rate;
use Modules\Order\Transformers\WebService\OrderProductResource;
use Modules\Order\Transformers\WebService\OrderResource;
use Modules\Transaction\Services\TapPaymentService;
use Modules\Transaction\Services\UPaymentService;
use Modules\Transaction\Traits\PaymentTrait;
use Modules\User\Repositories\WebService\AddressRepository;

class OrderController extends WebServiceController
{
    use CartTrait;

    protected $payment;
    protected $order;
    protected $company;
    protected $catalog;
    protected $address;
    protected $rate;

    public function __construct(
        Order $order,
        UPaymentService $payment,
        Company $company,
        Catalog $catalog,
        AddressRepository $address,
        Rate $rate
    ) {
        $this->payment = $payment;
        $this->order = $order;
        $this->company = $company;
        $this->catalog = $catalog;
        $this->address = $address;
        $this->rate = $rate;
    }

    public function createOrder(CreateOrderRequest $request)
    {
        if (auth('api')->check()) {
            $userToken = auth('api')->user()->id;
        } else {
            $userToken = $request->user_id;
        }

        // Check if address is not found
        if ($request->address_type == 'selected_address') {
            // get address by id
            $companyDeliveryFees = getCartConditionByName($userToken, 'company_delivery_fees');
            $addressId = isset($companyDeliveryFees->getAttributes()['address_id'])
            ? $companyDeliveryFees->getAttributes()['address_id']
            : null;
            $address = $this->address->findByIdWithoutAuth($addressId);
            if (!$address) {
                return $this->error(__('user::webservice.address.errors.address_not_found'), [], 422);
            }

        }

        foreach (getCartContent($userToken) as $key => $item) {

            if ($item->attributes->product->product_type == 'product') {
                $cartProduct = $item->attributes->product;
                $product = $this->catalog->findOneProduct($cartProduct->id);
                if (!$product) {
                    return $this->error(__('cart::api.cart.product.not_found') . $cartProduct->id, [], 422);
                }

                $product->product_type = 'product';
            } else {
                $cartProduct = $item->attributes->product;
                $product = $this->catalog->findOneProductVariant($cartProduct->id);
                if (!$product) {
                    return $this->error(__('cart::api.cart.product.not_found') . $cartProduct->id, [], 422);
                }

                $product->product_type = 'variation';
            }

            $checkPrdFound = $this->productFound($product, $item);
            if ($checkPrdFound) {
                return $this->error($checkPrdFound, [], 422);
            }

            $checkPrdStatus = $this->checkProductActiveStatus($product, $request);
            if ($checkPrdStatus) {
                return $this->error($checkPrdStatus, [], 422);
            }

            if (!is_null($product->qty)) {
                $checkPrdMaxQty = $this->checkMaxQty($product, $item->quantity);
                if ($checkPrdMaxQty) {
                    return $this->error($checkPrdMaxQty, [], 422);
                }

            }

            //        TODO    $checkVendorStatus = $this->vendorStatus($product);
            //            if ($checkVendorStatus)
            //                return $this->error($checkVendorStatus, [], 422);

        }

        $payment = $request['payment'] != 'cash' ? PaymentTrait::getPaymentGateway($request['payment']) : 'cash';

        $order = $this->order->create($request, $userToken);
        if (!$order) {
            return $this->error('error', [], 422);
        }

        if ($request['payment'] != 'cash' && !$payment) {
            return $this->error(__('order::frontend.orders.index.alerts.payment_not_supported_now'), [], 422);
        }

        /* if ($request['payment'] != 'cash') {
        $payment = $this->payment->send($order, 'api-order');

        return $this->response([
        'paymentUrl' => $payment,
        ]);
        } */

        if ($request['payment'] != 'cash') {
            $redirect = $payment->send($order, 'online', 'api-order');

            if (isset($redirect['status'])) {

                if ($redirect['status'] == true && isset($redirect['url'])) {
                    return $this->response([
                        'paymentUrl' => $redirect['url'],
                        'order_id' => $order->id,
                    ]);
                } else {
                    return $this->error('Online Payment not valid now', [], 422);
                }
            }

            return 'field';
        }

        $this->fireLog($order);
        $this->clearCart($userToken);

        $htmlOrder = $this->returnHtmlOrder($order);
        return $this->response(new OrderResource($order), 'Successfully', $htmlOrder);
    }

    public function webhooks(Request $request)
    {
        $this->order->updateOrder($request);
    }

    public function success(Request $request)
    {
        $order = $this->order->updateOrder($request);
        if ($order) {
            $orderDetails = $this->order->findById($request['OrderID']);
            $userToken = $orderDetails->user_id ?? $orderDetails->user_token;
            if ($orderDetails) {
                $this->fireLog($orderDetails);
                if (!is_null($userToken)) {
                    $this->clearCart($userToken);
                }
                $htmlOrder = $this->returnHtmlOrder($orderDetails);
                return $this->response(new OrderResource($orderDetails), 'Successfully', $htmlOrder);
            } else {
                return $this->error(__('order::frontend.orders.index.alerts.order_failed'), [], 422);
            }

        }
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

    public function failed(Request $request)
    {
        $this->order->updateOrder($request);
        return $this->error(__('order::frontend.orders.index.alerts.order_failed'), [], 422);
    }

    public function userOrdersList(Request $request)
    {
        if (auth('api')->check()) {
            $userId = auth('api')->id();
            $userColumn = 'user_id';
        } else {
            $userId = $request->user_token ?? 'not_found';
            $userColumn = 'user_token';
        }
        $orders = $this->order->getAllByUser($userId, $userColumn);
        return $this->response(OrderResource::collection($orders));
    }

    public function getOrderDetails(Request $request, $id)
    {
        $order = $this->order->findById($id);

        if (!$order) {
            return $this->error(__('order::api.orders.validations.order_not_found'), [], 422);
        }

        $allOrderProducts = $order->orderProducts->mergeRecursive($order->orderVariations);
        return $this->response(OrderProductResource::collection($allOrderProducts));
    }

    public function fireLog($order)
    {
        $dashboardUrl = LaravelLocalization::localizeUrl(url(route('dashboard.orders.show', [$order->id, 'current_orders'])));
        $data = [
            'id' => $order->id,
            'type' => 'orders',
            'url' => $dashboardUrl,
            'description_en' => 'New Order',
            'description_ar' => 'طلب جديد ',
        ];

        event(new ActivityLog($data));
        $this->sendNotifications($order);
    }

    public function sendNotifications($order)
    {
        $email = optional($order->orderAddress)->email ?? (optional($order->user)->email ?? null);
        if (!is_null($email)) {
            $emails[] = $email;
            dispatch(new SendOrderToMultipleJob($order, $emails, 'user_email'));
        }

        if (config('setting.contact_us.email')) {
            $emails = [];
            $emails[] = config('setting.contact_us.email');
            dispatch(new SendOrderToMultipleJob($order, $emails, 'admin_email'));
        }
    }

    public function orderRate(RateRequest $request, $id)
    {
        $order = $this->rate->findOrderByIdWithUserId($id);
        if ($order) {
            $rate = $this->rate->checkUserRate($id);
            if (!$rate) {
                $createdRate = $this->rate->create($request, $id);
                return $this->response([]);
            } else {
                return $this->error(__('order::api.rates.user_rate_before'));
            }

        } else {
            return $this->error(__('order::api.rates.user_not_have_order'));
        }

    }

    private function returnHtmlOrder($order)
    {
        $order->allProducts = $order->orderProducts->mergeRecursive($order->orderVariations);
        $htmlOrder['html_order'] = view('order::api.html-order', compact('order'))->render();
        return $htmlOrder;
    }

    public function cancelOrderPayment(Request $request, $id)
    {
        if (auth('api')->check()) {
            $userData['column'] = 'user_id';
            $userData['value'] = auth('api')->id();
        } else {
            $userData['column'] = 'user_token';
            $userData['value'] = $request->user_token;
        }

        $order = $this->order->checkOrderPendingPayment($id, $userData);
        if ($order) {
            $orderStatusId = $this->order->getOrderStatusByFlag('failed')->id;
            $paymentStatusId = optional(PaymentStatus::where('flag', 'failed')->first())->id ?? $order->payment_status_id;

            $order->update([
                'order_status_id' => $orderStatusId, // failed
                'payment_status_id' => $paymentStatusId, // failed
                // 'payment_confirmed_at' => null,
                'increment_qty' => true,
            ]);

            // Add Order Status History
            OrderStatusesHistory::create([
                'order_id' => $order->id,
                'order_status_id' => $orderStatusId, // failed
                'user_id' => null,
            ]);

            if ($order->orderProducts) {
                foreach ($order->orderProducts as $i => $orderProduct) {
                    if (!is_null($orderProduct->product->qty)) {
                        $orderProduct->product->increment('qty', $orderProduct->qty);
                    }
                }
            }

            if ($order->orderVariations) {
                foreach ($order->orderVariations as $i => $orderProduct) {
                    if (!is_null($orderProduct->variant->qty)) {
                        $orderProduct->variant->increment('qty', $orderProduct->qty);
                    }
                }
            }

        }
        return $this->response(null);
    }
}
