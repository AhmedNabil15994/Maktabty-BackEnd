<?php

namespace Modules\Service\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Traits\DataTable;
use Modules\Service\Http\Requests\Dashboard\ServiceRequest;
use Modules\Service\Transformers\Dashboard\ServiceResource;
use Modules\Service\Repositories\Dashboard\ServiceRepository as ServiceRepo;

class ServiceController extends Controller
{
    protected $service;

    function __construct(ServiceRepo $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return view('service::dashboard.services.index');
    }

    public function datatable(Request $request)
    {
        $datatable = DataTable::drawTable($request, $this->service->QueryTable($request));
        $datatable['data'] = ServiceResource::collection($datatable['data']);
        return Response()->json($datatable);
    }

    public function create()
    {
        return view('service::dashboard.services.create');
    }

    public function store(ServiceRequest $request)
    {
        try {
            $create = $this->service->create($request);

            if ($create) {
                return Response()->json([true, __('apps::dashboard.general.message_create_success')]);
            }

            return Response()->json([false, __('apps::dashboard.general.message_error')]);
        } catch (\Exception $e) {
            return Response()->json([false, $e->errorInfo[2]]);
        }
    }

    public function show($id)
    {
        return view('service::dashboard.services.show');
    }

    public function edit($id)
    {
        $service = $this->service->findById($id);
        if (!$service)
            abort(404);
        return view('service::dashboard.services.edit', compact('service'));
    }

    public function clone($id)
    {
        $service = $this->service->findById($id);
        if (!$service)
            abort(404);
        return view('service::dashboard.services.clone', compact('service'));
    }

    public function update(ServiceRequest $request, $id)
    {
        try {
            $update = $this->service->update($request, $id);

            if ($update) {
                return Response()->json([true, __('apps::dashboard.general.message_update_success')]);
            }

            return Response()->json([false, __('apps::dashboard.general.message_error')]);
        } catch (\Exception $e) {
            return Response()->json([false, $e->errorInfo[2]]);
        }
    }

    public function destroy($id)
    {
        try {
            $delete = $this->service->delete($id);

            if ($delete) {
                return Response()->json([true, __('apps::dashboard.general.message_delete_success')]);
            }

            return Response()->json([false, __('apps::dashboard.general.message_error')]);
        } catch (\Exception $e) {
            return Response()->json([false, $e->errorInfo[2]]);
        }
    }

    public function deletes(Request $request)
    {
        try {
            if (empty($request['ids']))
                return Response()->json([false, __('apps::dashboard.general.select_at_least_one_item')]);

            $deleteSelected = $this->service->deleteSelected($request);
            if ($deleteSelected) {
                return Response()->json([true, __('apps::dashboard.general.message_delete_success')]);
            }

            return Response()->json([false, __('apps::dashboard.general.message_error')]);
        } catch (\Exception $e) {
            return Response()->json([false, $e->errorInfo[2]]);
        }
    }
}
