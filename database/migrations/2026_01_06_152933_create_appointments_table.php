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
    Schema::create('appointments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('lawyer_id')->nullable()->constrained('users')->nullOnDelete();
        $table->string('category')->nullable();
        $table->timestamp('schedule_date')->nullable();
        $table->string('client_name')->nullable();
        $table->string('client_phone')->nullable();
        $table->string('client_email')->nullable();
        $table->enum('consultation_type', ['in-person', 'online', 'phone'])->default('in-person');
        $table->text('notes')->nullable();
        $table->string('status')->default('pending');
        $table->timestamp('accepted_at')->nullable();
        $table->timestamp('rejected_at')->nullable();
        $table->timestamp('cancelled_at')->nullable();
        $table->timestamp('completed_at')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
