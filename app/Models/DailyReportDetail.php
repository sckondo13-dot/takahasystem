<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

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
        'note',
        'attendance_time_name',
        'start_time',
        'end_time',
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

    public function getBreakHoursAttribute()
    {
        $overtime = (float)$this->overtime_hours;

        if ($overtime == 0) {
            return 2;
        }

        if ($overtime <= 2) {
            return 2 - $overtime;
        }

        return 0;
    }

    public function getActualEndTimeAttribute()
    {
        if (!$this->end_time) {
            return null;
        }

        $end = Carbon::parse($this->end_time);

        $extendHours = max(
            0,
            $this->overtime_hours - (2 - $this->break_hours)
        );

        return $end->copy()->addMinutes($extendHours * 60);
    }

    public function getStartTimeFormattedAttribute()
    {
        if (!$this->start_time) {
            return '';
        }

        return Carbon::parse($this->start_time)
            ->format('H:i');
    }

    public function getEndTimeFormattedAttribute()
    {
        if (!$this->actual_end_time) {
            return '';
        }

        return $this->actual_end_time
            ->format('H:i');
    }

    public function getBreakHoursFormattedAttribute()
    {
        return number_format($this->break_hours, 1);
    }
}
