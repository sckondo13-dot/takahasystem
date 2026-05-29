<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyReportDetail extends Model
{
    protected $fillable = [
        'daily_report_id',
        'employee_id',
        'subcontractor_id',
        'work_type_id',
        'man_hours',
        'overtime_hours',
        'transportation_cost',
        'expressway_cost',
        'parking_cost',
        'sales',
    ];

    public function dailyReport(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function subcontractor(): BelongsTo
    {
        return $this->belongsTo(Subcontractor::class);
    }

    public function workType(): BelongsTo
    {
        return $this->belongsTo(WorkType::class);
    }
}
