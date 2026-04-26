<?php
// database/migrations/2024_01_01_000003_create_loan_applications_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_id')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('requested_amount', 10, 2)->nullable();
            $table->decimal('approved_amount', 10, 2)->nullable();
            $table->integer('tenure_months')->nullable();
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->decimal('emi_amount', 10, 2)->nullable();
            $table->decimal('processing_fee', 10, 2)->default(299.00);
            $table->enum('status', [
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
            ])->default('draft');
            $table->enum('employment_type', ['salaried', 'business'])->nullable();
            $table->string('loan_purpose')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('disbursed_at')->nullable();
            $table->string('credit_score')->nullable();
            $table->boolean('is_eligible')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('loan_applications');
    }
};
