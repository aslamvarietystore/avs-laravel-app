<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnRecord extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'reason',
        'amount_refunded',
        'returned_on',
        'status',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'amount_refunded' => 'decimal:2',
        'returned_on' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
