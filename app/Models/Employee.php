<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'name',
        'hire_date',
        'retirement_date',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'retirement_date' => 'date',
    ];

    public function dailyReportDetails(): HasMany
    {
        return $this->hasMany(DailyReportDetail::class);
    }
    public function allowances()
    {
        return $this->hasMany(EmployeeAllowance::class);
    }
    public function employeeAllowances()
    {
        return $this->hasMany(EmployeeAllowance::class);
    }

    /**
     * 指定日時点で在籍しているか
     */
    public function isEmployedAt($date): bool
    {
        $date = \Carbon\Carbon::parse($date);

        // 雇用日前
        if ($this->hire_date && $this->hire_date->gt($date)) {
            return false;
        }

        // 退職日より後
        if (
            $this->retirement_date &&
            $this->retirement_date->lt($date)
        ) {
            return false;
        }

        return true;
    }
}
