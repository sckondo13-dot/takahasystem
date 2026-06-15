<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allowance extends Model
{
    protected $fillable = [
        'name',
        'type',
        'amount',
        'note',
    ];

    public function employeeAllowances()
    {
        return $this->hasMany(EmployeeAllowance::class);
    }
    public function histories()
    {
        return $this->hasMany(
            AllowanceHistory::class
        );
    }
}
