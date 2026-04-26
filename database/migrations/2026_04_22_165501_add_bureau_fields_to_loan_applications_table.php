<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->string('bureau_name')->nullable()->after('credit_score');
            $table->json('bureau_raw_response')->nullable()->after('bureau_name');
            $table->string('bureau_check_id')->nullable()->after('bureau_raw_response');
            $table->timestamp('score_fetched_at')->nullable()->after('bureau_check_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropColumn(['bureau_name', 'bureau_raw_response', 'bureau_check_id', 'score_fetched_at']);
        });
    }
};
