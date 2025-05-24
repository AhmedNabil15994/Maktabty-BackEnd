<?php

namespace Modules\Service\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Traits\HasSlugTranslation;
use Modules\Core\Traits\ScopesTrait;
use Modules\Service\Entities\ServiceOrder;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasTranslations, SoftDeletes, ScopesTrait;
    use HasSlugTranslation;

    protected $with = [];
    protected $guarded = ['id'];
    public $translatable = [
        'title', "slug",
    ];

    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    public function orders()
    {
        return $this->hasMany(ServiceOrder::class, 'service_id');
    }

    public function categories()
    {
        return $this->belongsToMany(ServiceCategory::class, 'service_category_pivot')->withTimestamps();
    }

    public function subCategories()
    {
        return $this->belongsToMany(ServiceCategory::class, 'service_category_pivot')
            ->whereNotNull('service_categories.service_category_id')->withTimestamps();
    }

    public function parentCategories()
    {
        return $this->belongsToMany(ServiceCategory::class, 'service_category_pivot')
            ->whereNull('service_categories.service_category_id')->withTimestamps();
    }
}
