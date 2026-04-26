<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoanApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id', 'user_id', 'requested_amount', 'approved_amount',
        'tenure_months', 'interest_rate', 'emi_amount', 'processing_fee',
        'status', 'employment_type', 'loan_purpose', 'rejection_reason',
        'admin_notes', 'reviewed_by', 'reviewed_at', 'disbursed_at',
        'credit_score', 'is_eligible', 'bureau_name', 'bureau_raw_response',
        'bureau_check_id', 'score_fetched_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'disbursed_at' => 'datetime',
        'score_fetched_at' => 'datetime',
        'is_eligible' => 'boolean',
        'requested_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'emi_amount' => 'decimal:2',
        'processing_fee' => 'decimal:2',
        'bureau_raw_response' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->application_id) {
                $model->application_id = 'LU' . strtoupper(uniqid());
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function notes()
    {
        return $this->hasMany(LoanNote::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft' => '<span class="badge bg-secondary">Draft</span>',
            'payment_pending' => '<span class="badge bg-warning text-dark">Payment Pending</span>',
            'payment_done', 'under_review' => '<span class="badge bg-info">Under Review</span>',
            'approved' => '<span class="badge bg-success">Approved</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            'disbursed' => '<span class="badge bg-primary">Disbursed</span>',
            default => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>',
        };
    }

    public function getSuccessfulPaymentAttribute()
    {
        return $this->payments()->where('status', 'success')->first();
    }
}
