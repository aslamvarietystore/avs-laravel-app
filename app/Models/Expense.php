<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'category',
        'description',
        'amount',
        'incurred_on',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'incurred_on' => 'date',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'vendor_id');
    }
}
