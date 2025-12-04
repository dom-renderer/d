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
            $table->enum('discount_type', ['PERCENT', 'FIX'])->default('FIX')->after('grand_total');
            $table->double('discount_amount')->default(0)->after('discount_type');
            $table->enum('vat_type', ['PERCENT', 'FIX'])->default('PERCENT')->after('discount_amount');
            $table->double('vat_amount')->default(0)->after('vat_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_amount', 'vat_type', 'vat_amount']);
        });
    }
};
