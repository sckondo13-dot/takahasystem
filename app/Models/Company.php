<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    protected $fillable = [
        'name',
        'postal_code',
        'address',
        'tel',
        'fax',
        'email',
        'registration_number',
        'representative',
        'seal_image',
        'bank_name',
        'branch_name',
        'account_type',
        'account_number',
        'account_name',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
