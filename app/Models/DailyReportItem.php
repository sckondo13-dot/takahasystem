<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyReportItem extends Model
{
    protected $fillable = [

        'daily_report_id',

        'category',

        'name',

        'quantity',

        'unit',

        'unit_price',

        'amount',

        'note',

    ];

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }
}
