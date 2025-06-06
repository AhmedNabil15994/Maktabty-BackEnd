<?php

namespace Modules\Service\Transformers\WebService;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceCategoryResource extends JsonResource
{
    public function toArray($request)
    {
        $response = [
            'id' => $this->id,
            'title' => $this->title,
            'image' => $this->image ? url($this->image) : null,
            'color' => $this->color,
        ];

        if (request()->get('model_flag') == 'tree') {
            $response['sub_categories'] = ServiceCategoryResource::collection($this->childrenRecursive);
        }

        return $response;
    }
}
