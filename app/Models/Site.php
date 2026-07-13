<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    protected $fillable = [
        'client_id',
        'name',
        'contract_type',
        'status',
        'contract_start',
        'contract_end',
    ];

    protected $casts = [
        'contract_start' => 'date',
        'contract_end'   => 'date',
    ];

    public function getStatusAttribute()
    {
        if (!$this->contract_start) {
            return '未設定';
        }

        $today = now()->startOfMonth();

        if (
            $this->contract_end &&
            $this->contract_end->startOfMonth()->lt($today)
        ) {
            return '終了';
        }

        if ($this->contract_start->startOfMonth()->gt($today)) {
            return '開始前';
        }

        return '進行中';
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            '進行中' => 'bg-green-100 text-green-700',
            '開始前' => 'bg-yellow-100 text-yellow-700',
            '終了'   => 'bg-gray-200 text-gray-700',
            default  => 'bg-red-100 text-red-700',
        };
    }

    public function scopeActiveAt($query, $date)
    {
        $month = \Carbon\Carbon::parse($date)->startOfMonth();

        return $query
            ->whereDate('contract_start', '<=', $month)
            ->where(function ($q) use ($month) {

                $q->whereNull('contract_end')
                    ->orWhereDate('contract_end', '>=', $month);
            });
    }

    public function scopeActiveOn($query, $date)
    {
        return $query
            ->whereDate(
                'contract_start',
                '<=',
                $date->copy()->endOfMonth()
            )
            ->where(function ($q) use ($date) {

                $q->whereNull('contract_end')
                    ->orWhereDate(
                        'contract_end',
                        '>=',
                        $date->copy()->startOfMonth()
                    );
            });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function dailyReports(): HasMany
    {
        return $this->hasMany(DailyReport::class);
    }
}
