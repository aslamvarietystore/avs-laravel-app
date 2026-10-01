<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'quotation_number',
        'issued_on',
        'valid_until',
        'subtotal',
        'status',
        'notes',
    ];

    protected $casts = [
        'issued_on' => 'date',
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
