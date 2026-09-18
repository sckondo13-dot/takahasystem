<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceDetail extends Model
{
    protected $fillable = [
        'invoice_id',
        'site_id',
        'work_type_id',
        'description',
        'quantity',
        'unit',
        'tax_type',
        'unit_price',
        'amount',
        'sort_order',
        'source_type',
        'progress_rate',
        'remaining_rate',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'amount' => 'decimal:2',
        'progress_rate' => 'decimal:2',
        'remaining_rate' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function workType(): BelongsTo
    {
        return $this->belongsTo(WorkType::class);
    }
}
