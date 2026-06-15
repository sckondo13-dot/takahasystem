<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'name',
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
}
