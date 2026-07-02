<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_status')->default('unpaid')->after('payment_method');
            $table->string('payment_proof')->nullable()->after('payment_status');
            $table->timestamp('payment_proof_submitted_at')->nullable()->after('payment_proof');
            $table->string('tracking_status')->default('placed')->after('status');
            $table->string('tracking_number')->nullable()->after('tracking_status');
            $table->text('tracking_note')->nullable()->after('tracking_number');
            $table->text('admin_note')->nullable()->after('tracking_note');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'payment_proof',
                'payment_proof_submitted_at',
                'tracking_status',
                'tracking_number',
                'tracking_note',
                'admin_note',
            ]);
        });
    }
};
