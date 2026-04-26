<?php
// database/migrations/2024_01_01_000007_create_loan_notes_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('loan_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->constrained()->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('admins')->onDelete('cascade');
            $table->text('note');
            $table->enum('type', ['general', 'approval', 'rejection', 'query', 'disbursement'])->default('general');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('loan_notes');
    }
};
