<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('appointments')) {
            return;
        }

        Schema::table('appointments', function (Blueprint $table) {

            if (!Schema::hasColumn('appointments', 'category')) {
                $table->string('category')->nullable()->after('lawyer_id');
            }

            if (!Schema::hasColumn('appointments', 'client_name')) {
                $table->string('client_name')->nullable()->after('schedule_date');
            }

            if (!Schema::hasColumn('appointments', 'client_phone')) {
                $table->string('client_phone')->nullable()->after('client_name');
            }

            if (!Schema::hasColumn('appointments', 'client_email')) {
                $table->string('client_email')->nullable()->after('client_phone');
            }

            if (!Schema::hasColumn('appointments', 'consultation_type')) {
                $table->enum('consultation_type', ['in-person', 'online', 'phone'])
                      ->default('in-person')
                      ->after('client_email');
            }

            if (!Schema::hasColumn('appointments', 'accepted_at')) {
                $table->timestamp('accepted_at')->nullable()->after('status');
            }

            if (!Schema::hasColumn('appointments', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('accepted_at');
            }

            if (!Schema::hasColumn('appointments', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('rejected_at');
            }

            if (!Schema::hasColumn('appointments', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('cancelled_at');
            }

            // Make lawyer_id nullable if needed
            $table->unsignedBigInteger('lawyer_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn([
                'category',
                'client_name',
                'client_phone',
                'client_email',
                'consultation_type',
                'accepted_at',
                'rejected_at',
                'cancelled_at',
                'completed_at',
            ]);
        });
    }
};
