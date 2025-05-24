<?php

namespace Modules\Service\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Traits\DataTable;
use Modules\Service\Repositories\Dashboard\ServiceOrderRepository as ServiceOrder;
use Modules\Service\Repositories\Dashboard\ServiceRepository as Service;
use Modules\Service\Transformers\Dashboard\ServiceOrderResource;

class ServiceOrderController extends Controller
{
    protected $order;
    protected $service;

    public function __construct(ServiceOrder $order, Service $service)
    {
        $this->order = $order;
        $this->service = $service;
    }

    public function index()
    {
        return view('service::dashboard.service_orders.index');
    }

    public function datatable(Request $request)
    {
        $datatable = DataTable::drawTable($request, $this->order->queryTable($request));
        $datatable['data'] = ServiceOrderResource::collection($datatable['data']);
        return Response()->json($datatable);
    }

    public function show($id, $flag = null)
    {
        $order = $this->order->findById($id);
        if (!$order) {
            abort(404);
        }

        $this->order->updateUnread($id);
        return view('service::dashboard.service_orders.show', compact('order'));
    }

    public function destroy($id)
    {
        try {
            $delete = $this->order->delete($id);

            if ($delete) {
                return Response()->json([true, __('apps::dashboard.general.message_delete_success')]);
            }

            return Response()->json([false, __('apps::dashboard.general.message_error')]);
        } catch (\PDOException $e) {
            return Response()->json([false, $e->errorInfo[2]]);
        }
    }

    public function deletes(Request $request)
    {
        try {
            if (empty($request['ids'])) {
                return Response()->json([false, __('apps::dashboard.general.select_at_least_one_item')]);
            }

            $deleteSelected = $this->order->deleteSelected($request);
            if ($deleteSelected) {
                return Response()->json([true, __('apps::dashboard.general.message_delete_success')]);
            }

            return Response()->json([false, __('apps::dashboard.general.message_error')]);
        } catch (\PDOException $e) {
            return Response()->json([false, $e->errorInfo[2]]);
        }
    }

}
