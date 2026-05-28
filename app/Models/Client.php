<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'name',
        'demolition_unit_price',
        'heavy_equipment_unit_price',
        'heavy_equipment2_unit_price',
        'chipping_unit_price',
        'asbestos_unit_price',
        'truck_unit_price',
        'unic_unit_price',
    ];

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    public function monthlySales(): HasMany
    {
        return $this->hasMany(MonthlySale::class);
    }
}
