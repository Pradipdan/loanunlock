<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'user_id', 'loan_application_id', 'type',
        'file_path', 'original_name', 'verification_status', 'rejection_reason',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function loanApplication() { return $this->belongsTo(LoanApplication::class); }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
}
