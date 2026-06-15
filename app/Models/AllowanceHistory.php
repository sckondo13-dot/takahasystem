<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllowanceHistory extends Model
{
    protected $fillable = [
        'allowance_id',
        'amount',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function allowance()
    {
        return $this->belongsTo(Allowance::class);
    }
}