<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'payment_due'  => 'date',
        'subtotal'     => 'integer',
        'tax'          => 'integer',
        'total'        => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function details()
    {
        return $this->hasMany(InvoiceDetail::class);
    }

    public function dailyReports()
    {
        return $this->belongsToMany(
            DailyReport::class,
            'invoice_daily_reports'
        );
    }
}
