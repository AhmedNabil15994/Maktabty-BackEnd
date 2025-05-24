<?php

namespace Modules\Service\Repositories\WebService;

use Modules\Service\Entities\Service;
use Modules\Service\Entities\ServiceCategory;
use Modules\Service\Traits\ServiceTrait;

class ServiceRepository
{
    use ServiceTrait;

    protected $service;
    protected $category;

    public function __construct(Service $service, ServiceCategory $category)
    {
        $this->service = $service;
        $this->category = $category;
    }

    public function getServices($request)
    {
        $allCats = $this->getAllSubServiceCategoryIds($request->category_id);
        array_push($allCats, intval($request->category_id));

        $query = $this->service->active();

        if ($request->category_id) {
            $query = $query->whereHas('categories', function ($query) use ($allCats) {
                $query->whereIn('service_category_pivot.service_category_id', $allCats);
            });
        }

        if ($request['search']) {
            $query = $this->search($query, $request);
        }

        if ($request['sort']) {
            $query = $query->when($request['sort'] == 'a_to_z', function ($query) {
                $query->orderBy('title->' . locale(), 'asc');
            })->when($request['sort'] == 'z_to_a', function ($query) {
                $query->orderBy('title->' . locale(), 'desc');
            });
        } else {
            $query->orderBy('sort', 'DESC');
        }

        if ($request->response_type == 'paginated') {
            $query = $query->paginate($request->count ?? 24);
        } else {
            if (!empty($request->count)) {
                $query = $query->take($request->count);
            }

            $query = $query->get();
        }

        return $query;
    }

    public function findById($id)
    {
        $query = $this->service->active();
        return $query->find($id);
    }

    public function getServiceDetails($request, $id)
    {
        return $this->findById($id);
    }

    public function search($model, $request)
    {
        $term = strtolower($request['search']);
        return $model->where(function ($query) use ($term) {
            $query->whereRaw('lower(title) like (?)', ["%{$term}%"]);
        });
    }

    public function getServiceCategories($request)
    {
        $query = $this->category->active()->mainCategories();

        if ($request->show_in_home == 1) {
            $query = $query->where('show_in_home', 1);
        }

        if ($request->model_flag == 'tree') {
            $query = $query->with('childrenRecursive');
        }

        $query = $query->whereHas('services', function ($query) use ($request) {
            $query->active();
        });

        $query = $query->orderBy('sort', 'asc');

        if ($request->response_type == 'paginated') {
            $query = $query->paginate($request->count ?? 24);
        } else {
            if (!empty($request->count)) {
                $query = $query->take($request->count);
            }

            $query = $query->get();
        }

        return $query;
    }

}
