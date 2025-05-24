<?php

namespace Modules\Service\Http\Controllers\WebService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Apps\Http\Controllers\WebService\WebServiceController;
use Modules\Service\Repositories\WebService\ServiceRepository as ServiceRepo;
use Modules\Service\Transformers\WebService\ServiceCategoryResource;
use Modules\Service\Transformers\WebService\ServiceResource;

class ServiceController extends WebServiceController
{
    protected $service;

    public function __construct(ServiceRepo $service)
    {
        $this->service = $service;
    }

    public function getServices(Request $request)
    {
        $items = $this->service->getServices($request);

        if ($request->response_type == 'paginated') {
            return $this->responsePagination(ServiceResource::collection($items));
        } else {
            return $this->response(ServiceResource::collection($items));
        }

    }

    public function getServiceDetails(Request $request, $id): JsonResponse
    {
        $item = $this->service->getServiceDetails($request, $id);
        if ($item) {
            $result = new ServiceResource($item);
            return $this->response($result);
        } else {
            return $this->response(null);
        }

    }

    public function getServiceCategories(Request $request)
    {
        $items = $this->service->getServiceCategories($request);

        if ($request->response_type == 'paginated') {
            return $this->responsePagination(ServiceCategoryResource::collection($items));
        } else {
            return $this->response(ServiceCategoryResource::collection($items));
        }

    }

}
