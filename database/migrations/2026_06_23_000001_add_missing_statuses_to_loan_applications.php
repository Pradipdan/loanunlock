<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE loan_applications MODIFY COLUMN status ENUM(
            'draft',
            'personal_filled',
            'permissions_granted',
            'loan_details_filled',
            'eligibility_checked',
            'payment_pending',
            'payment_done',
            'under_review',
            'approved',
            'rejected',
            'disbursed',
            'closed'
        ) NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE loan_applications MODIFY COLUMN status ENUM(
            'draft',
            'personal_filled',
            'eligibility_checked',
            'payment_pending',
            'payment_done',
            'under_review',
            'approved',
            'rejected',
            'disbursed',
            'closed'
        ) NOT NULL DEFAULT 'draft'");
    }
};
