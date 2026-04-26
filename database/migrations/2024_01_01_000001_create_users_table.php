<?php
// database/migrations/2024_01_01_000001_create_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('mobile', 15)->unique();
            $table->string('email')->nullable()->unique();
            $table->string('pan_number', 10)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('state')->nullable();
            $table->string('preferred_language')->default('ENGLISH');
            $table->enum('employment_type', ['salaried', 'business'])->nullable();
            $table->string('monthly_income')->nullable();
            $table->string('company_name')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_blocked')->default(false);
            $table->boolean('permissions_granted')->default(false);
            $table->string('profile_photo')->nullable();
            $table->string('aadhar_number', 16)->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('pincode', 6)->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_ifsc')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('users');
    }
};
