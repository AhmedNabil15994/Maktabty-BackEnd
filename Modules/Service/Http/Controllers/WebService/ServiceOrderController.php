<?php

namespace Modules\Service\Http\Controllers\WebService;

use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Apps\Http\Controllers\WebService\WebServiceController;
use Modules\Order\Events\ActivityLog;
use Modules\Service\Http\Requests\WebService\CreateServiceOrderRequest;
use Modules\Service\Repositories\WebService\ServiceOrderRepository as ServiceOrder;
use Modules\Service\Repositories\WebService\ServiceRepository as Service;
use Modules\Service\Transformers\WebService\ServiceOrderResource;

class ServiceOrderController extends WebServiceController
{
    protected $order;
    protected $service;

    public function __construct(ServiceOrder $order, Service $service)
    {
        $this->order = $order;
        $this->service = $service;
    }

    public function createOrder(CreateServiceOrderRequest $request)
    {
        $service = $this->service->findById($request->service_id);
        if (!$service) {
            return $this->error('error', [], 422);
        }

        $order = $this->order->create($request);
        if (!$order) {
            return $this->error(__('order::api.orders.validations.order_error'), [], 422);
        }

        $newOrder = $order->fresh();
        $this->fireLog($newOrder);
        return $this->response(new ServiceOrderResource($newOrder));
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
        return $this->response(ServiceOrderResource::collection($orders));
    }

    public function getOrderDetails(Request $request, $id)
    {
        $order = $this->order->findById($id);

        if (!$order) {
            return $this->error(__('order::api.orders.validations.order_not_found'), [], 422);
        }

        return $this->response(new ServiceOrderResource($order));
    }

    public function fireLog($order)
    {
        $dashboardUrl = LaravelLocalization::localizeUrl(url(route('dashboard.service_orders.show', $order->id)));
        $data = [
            'id' => $order->id,
            'type' => 'orders',
            'url' => $dashboardUrl,
            'description_en' => 'New Service Order',
            'description_ar' => 'طلب خدمة جديد ',
        ];

        event(new ActivityLog($data));
    }

}
