<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashflowEntry extends Model
{
    protected $table = 'cashflow_entries';

    protected $fillable = [
        'entry_date',
        'description',
        'type',
        'amount',
        'category',
        'reference',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'entry_date' => 'date',
    ];
}
