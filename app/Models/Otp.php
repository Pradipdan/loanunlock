<?php
// app/Models/Otp.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = ['mobile', 'otp', 'is_used', 'attempts', 'expires_at'];
    protected $casts = ['expires_at' => 'datetime', 'is_used' => 'boolean'];

    public function isExpired(): bool
    {
        return now()->isAfter($this->expires_at);
    }

    public function isValid(string $otp): bool
    {
        return !$this->is_used && !$this->isExpired() && $this->otp === $otp && $this->attempts < 5;
    }
}
