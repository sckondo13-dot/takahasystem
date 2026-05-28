<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MonthlySale extends Model
{
    protected $fillable = [
        'client_id',
        'year',
        'month',
        'total_sales',
        'confirmed_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(MonthlySaleDetail::class);
    }
}
