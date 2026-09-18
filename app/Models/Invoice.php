<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'company_id',
        'client_id',
        'site_id',
        'invoice_no',
        'invoice_type',
        'title',
        'invoice_date',
        'payment_due',
        'subtotal',
        'tax',
        'total',
        'remarks',
        'status',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'payment_due' => 'date',
        'subtotal' => 'integer',
        'tax' => 'integer',
        'total' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(InvoiceDetail::class);
    }

    public function dailyReports(): BelongsToMany
    {
        return $this->belongsToMany(
            DailyReport::class,
            'invoice_daily_reports'
        );
    }
}
