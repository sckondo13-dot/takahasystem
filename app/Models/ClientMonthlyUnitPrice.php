<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientMonthlyUnitPrice extends Model
{
    protected $fillable = [
        'client_id',
        'work_type_id',
        'price_month',
        'unit_price',
    ];

    protected $casts = [
        'price_month' => 'date',
        'unit_price' => 'integer',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function workType(): BelongsTo
    {
        return $this->belongsTo(WorkType::class);
    }
}