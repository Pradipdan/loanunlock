<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL for ENUM updates as SQLite/MySQL handle this differently
        DB::statement("ALTER TABLE payments MODIFY COLUMN method ENUM('upi', 'card', 'netbanking', 'wallet', 'razorpay', 'cosmofeed')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE payments MODIFY COLUMN method ENUM('upi', 'card', 'netbanking', 'wallet')");
    }
};
