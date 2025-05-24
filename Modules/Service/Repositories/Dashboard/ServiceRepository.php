<?php

namespace Modules\Service\Repositories\Dashboard;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Modules\Core\Traits\CoreTrait;
use Modules\Core\Traits\SyncRelationModel;
use Modules\Service\Entities\Service;

class ServiceRepository
{
    use SyncRelationModel, CoreTrait;

    protected $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function getAll($order = 'id', $sort = 'desc')
    {
        $services = $this->service->orderBy($order, $sort)->get();
        return $services;
    }

    public function getAllActive($order = 'id', $sort = 'desc')
    {
        $services = $this->service->orderBy($order, $sort)->active()->get();
        return $services;
    }

    public function servicesCount()
    {
        $services = $this->service->active()->count();
        return $services;
    }

    public function findById($id)
    {
        $service = $this->service->withDeleted()->find($id);
        return $service;
    }

    public function findActiveById($id)
    {
        $query = $this->service->active();
        return $query->find($id);
    }

    public function create($request)
    {
        DB::beginTransaction();

        try {
            $data = [
                'status' => $request->status ? 1 : 0,
                "title" => $request->title,
            ];

            if (!is_null($request->image)) {
                $imgName = $this->uploadImage(public_path(config('core.config.service_img_path')), $request->image);
                $data['image'] = config('core.config.service_img_path') . '/' . $imgName;
            } else {
                $data['image'] = null;
            }

            $service = $this->service->create($data);

            $service->categories()->sync((is_array($request->service_category_id) ? $request->service_category_id : int_to_array($request->service_category_id)));

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        $service = $this->findById($id);
        $restore = $request->restore ? $this->restoreSoftDelete($service) : null;

        try {
            $data = [
                'status' => $request->status ? 1 : 0,
                "title" => $request->title,
            ];

            if ($request->image) {
                if (!empty($service->image) && !in_array($service->image, config('core.config.special_images'))) {
                    File::delete($service->image); ### Delete old image
                }
                $imgName = $this->uploadImage(public_path(config('core.config.service_img_path')), $request->image);
                $data['image'] = config('core.config.service_img_path') . '/' . $imgName;
            } else {
                $data['image'] = $service->image;
            }

            $service->update($data);
            $service->categories()->sync((is_array($request->service_category_id) ? $request->service_category_id : int_to_array($request->service_category_id)));

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function restoreSoftDelete($model)
    {
        return $model->restore();
    }

    public function translateTable($model, $request)
    {
        foreach ($request['title'] as $locale => $value) {
            $model->translateOrNew($locale)->title = $value;
        }
        $model->save();
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {
            $exceptions = [];
            $model = $this->findById($id);
            if ($model) {
                if ($model->trashed()) {
                    if (!empty($model->image) && !in_array($model->image, config('core.config.special_images')) && !in_array($model->image, $exceptions)) {
                        File::delete($model->image); ### Delete old image
                    }
                    $model->forceDelete();
                } else {
                    $model->delete();
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function deleteSelected($request)
    {
        DB::beginTransaction();

        try {
            foreach ($request['ids'] as $id) {
                $model = $this->delete($id);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function QueryTable($request)
    {
        $query = $this->service->query();

        $query->where(function ($query) use ($request) {
            $query->where('id', 'like', '%' . $request->input('search.value') . '%');
            $query->orWhere(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->input('search.value') . '%');
            });
        });

        return $this->filterDataTable($query, $request);
    }

    public function filterDataTable($query, $request)
    {
        // Search Categories by Created Dates
        if (isset($request['req']['from']) && $request['req']['from'] != '') {
            $query->whereDate('created_at', '>=', $request['req']['from']);
        }

        if (isset($request['req']['to']) && $request['req']['to'] != '') {
            $query->whereDate('created_at', '<=', $request['req']['to']);
        }

        if (isset($request['req']['deleted']) && $request['req']['deleted'] == 'only') {
            $query->onlyDeleted();
        }

        if (isset($request['req']['deleted']) && $request['req']['deleted'] == 'with') {
            $query->withDeleted();
        }

        if (isset($request['req']['status']) && $request['req']['status'] == '1') {
            $query->active();
        }

        if (isset($request['req']['status']) && $request['req']['status'] == '0') {
            $query->unactive();
        }

        return $query;
    }
}
