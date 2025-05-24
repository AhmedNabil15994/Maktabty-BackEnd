<?php

namespace Modules\Catalog\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Traits\HasSlugTranslation;
use Modules\Core\Traits\ScopesTrait;
use Spatie\Translatable\HasTranslations;

class EducationalInstitution extends Model
{
    use HasTranslations, SoftDeletes, ScopesTrait;
    use HasSlugTranslation;

    public $table = 'educational_institutions';
    public $guarded = ['id'];
    public $sluggable = 'title';
    public $translatable = ['title', 'slug'];

    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Get all of the products that are assigned this search keywords.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'educational_institution_id');
    }

}
