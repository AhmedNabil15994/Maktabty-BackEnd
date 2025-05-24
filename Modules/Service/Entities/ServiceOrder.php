<?php

namespace Modules\Service\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Traits\ScopesTrait;
use Modules\Service\Entities\Service;

class ServiceOrder extends Model
{
    use SoftDeletes, ScopesTrait;

    protected $guarded = ['id'];
    protected $casts = [
        'contact_info' => 'array',
        'files' => 'array',
    ];

    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    public function user()
    {
        return $this->belongsTo(\Modules\User\Entities\User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

}
