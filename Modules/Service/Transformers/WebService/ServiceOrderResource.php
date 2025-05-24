<?php

namespace Modules\Service\Transformers\WebService;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Service\Transformers\WebService\ServiceResource;

class ServiceOrderResource extends JsonResource
{
    public function toArray($request)
    {
        $result = [
            'id' => $this->id,
            'created_at' => date('d-m-Y H:i', strtotime($this->created_at)),
            'description' => $this->description,
            'service' => new ServiceResource($this->service),
        ];

        if (!is_null($this->user)) {
            $result['user'] = [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'mobile' => $this->user->mobile,
            ];
        } else {
            $result['user'] = [
                'name' => $this->contact_info['name'] ?? null,
                'email' => $this->contact_info['email'] ?? null,
                'mobile' => $this->contact_info['mobile'] ?? null,
            ];
        }

        $files = [];
        if (!empty($this->files)) {
            foreach ($this->files as $key => $file) {
                $files[] = url($file);
            }
        }

        $result['files'] = $files;
        return $result;
    }
}
