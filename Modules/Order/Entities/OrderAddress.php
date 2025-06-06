<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Attribute\Entities\AttributeValue;

class OrderAddress extends Model
{
    protected $guarded = ['id'];
    
    protected $casts = [
        'json_data' => 'array',
    ];

    public function state()
    {
        return $this->belongsTo(\Modules\Area\Entities\State::class);
    }

    public function attributes()
    {
        return $this->morphMany(AttributeValue::class,'attributeValuable','order_product_attributes_type','order_product_attributes_id');
    }
}
