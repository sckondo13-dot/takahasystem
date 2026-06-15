<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAllowance extends Model
{
    protected $fillable = [
        'employee_id',
        'allowance_id',
        'amount',
        'start_date',
        'end_date',
        'allowance_name',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function allowance()
    {
        return $this->belongsTo(Allowance::class);
    }
}
