<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyReport extends Model
{
    protected $fillable = [
        'site_id',
        'work_date',
        'remarks',
    ];

    protected $casts = [
        'work_date' => 'date',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(DailyReportDetail::class);
    }
}
