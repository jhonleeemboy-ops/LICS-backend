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
    Schema::create('lawyer_recommendations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('chat_session_id')->constrained()->cascadeOnDelete();
    $table->foreignId('lawyer_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('legal_category_id')->constrained()->cascadeOnDelete();
    $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lawyer_recommendations');
    }
};
