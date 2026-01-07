<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('butuan_ordinances', function (Blueprint $table) {
            $table->id();
            $table->string('ordinance_number');
            $table->string('title');
            $table->foreignId('legal_category_id')
                  ->constrained('legal_categories');
            $table->text('summary');
            $table->string('status')->default('Active');
            $table->string('repealed_by')->nullable();
            $table->string('file_path')->nullable();
            $table->year('year');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('butuan_ordinances');
    }
};
