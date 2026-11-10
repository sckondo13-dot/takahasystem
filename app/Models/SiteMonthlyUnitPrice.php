<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteMonthlyUnitPrice extends Model
{
    protected $fillable = [
        'site_id',
        'work_type_id',
        'price_month',
        'unit_price',
    ];

    protected $casts = [
        'price_month' => 'date',
        'unit_price' => 'integer',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function workType(): BelongsTo
    {
        return $this->belongsTo(WorkType::class);
    }
}