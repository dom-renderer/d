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
        Schema::table('job', function (Blueprint $table) {
            $table->boolean('is_invoice_generated')->default(false);
            $table->string('invoice_number')->nullable();
            $table->dateTime('invoice_generated_at')->nullable();
            $table->unsignedBigInteger('invoice_generated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job', function (Blueprint $table) {
            $table->dropColumn(['is_invoice_generated', 'invoice_number', 'invoice_generated_at', 'invoice_generated_by']);
        });
    }
};
