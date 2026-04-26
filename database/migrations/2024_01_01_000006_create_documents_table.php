<?php
// database/migrations/2024_01_01_000006_create_documents_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('loan_application_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['pan', 'aadhar_front', 'aadhar_back', 'selfie', 'salary_slip', 'bank_statement', 'itr', 'business_proof', 'other']);
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->string('rejection_reason')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('documents');
    }
};
