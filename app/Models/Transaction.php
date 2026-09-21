<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'user_id',
        'type', // Inbound, Outbound, Adjustment, PO, Transfer, return
        'ref_number',
        'transaction_date',
        'status', // Draft, Approved, Completed
        'total_amount',
        'payment_method',
        'paid_amount',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function lines()
    {
        return $this->hasMany(TransactionLine::class);
    }
}
