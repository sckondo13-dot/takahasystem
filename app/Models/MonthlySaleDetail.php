<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlySaleDetail extends Model
{
    protected $fillable = [
        'monthly_sale_id',
        'site_id',
        'work_type_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    public function monthlySale(): BelongsTo
    {
        return $this->belongsTo(MonthlySale::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function workType(): BelongsTo
    {
        return $this->belongsTo(WorkType::class);
    }
}
