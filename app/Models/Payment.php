<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id', 'loan_application_id', 'transaction_id',
        'payment_gateway_id', 'amount', 'method', 'status',
        'gateway_response', 'paid_at',
    ];

    protected $casts = ['paid_at' => 'datetime', 'amount' => 'decimal:2'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->transaction_id) {
                $model->transaction_id = 'TXN' . strtoupper(uniqid());
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loanApplication()
    {
        return $this->belongsTo(LoanApplication::class);
    }
}
