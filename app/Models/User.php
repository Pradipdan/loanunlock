<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name', 'mobile', 'email', 'pan_number', 'date_of_birth',
        'state', 'preferred_language', 'employment_type', 'monthly_income',
        'company_name', 'is_verified', 'is_blocked', 'permissions_granted',
        'profile_photo', 'aadhar_number', 'address', 'city', 'pincode',
        'bank_account', 'bank_ifsc', 'bank_name', 'fcm_token',
    ];

    protected $hidden = ['remember_token'];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_verified' => 'boolean',
        'is_blocked' => 'boolean',
        'permissions_granted' => 'boolean',
    ];

    public function loanApplications()
    {
        return $this->hasMany(LoanApplication::class);
    }

    public function latestApplication()
    {
        return $this->hasOne(LoanApplication::class)->latestOfMany();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function getFullNameAttribute()
    {
        return $this->name ?? 'User';
    }

    public function getMaskedMobileAttribute()
    {
        return substr($this->mobile, 0, 2) . '******' . substr($this->mobile, -2);
    }
}
