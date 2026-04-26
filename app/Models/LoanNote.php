<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanNote extends Model
{
    protected $fillable = ['loan_application_id', 'admin_id', 'note', 'type'];

    public function loanApplication() { return $this->belongsTo(LoanApplication::class); }
    public function admin() { return $this->belongsTo(Admin::class); }
}
