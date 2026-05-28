<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subcontractor extends Model
{
    protected $fillable = [
        'name',
    ];

    public function dailyReportDetails(): HasMany
    {
        return $this->hasMany(DailyReportDetail::class);
    }
}
