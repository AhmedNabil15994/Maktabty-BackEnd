<?php

namespace Modules\Catalog\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Traits\DataTable;
use Modules\Catalog\Http\Requests\Dashboard\EducationalInstitutionRequest;
use Modules\Catalog\Transformers\Dashboard\EducationalInstitutionResource;
use Modules\Catalog\Repositories\Dashboard\EducationalInstitutionRepository as EducationalInstitutionRepo;

class EducationalInstitutionController extends Controller
{
    protected $institution;

    function __construct(EducationalInstitutionRepo $institution)
    {
        $this->institution = $institution;
    }

    public function index()
    {
        return view('catalog::dashboard.educational_institutions.index');
    }

    public function datatable(Request $request)
    {
        $datatable = DataTable::drawTable($request, $this->institution->QueryTable($request));
        $datatable['data'] = EducationalInstitutionResource::collection($datatable['data']);
        return Response()->json($datatable);
    }

    public function create()
    {
        return view('catalog::dashboard.educational_institutions.create');
    }

    public function store(EducationalInstitutionRequest $request)
    {
        try {
            $create = $this->institution->create($request);

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
        return view('catalog::dashboard.educational_institutions.show');
    }

    public function edit($id)
    {
        $institution = $this->institution->findById($id);
        if (!$institution)
            abort(404);
        return view('catalog::dashboard.educational_institutions.edit', compact('institution'));
    }

    public function clone($id)
    {
        $institution = $this->institution->findById($id);
        if (!$institution)
            abort(404);
        return view('catalog::dashboard.educational_institutions.clone', compact('institution'));
    }

    public function update(EducationalInstitutionRequest $request, $id)
    {
        try {
            $update = $this->institution->update($request, $id);

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
            $delete = $this->institution->delete($id);

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

            $deleteSelected = $this->institution->deleteSelected($request);
            if ($deleteSelected) {
                return Response()->json([true, __('apps::dashboard.general.message_delete_success')]);
            }

            return Response()->json([false, __('apps::dashboard.general.message_error')]);
        } catch (\Exception $e) {
            return Response()->json([false, $e->errorInfo[2]]);
        }
    }
}
