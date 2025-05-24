<?php

namespace Modules\Service\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Traits\Dashboard\DatatableExportTrait;
use Modules\Service\Http\Requests\Dashboard\ImportServiceCategoryRequest;
use Modules\Service\Http\Requests\Dashboard\ServiceCategoryRequest;
use Modules\Service\Http\Requests\Dashboard\UpdatePhotoRequest;
use Modules\Service\Imports\ServiceCategoryImport;
use Modules\Service\Repositories\Dashboard\ServiceCategoryRepository as ServiceCategory;
use Modules\Service\Transformers\Dashboard\ServiceCategoryResource;

class ServiceCategoryController extends Controller
{
    use DatatableExportTrait;

    protected $category;

    public function __construct(ServiceCategory $category)
    {
        $this->category = $category;
        $this->setRepository(ServiceCategory::class);
        $this->setResource(new ServiceCategoryResource([]));
    }

    public function index()
    {
        return view('service::dashboard.service_categories.index');
    }

    public function create()
    {
        return view('service::dashboard.service_categories.create');
    }

    public function store(ServiceCategoryRequest $request)
    {
        try {
            $create = $this->category->create($request);

            if ($create) {
                return Response()->json([true, __('apps::dashboard.general.message_create_success')]);
            }

            return Response()->json([false, __('apps::dashboard.general.message_error')]);
        } catch (\PDOException$e) {
            return Response()->json([false, $e->errorInfo[2]]);
        }
    }

    public function updatePhoto(UpdatePhotoRequest $request)
    {
        try {
            $imagePath = $this->category->updatePhoto($request);

            if ($imagePath) {
                return Response()->json([true, __('apps::dashboard.general.message_create_success'), 'imagePath' => $imagePath]);
            }

            return Response()->json([false, __('apps::dashboard.general.message_error')]);
        } catch (\PDOException$e) {
            throw $e;
            return Response()->json([false, $e->errorInfo[2]]);
        }
    }

    public function Import(ImportServiceCategoryRequest $request)
    {
        try {

            DB::beginTransaction();
            Excel::import(new ServiceCategoryImport($request), $request->file('excel_file'));
            DB::commit();
            return Response()->json([true, __('apps::dashboard.general.message_create_success')]);
        } catch (\PDOException$e) {
            return Response()->json([false, $e->errorInfo[2]]);
        }
    }

    public function show($id)
    {
        return view('service::dashboard.service_categories.show');
    }

    public function edit($id)
    {
        $category = $this->category->findById($id);

        return view('service::dashboard.service_categories.edit', compact('category'));
    }

    public function update(ServiceCategoryRequest $request, $id)
    {
        try {
            $update = $this->category->update($request, $id);

            if ($update) {
                return Response()->json([true, __('apps::dashboard.general.message_update_success')]);
            }

            return Response()->json([false, __('apps::dashboard.general.message_error')]);
        } catch (\PDOException$e) {
            return Response()->json([false, $e->errorInfo[2]]);
        }
    }

    public function destroy($id)
    {
        try {
            $delete = false;
            if ($id != 1) {
                $delete = $this->category->delete($id);
            }

            if ($delete) {
                return Response()->json([true, __('apps::dashboard.general.message_delete_success')]);
            }

            return Response()->json([false, __('apps::dashboard.general.message_error')]);
        } catch (\PDOException$e) {
            return Response()->json([false, $e->errorInfo[2]]);
        }
    }

    public function deletes(Request $request)
    {
        try {
            if (empty($request['ids'])) {
                return Response()->json([false, __('apps::dashboard.general.select_at_least_one_item')]);
            }

            $deleteSelected = $this->category->deleteSelected($request);
            if ($deleteSelected) {
                return Response()->json([true, __('apps::dashboard.general.message_delete_success')]);
            }

            return Response()->json([false, __('apps::dashboard.general.message_error')]);
        } catch (\PDOException$e) {
            return Response()->json([false, $e->errorInfo[2]]);
        }
    }
}
