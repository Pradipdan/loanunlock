<?php
// database/migrations/2024_01_01_000002_create_otps_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->string('mobile', 15);
            $table->string('otp', 6);
            $table->boolean('is_used')->default(false);
            $table->integer('attempts')->default(0);
            $table->timestamp('expires_at');
            $table->timestamps();
            $table->index('mobile');
        });
    }
    public function down(): void {
        Schema::dropIfExists('otps');
    }
};
