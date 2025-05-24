<?php

namespace Modules\Service\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Modules\Advertising\Entities\Advertising;
use Modules\Core\Traits\HasSlugTranslation;
use Modules\Core\Traits\ScopesTrait;
use Modules\Notification\Entities\GeneralNotification;
use Modules\Slider\Entities\Slider;
use Spatie\Translatable\HasTranslations;

class ServiceCategory extends Model
{
    use HasTranslations, SoftDeletes, ScopesTrait;
    use HasSlugTranslation;

    public $table = 'service_categories';
    public $sluggable = 'title';
    protected $guarded = ["id"];
    public $translatable = ['title', 'slug', 'seo_description', 'seo_keywords'];

    public function scopeMainCategories($query)
    {
        return $query->whereNull('service_category_id');
    }

    public function parent()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_category_pivot')->withTimestamps();
    }

    public function activeServices()
    {
        return $this->belongsToMany(Service::class, 'service_category_pivot')->active();
    }

    public function getParentsAttribute()
    {
        $parents = collect([]);

        $parent = $this->parent;

        while (!is_null($parent)) {
            $parents->push($parent);
            $parent = $parent->parent;
        }

        return $parents;
    }

    public function children()
    {
        return $this->hasMany(ServiceCategory::class, 'service_category_id');
    }

    public function frontendChildren()
    {
        return $this->hasMany(ServiceCategory::class, 'service_category_id')->active()->has('services');
    }

    public function dashboardChildren()
    {
        $categories = $this->hasMany(ServiceCategory::class, 'service_category_id')->withCount(['services' => function ($q) {
            $q->active();
        }]);

        if (!is_null(request()->route()) && in_array(request()->route()->getName(), ['api.home', 'frontend.home'])) {
            $categories = $categories->where('show_in_home', 1);
        }

        // Get Child Category Services
        $categories = $categories->with([
            'services' => function ($query) {
                $query->active();
                $query->orderBy('id', 'DESC'); /*->limit(10)*/
            },
        ]);

        return $categories;
    }

    public function childrenRecursive()
    {
        return $this->children()->active()->with('childrenRecursive');
    }

    public function subCategories()
    {
        return $this->hasMany(ServiceCategory::class, 'service_category_id')
            ->has('services')
            ->whereNotNull('service_categories.service_category_id');
    }

    public function getAllRecursiveChildren()
    {
        $category = new Collection();
        foreach ($this->children as $cat) {
            $category->push($cat);
            $category = $category->merge($cat->getAllRecursiveChildren());
        }
        return $category;
    }

    public function adverts()
    {
        return $this->morphMany(Advertising::class, 'advertable');
    }

    public function getShowChildrenAttribute()
    {
        return (count($this->frontendChildren) && $this->open_sub_category == 1);
    }

    public function scopeHasServiceWithAttr($query, $serviceId)
    {
        return $query->whereHas('services', function ($query) use ($serviceId) {
            $query->where('service_id', $serviceId);
        })->orWhereHas('children', function ($query) use ($serviceId) {

            $query->hasServiceWithAttr($serviceId);
        });
    }

    public function generalNotifications()
    {
        return $this->morphMany(GeneralNotification::class, 'notifiable');
    }

    public function sliders()
    {
        return $this->morphMany(Slider::class, 'sliderable');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
