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
            $table->enum('services_discount_type', ['PERCENT', 'FIX'])->default('FIX')->after('grand_total');
            $table->double('services_discount_amount')->default(0)->after('services_discount_type');
            $table->enum('services_vat_type', ['PERCENT', 'FIX'])->default('PERCENT')->after('services_discount_amount');
            $table->double('services_vat_amount')->default(0)->after('services_vat_type');
            $table->enum('parts_discount_type', ['PERCENT', 'FIX'])->default('FIX')->after('services_vat_amount');
            $table->double('parts_discount_amount')->default(0)->after('parts_discount_type');
            $table->enum('parts_vat_type', ['PERCENT', 'FIX'])->default('PERCENT')->after('parts_discount_amount');
            $table->double('parts_vat_amount')->default(0)->after('parts_vat_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job', function (Blueprint $table) {
            $table->dropColumn([
                'services_discount_type', 
                'services_discount_amount', 
                'services_vat_type', 
                'services_vat_amount',
                'parts_discount_type', 
                'parts_discount_amount', 
                'parts_vat_type', 
                'parts_vat_amount'
            ]);
        });
    }
};
